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
 * Class CompoundEntry
 * @package SwissCollections\RenderConfig
 *
 * Represents several non repeating marc subfields of one marc field.
 */
class CompoundEntry extends AbstractRenderConfigEntry {

    /**
     * @var SingleEntry[]
     */
    public $elements = [];

    /**
     * GroupEntry constructor.
     * @param String $fieldName
     * @param String $subfieldName
     * @param String $labelKey
     * @param int $marcIndex
     * @param FormatterConfig $formatterConfig from "detail-view-field-structure.yaml"
     * @param int $indicator1 set to -1 if not relevant
     * @param int $indicator2 set to -1 if not relevant
     */
    public function __construct(String $fieldName, String $subfieldName, String $labelKey, int $marcIndex,
                                $formatterConfig, int $indicator1 = -1, int $indicator2 = -1) {
        parent::__construct($fieldName, $subfieldName, $labelKey, $marcIndex, $formatterConfig, $indicator1, $indicator2);
        if (empty($this->formatterConfig->formatterNameDefault)) {
            $this->formatterConfig->formatterNameDefault = "line";
        }
    }

    /**
     * @param String $labelKey
     * @param String $marcSubfieldName - a marc subfield name (e.g. 'a')
     */
    public function addElement(String $labelKey, String $marcSubfieldName) {
        $formatter = $this->formatterConfig->singleFormatter($labelKey, "simple", $this->formatterConfig);
        $singleEntry = new SingleEntry($this->fieldName, $this->subfieldName, $labelKey, $this->marcIndex,
            $formatter, $marcSubfieldName, $this->indicator1, $this->indicator2);
        $singleEntry->fieldGroupFormatter = $this->fieldGroupFormatter;
        array_push($this->elements, $singleEntry);
    }

    /**
     * Uses "simple" as default marc subfield formatter.
     * @return FieldFormatterConfig[]
     */
    public function getEntryOrder() {
        return $this->formatterConfig->getEntryOrder("simple");
    }


    public function __toString() {
        $s = "CompoundEntry{" . parent::__toString() . ",[\n";
        foreach ($this->elements as $e) {
            $s = $s . "\t\t\t" . $e . ",\n";
        }
        return $s . "]}";
    }

    public function get(String $name) {
        foreach ($this->elements as $element) {
            if ($name === $element->labelKey) {
                return $element;
            }
        }
        return null;
    }

    public function orderEntries() {
        $newEntries = [];
        $entryOrder = $this->getEntryOrder();
        $fieldNames = [];
        foreach ($entryOrder as $fieldFormatter) {
            $fieldName = $fieldFormatter->fieldName;
            $fieldNames[] = $fieldName;
            $e = $this->get($fieldName);
            if ($e) {
                $newEntries[] = $e;
            }
        }
        foreach ($this->elements as $element) {
            if (!in_array($element->labelKey, $fieldNames)) {
                $newEntries[] = $element;
            }
        }
        $this->elements = $newEntries;
    }

    /**
     * @param \File_MARC_Control_Field|\File_MARC_Field $field
     * @param FieldRenderContext $context
     * @return FieldFormatterData[]
     */
    public function getAllRenderData(&$field, &$context): array {
        /**
         * @var FieldFormatterData[]
         */
        $values = [];
        foreach ($this->elements as $elem) {
            $renderFieldData = $context->solrMarc->getRenderFieldData($field, $elem);
            if (!empty($renderFieldData) && !$renderFieldData->emptyValue()) {
                $values[] = new FieldFormatterData($elem, $renderFieldData);
            }
        }
        return $values;
    }

    public function knowsSubfield($name): bool {
        return $this->findSubfield($name) !== null;
    }

    /**
     * @param $name
     * @return null | SingleEntry
     */
    protected function findSubfield($name) {
        foreach ($this->elements as $element) {
            if ($name === $element->getMarcSubfieldName()) {
                return $element;
            }
        }
        return null;
    }

    /**
     * @param String $subfieldName
     * @param String $text
     * @param SolrMarc $solrMarc
     * @return FieldFormatterData
     */
    public function buildFieldFormatterData($subfieldName, $text, &$solrMarc) {
        $renderConfigEntry = $this->findSubfield($subfieldName);
        if ($renderConfigEntry === null) {
            throw new \Exception("Didn't find $subfieldName in " . $this);
        }
        $renderFieldData = $solrMarc->buildGenericSubMap($text, TRUE);
        return new FieldFormatterData($renderConfigEntry, $renderFieldData);
    }

    /**
     * Create copy without elements.
     * @return CompoundEntry
     */
    public function flatCloneEntry() {
        return new CompoundEntry($this->fieldName, $this->subfieldName, $this->labelKey, $this->marcIndex,
            $this->formatterConfig, $this->indicator1, $this->indicator2);
    }
}