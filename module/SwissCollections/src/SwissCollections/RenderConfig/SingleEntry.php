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
     * @param String $labelKey
     * @param int $marcIndex
     * @param String $subfieldName
     * @param int $indicator1 set to -1 if not relevant
     * @param int $indicator2 set to -1 if not relevant
     */
    public function __construct(String $labelKey, int $marcIndex, String $subfieldName = null,
                                int $indicator1 = -1, int $indicator2 = -1) {
        parent::__construct($labelKey, $marcIndex, $indicator1, $indicator2);
        $this->subfieldName = $subfieldName;
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
     * @return array with two elements: FieldFormatterData[] and lookup key
     */
    public function getAllRenderData(&$field, &$context): array {
        $values = [];
        $lookupKey = "";
        $renderFieldData = $context->solrMarc->getRenderFieldData($field, $this);
        if (!empty($renderFieldData) && !$renderFieldData->emptyValue()) {
            $lookupKey = $renderFieldData->asLookupKey();
            $values = [new FieldFormatterData($this, $renderFieldData)];
        }
        return array($values, $lookupKey);
    }
}