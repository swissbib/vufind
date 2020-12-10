<?php
/**
 * Created by IntelliJ IDEA.
 * User: ballmann
 * Date: 12/4/20
 * Time: 8:29 AM
 */

namespace SwissCollections\RenderConfig;

class SequencesEntry extends CompoundEntry {

    /**
     * @var String[][]
     */
    protected $sequences;

    /**
     * GroupEntry constructor.
     * @param String $labelKey
     * @param int $marcIndex
     * @param int $indicator1 set to -1 if not relevant
     * @param int $indicator2 set to -1 if not relevant
     */
    public function __construct(String $labelKey, int $marcIndex, int $indicator1 = -1, int $indicator2 = -1) {
        parent::__construct($labelKey, $marcIndex, $indicator1, $indicator2);
        $this->setInlineRenderMode();
        $this->repeated = TRUE;
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

    public function knowsSubfield($name): bool {
        foreach ($this->elements as $element) {
            if ($name === $element->getSubfieldName()) {
                return TRUE;
            }
        }
        return FALSE;
    }

    /**
     * Calls addElement() for every not already added subfield.
     * @param String[][] $sequences
     */
    public function setSequences($sequences) {
        $this->sequences = $sequences;
    }

    /**
     * @param array $rawDataArray - contains Objects with keys 'tag' and 'data'
     * @param $index
     * @return array|null - values of the matched subfield sequence (sublist of $rawDataArray)
     */
    public function matchesSubfieldSequence(&$rawDataArray, $index) {
        $len = count($rawDataArray);
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
                $values[] = $rawDataArray[$pos];
                $pos++;
            }
            return $values;
        }
        return null;
    }

    /**
     * @param SingleEntry $entry
     * @param array $rawDataArray - contains Objects with keys 'tag' and 'data'
     * @return null|String
     */
    public function valueForSubfield(SingleEntry $entry, $rawDataArray) {
        foreach ($rawDataArray as $data) {
            if ($data['tag'] === $entry->getSubfieldName()) {
                return $data['data'];
            }
        }
        return null;
    }

    public function orderEntries() {
        // TODO
//        $newEntries = [];
//        foreach ($entryOrder as $fieldName) {
//            $e = $this->get($fieldName);
//            if ($e) {
//                $newEntries[] = $e;
//            }
//        }
//        foreach ($this->elements as $element) {
//            if (!in_array($element->labelKey, $entryOrder)) {
//                $newEntries[] = $element;
//            }
//        }
//        $this->elements = $newEntries;
    }

    public function addSubfieldsFromSequences(): void {
        foreach ($this->sequences as $seq) {
            foreach ($seq as $subfieldName) {
                if (!$this->knowsSubfield($subfieldName)) {
                    $this->addElement($this->labelKey, $subfieldName);
                }
            }
        }
    }
}