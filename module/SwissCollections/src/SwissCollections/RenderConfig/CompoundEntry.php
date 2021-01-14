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
use SwissCollections\RecordDriver\SubfieldRenderData;

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
     * @param String $groupName
     * @param String $fieldName
     * @param String $subfieldName
     * @param int $marcIndex
     * @param FormatterConfig $formatterConfig from "detail-view-field-structure.yaml"
     * @param int $indicator1 set to -1 if not relevant
     * @param int $indicator2 set to -1 if not relevant
     * @param String $condition
     */
    public function __construct($groupName, $fieldName, $subfieldName, $marcIndex,
                                $formatterConfig, $indicator1 = -1, $indicator2 = -1, $condition = "") {
        parent::__construct($groupName, $fieldName, $subfieldName, $marcIndex, $formatterConfig, $indicator1,
            $indicator2, $condition);
        if (empty($this->formatterConfig->formatterNameDefault)) {
            $this->formatterConfig->formatterNameDefault = "line";
        }
    }

    /**
     * @param String $labelKey
     * @param String $marcSubfieldName - a marc subfield name (e.g. 'a')
     */
    public function addElement(String $labelKey, String $marcSubfieldName) {
        $singleEntry = $this->buildElement($labelKey, $marcSubfieldName);
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
        // if no subfields are specified, get all
        if (empty($this->elements)) {
            $fieldValueMap = $context->solrMarc->getMarcFieldRawMap($field, $this->indicator1, $this->indicator2);
            $ind1 = AbstractRenderConfigEntry::$UNKNOWN_INDICATOR;
            $ind2 = AbstractRenderConfigEntry::$UNKNOWN_INDICATOR;
            if ($field instanceof \File_MARC_Data_Field) {
                $ind1 = $context->solrMarc->normalizeIndicator($field->getIndicator(1));
                $ind2 = $context->solrMarc->normalizeIndicator($field->getIndicator(2));
            }
            foreach ($fieldValueMap as $marcSubfieldName => $value) {
                $elem = $this->buildElement($this->labelKey . "." . $marcSubfieldName, $marcSubfieldName);
                $renderFieldData = new SubfieldRenderData($value, true, $ind1, $ind2);
                $values[] = new FieldFormatterData($elem, $renderFieldData);
            }
        } else {
            // get only values for the specified fields
            foreach ($this->elements as $elem) {
                $renderFieldData = $context->solrMarc->getRenderFieldData($field, $elem);
                if (!empty($renderFieldData) && !$renderFieldData->emptyValue()) {
                    $values[] = new FieldFormatterData($elem, $renderFieldData);
                }
            }
        }
        return $values;
    }

    /**
     *
     * @param \File_MARC_Control_Field|\File_MARC_Field $field
     * @param SolrMarc $solrMarc
     * @return bool
     */
    public function hasRenderData(&$field, $solrMarc): bool {
        // all values matching the required indicators are shown if no subfields are specified
        if (empty($this->elements)) {
            $rawData = $solrMarc->getMarcFieldRawMap($field, $this->indicator1, $this->indicator2);
            return !empty($rawData);
        } else {
            // show only the specified subfields
            foreach ($this->elements as $elem) {
                if ($elem->hasRenderData($field, $solrMarc)) {
                    return true;
                }
            }
            return false;
        }
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
        return new CompoundEntry($this->groupName, $this->fieldName, $this->subfieldName, $this->marcIndex,
            $this->formatterConfig, $this->indicator1, $this->indicator2, $this->subfieldCondition);
    }

    /**
     * @param String $labelKey
     * @param String $marcSubfieldName
     * @return SingleEntry
     */
    protected function buildElement(String $labelKey, String $marcSubfieldName): SingleEntry {
        $formatter = $this->formatterConfig->singleFormatter($labelKey, "simple", $this->formatterConfig);
        $singleEntry = new SingleEntry($this->groupName, $this->fieldName, $this->subfieldName, $this->marcIndex,
            $formatter, $marcSubfieldName, $this->indicator1, $this->indicator2, $this->subfieldCondition);
        $singleEntry->fieldGroupFormatter = $this->fieldGroupFormatter;
        return $singleEntry;
    }
}