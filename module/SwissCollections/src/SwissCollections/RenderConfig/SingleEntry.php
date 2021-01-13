<?php
/**
 * Created by IntelliJ IDEA.
 * User: ballmann
 * Date: 12/4/20
 * Time: 8:28 AM
 */

namespace SwissCollections\RenderConfig;

use SwissCollections\Formatter\FieldFormatterData;
use SwissCollections\RecordDriver\FieldRenderContext;
use SwissCollections\RecordDriver\SolrMarc;

class SingleEntry extends AbstractRenderConfigEntry {
    protected $marcSubfieldName;

    /**
     * SingleEntry constructor.
     * @param String $fieldName
     * @param String $subfieldName
     * @param String $labelKey
     * @param int $marcIndex
     * @param FormatterConfig $formatterConfig
     * @param String $marcSubfieldName
     * @param int $indicator1 set to -1 if not relevant
     * @param int $indicator2 set to -1 if not relevant
     * @param String $condition
     */
    public function __construct($fieldName, $subfieldName, $labelKey, $marcIndex, $formatterConfig, $marcSubfieldName = null,
                                $indicator1 = -1, $indicator2 = -1, $condition = "") {
        parent::__construct($fieldName, $subfieldName, $labelKey, $marcIndex, $formatterConfig, $indicator1,
            $indicator2, $condition);
        $this->marcSubfieldName = $marcSubfieldName;
        if (empty($this->formatterConfig->formatterNameDefault)) {
            $this->formatterConfig->formatterNameDefault = "simple";
        }
    }

    /**
     * Returns empty array if no subfield name is set.
     * @return array
     */
    public function buildMap() {
        $result = [];
        if (!empty($this->marcSubfieldName)) {
            $result[$this->marcSubfieldName] = "value";
        }
        return $result;
    }

    public function __toString() {
        return "SingleEntry{" . parent::__toString() . "," . $this->marcSubfieldName . "}";
    }

    public function getMarcSubfieldName(): String {
        return $this->marcSubfieldName;
    }

    /**
     * @param \File_MARC_Control_Field|\File_MARC_Field $field
     * @param FieldRenderContext $context
     * @return FieldFormatterData[]
     */
    public function getAllRenderData(&$field, &$context): array {
        $values = [];
        $renderFieldData = $context->solrMarc->getRenderFieldData($field, $this);
        if (!empty($renderFieldData) && !$renderFieldData->emptyValue()) {
            $values = [new FieldFormatterData($this, $renderFieldData)];
        }
        return $values;
    }

    /**
     * @param \File_MARC_Control_Field|\File_MARC_Field $field
     * @param SolrMarc $solrMarc
     * @return bool
     */
    public function hasRenderData(&$field, $solrMarc): bool {
        if (empty($this->marcSubfieldName)) {
            return $solrMarc->checkIndicators($field, $this->indicator1, $this->indicator2);
        } else {
            $renderFieldData = $solrMarc->getRenderFieldData($field, $this);
            return !empty($renderFieldData) && !$renderFieldData->emptyValue();
        }
    }

    /**
     * @param String $lookupKey
     * @param FieldFormatterData[] $values
     * @param FieldRenderContext $context
     */
    public function applyFormatter($lookupKey, &$values, $context) {
        $renderMode = $this->getRenderMode();
        $context->applySubfieldFormatter($lookupKey, $values[0], $renderMode, $this->labelKey, $context);
    }
}