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

class SingleEntry extends AbstractRenderConfigEntry {
    protected $subfieldName;

    /**
     * SingleEntry constructor.
     * @param String $fieldName
     * @param String $labelKey
     * @param int $marcIndex
     * @param FormatterConfig $formatterConfig
     * @param String $subfieldName
     * @param int $indicator1 set to -1 if not relevant
     * @param int $indicator2 set to -1 if not relevant
     */
    public function __construct(String $fieldName, $labelKey, $marcIndex, $formatterConfig, $subfieldName = null,
                                $indicator1 = -1, $indicator2 = -1) {
        parent::__construct($fieldName, $labelKey, $marcIndex, $formatterConfig, $indicator1, $indicator2);
        $this->subfieldName = $subfieldName;
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
        if (!empty($this->subfieldName)) {
            $result[$this->subfieldName] = "value";
        }
        return $result;
    }

    public function __toString() {
        return "SingleEntry{" . parent::__toString() . "," . $this->subfieldName . "}";
    }

    public function getSubfieldName(): String {
        return $this->subfieldName;
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
     * @param String $lookupKey
     * @param FieldFormatterData[] $values
     * @param FieldRenderContext $context
     */
    public function applyFormatter($lookupKey, &$values, $context) {
        $context->applySubfieldFormatter($lookupKey, $values[0], $this->getRenderMode(), $this->labelKey, $context);
    }
}