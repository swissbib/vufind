<?php
/**
 * Created by IntelliJ IDEA.
 * User: ballmann
 * Date: 12/4/20
 * Time: 8:29 AM
 */

namespace SwissCollections\RenderConfig;

use SwissCollections\Formatter\FieldFormatterData;
use SwissCollections\RecordDriver\FieldRenderContext;
use SwissCollections\RecordDriver\SolrMarc;

/**
 * Class SequencesEntry
 * @package SwissCollections\RenderConfig
 *
 * Represents a stream of repeating marc subfield sequences of one marc record.
 * A sequence is a defined order of marc subfields.
 */
class SequencesEntry extends CompoundEntry {

    /**
     * @var String[][]
     */
    protected $sequences;

    /**
     * GroupEntry constructor.
     * @param String $groupName
     * @param String $fieldName
     * @param String $subfieldName
     * @param int $marcIndex
     * @param FormatterConfig $formatterConfig
     * @param int $indicator1 set to -1 if not relevant
     * @param int $indicator2 set to -1 if not relevant
     * @param String $condition
     */
    public function __construct($groupName, $fieldName, $subfieldName, $marcIndex,
                                $formatterConfig, $indicator1 = -1, $indicator2 = -1, $condition = "") {
        parent::__construct($groupName, $fieldName, $subfieldName, $marcIndex, $formatterConfig, $indicator1,
            $indicator2, $condition);
        $this->formatterConfig->setRepeatedDefault(TRUE);
        if (empty($this->formatterConfig->formatterNameDefault)) {
            $this->formatterConfig->formatterNameDefault = "inline";
        }
    }

    public function __toString() {
        $seqStr = "[";
        foreach ($this->sequences as $index => $seq) {
            if ($index > 0) {
                $seqStr .= ",";
            }
            $seqStr .= "[" . implode(",", $seq) . "]";
        }
        $seqStr .= "]";
        return "SequencesEntry{" . parent::__toString() . "," . $seqStr . "}";
    }

    /**
     * Calls addElement() for every not already added subfield.
     * @param String[][] $sequences
     */
    public function setSequences($sequences) {
        $this->sequences = $sequences;
    }

    /**
     * @param String $subfieldName
     * @param FieldFormatterData[] $values
     * @return null | FieldFormatterData
     */
    protected function inValues($subfieldName, $values) {
        foreach ($values as $ffd) {
            if ($ffd->renderConfig->labelKey === $subfieldName) {
                return $ffd;
            }
        }
        return null;
    }

    /**
     * Similiar to orderEntries(), but works on FieldFormatterData[] instead of SingleEntry[].
     *
     * @param FieldFormatterData[] $values
     */
    protected function orderValues($values) {
        $newEntries = [];
        $entryOrder = $this->getEntryOrder();
        if (empty($entryOrder)) {
            $newEntries = $values;
        } else {
            $fieldNames = [];
            foreach ($entryOrder as $fieldFormatter) {
                $fieldName = $fieldFormatter->fieldName;
                $fieldNames[] = $fieldName;
                $ffd = $this->inValues($fieldName, $values);
                if ($ffd) {
                    $newEntries[] = $ffd;
                }
            }
            foreach ($values as $v) {
                if (!in_array($v->renderConfig->labelKey, $fieldNames)) {
                    $newEntries[] = $v;
                }
            }
        }
        return $newEntries;
    }

    /**
     * @param array $rawDataArray - contains Objects with keys 'tag' and 'data'
     * @param int $index
     * @param SolrMarc $solrMarc
     * @return FieldFormatterData[]
     */
    public function matchesSubfieldSequence(&$rawDataArray, $index, &$solrMarc) {
        $len = count($rawDataArray);
        $values = [];
        if (empty($this->sequences)) {
            $subfieldName = $rawDataArray[$index]['tag'];
            $text = $rawDataArray[$index]['data'];
            $fieldFormatterData = $this->buildFieldFormatterData($subfieldName, $text, $solrMarc);
            $values[] = $fieldFormatterData;
        } else {
            foreach ($this->sequences as $seq) {
                $pos = $index;
                $values = [];
                foreach ($seq as $subfieldName) {
                    if ($pos >= $len) {
                        continue 2;
                    }
                    if ($rawDataArray[$pos]['tag'] !== $subfieldName) {
                        continue 2;
                    }
                    $text = $rawDataArray[$pos]['data'];
                    $fieldFormatterData = $this->buildFieldFormatterData($subfieldName, $text, $solrMarc);
                    $values[] = $fieldFormatterData;
                    $pos++;
                }
                break;
            }
        }
        // $this->elements are already ordered, but the $values are built from "sequences:" which
        // has to use its own sorting! so, re-sort $values too ...
        return $this->orderValues($values);
    }

    /**
     * Add not specified subfields from sequences.
     */
    public function addSubfieldsFromSequences(): void {
        foreach ($this->sequences as $seq) {
            foreach ($seq as $marcSubfieldName) {
                if (!$this->knowsSubfield($marcSubfieldName)) {
                    $this->addElement($this->labelKey . "-" . $marcSubfieldName, $marcSubfieldName);
                }
            }
        }
    }

    /**
     * @param \File_MARC_Control_Field|\File_MARC $field
     * @param FieldRenderContext $context
     */
    public function render(&$field, &$context) {
        if ($context->solrMarc->checkIndicators($field, $this->indicator1, $this->indicator2)) {
            $rawData = $context->solrMarc->getMarcSubfieldsRaw($this->marcIndex);
            foreach ($rawData as $entry) {
                $entryLen = count($entry);
                $index = 0;
                while ($index < $entryLen) {
                    $matchedValues = $this->matchesSubfieldSequence($entry, $index, $context->solrMarc);
                    if (!empty($matchedValues)) {
                        $this->renderImpl($matchedValues, $context);
                        $index += count($matchedValues);
                    } else {
                        $index++;
                    }
                }
            }
        }
    }

}