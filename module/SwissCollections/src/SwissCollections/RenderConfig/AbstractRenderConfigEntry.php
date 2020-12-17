<?php
/**
 * Created by IntelliJ IDEA.
 * User: ballmann
 * Date: 12/4/20
 * Time: 8:24 AM
 */

namespace SwissCollections\RenderConfig;

use SwissCollections\Formatter\FieldFormatterData;
use SwissCollections\RecordDriver\FieldRenderContext;

abstract class AbstractRenderConfigEntry {
    /**
     * @var String
     */
    public $labelKey;
    /**
     * @var int
     */
    public $marcIndex;
    /**
     * @var int
     */
    public $indicator1;
    /**
     * @var int
     */
    public $indicator2;

    /**
     * @var bool
     */
    public $repeated;

    /**
     * @var mixed | null
     */
    protected $fieldViewInfo;

    /**
     * @var String $renderMode is a FieldFormatter's name
     */
    protected $renderMode;

    /**
     * @var String
     */
    protected $listStartHml;

    /**
     * @var String
     */
    protected $listEndHml;

    public static $UNKNOWN_INDICATOR = -1;

    public function __construct(String $labelKey, int $marcIndex, int $indicator1, int $indicator2) {
        $this->labelKey = $labelKey;
        $this->marcIndex = $marcIndex;
        $this->indicator1 = $indicator1;
        $this->indicator2 = $indicator2;
        $this->repeated = false;
    }

    public function setRenderMode(String $name) {
        $this->renderMode = $name;
    }

    public function getRenderMode(): String {
        return $this->renderMode;
    }

    public function __toString() {
        return "RenderConfigEntry{" . $this->labelKey . ","
            . $this->marcIndex . "," . $this->indicator1 . "," . $this->indicator2
            . "," . $this->repeated . "," . $this->renderMode . "}";
    }

    public function orderEntries() {
        // NOP
    }

    /**
     * Not used in this class.
     *
     * @param \File_MARC_Control_Field|\File_MARC_Field $field
     * @param FieldRenderContext $context
     * @return array of 2 elements: FieldFormatterData[] and lookup key (String)
     */
    public function getAllRenderData(&$field, &$context): array {
        return array([], "");
    }

    public function setListHtml(String $start, String $end): void {
        $this->listStartHml = $start;
        $this->listEndHml = $end;
    }

    /**
     * @param FieldFormatterData[] $values
     * @param String $lookupKey
     * @param FieldRenderContext $context
     */
    protected function renderImpl(&$values, $lookupKey, &$context) {
        if (count($values) > 0 && !$context->alreadyProcessed($lookupKey)) {
            if ($this->repeated) {
                echo $this->listStartHml;
            }
            $context->applyFieldFormatter($lookupKey, $values, $this->renderMode, $this->labelKey);
            if ($this->repeated) {
                echo $this->listEndHml;
            }
        }
    }

    /**
     * @param \File_MARC_Control_Field|\File_MARC $field
     * @param FieldRenderContext $context
     */
    public function render(&$field, &$context) {
        list($values, $lookupKey) = $this->getAllRenderData($field, $context);
        $this->renderImpl($values, $lookupKey, $context);
    }

    /**
     * Raw field view info for this marc field from "detail-view-field-structure.yaml".
     * @param mixed|null $fieldViewInfo
     */
    public function setFieldViewInfo($fieldViewInfo) {
        $this->fieldViewInfo = $fieldViewInfo;
    }

    /**
     * Returns an associative array of config options from detail-view-field-structure.yaml for this
     * marc field.
     * @return mixed|null
     */
    public function getFieldViewInfo() {
        return $this->fieldViewInfo;
    }
}