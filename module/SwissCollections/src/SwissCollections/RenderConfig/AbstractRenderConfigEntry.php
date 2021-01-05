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
     * @var FormatterConfig
     */
    protected $formatterConfig;

    /**
     * @var FormatterConfig
     */
    protected $fieldGroupFormatter;

    public static $UNKNOWN_INDICATOR = -1;

    /**
     * AbstractRenderConfigEntry constructor.
     * @param String $labelKey
     * @param int $marcIndex
     * @param FormatterConfig $formatterConfig from "detail-view-field-structure.yaml"
     * @param int $indicator1
     * @param int $indicator2
     */
    public function __construct(String $labelKey, int $marcIndex, $formatterConfig, int $indicator1, int $indicator2) {
        $this->labelKey = $labelKey;
        $this->marcIndex = $marcIndex;
        $this->indicator1 = $indicator1;
        $this->indicator2 = $indicator2;
        $this->formatterConfig = $formatterConfig;
    }

    public function getRenderMode(): String {
        return $this->formatterConfig->getFormatterName();
    }

    public function __toString() {
        return "RenderConfigEntry{" . $this->labelKey . ","
            . $this->marcIndex . "," . $this->indicator1 . "," . $this->indicator2 . "," . $this->formatterConfig
            . "," . $this->fieldGroupFormatter . "}";
    }

    public function orderEntries() {
        // NOP
    }

    /**
     * Not used in this class.
     *
     * @param \File_MARC_Control_Field|\File_MARC_Field $field
     * @param FieldRenderContext $context
     * @return FieldFormatterData[]
     */
    public function getAllRenderData(&$field, &$context): array {
        return [];
    }

    /**
     * @param FieldFormatterData[] $fieldFormatterDataList
     * @return string
     */
    public function calculateRenderDataLookupKey($fieldFormatterDataList): string {
        $key = "";
        foreach ($fieldFormatterDataList as $ffd) {
            $key = $key . "{}" . $ffd->subfieldRenderData->asLookupKey();
        }
        return $key;
    }

    public function setListHtml(String $start, String $end): void {
        $this->formatterConfig->setListHtml($start, $end);
    }

    /**
     * @param FieldFormatterData[] $values
     * @param FieldRenderContext $context
     */
    public function renderImpl(&$values, &$context) {
        $lookupKey = $this->calculateRenderDataLookupKey($values);
        if (count($values) > 0 && !$context->alreadyProcessed($lookupKey)) {
            if ($this->formatterConfig->isRepeated()) {
                echo $this->formatterConfig->listStartHml;
            }
            $context->applyFieldFormatter($lookupKey, $values, $this->getRenderMode(), $this->labelKey);
            if ($this->formatterConfig->isRepeated()) {
                echo $this->formatterConfig->listEndHml;
            }
        }
    }

    /**
     * @param \File_MARC_Control_Field|\File_MARC $field
     * @param FieldRenderContext $context
     */
    public function render(&$field, &$context) {
        $values = $this->getAllRenderData($field, $context);
        $this->renderImpl($values, $context);
    }

    /**
     * Returns an object of config options from detail-view-field-structure.yaml for this
     * marc field.
     * @return FormatterConfig
     */
    public function getFormatterConfig() {
        return $this->formatterConfig;
    }

    /**
     * @return FormatterConfig|null
     */
    public function getFieldGroupFormatter() {
        return $this->fieldGroupFormatter;
    }

    /**
     * @param FormatterConfig|null $fieldGroupFormatter
     */
    public function setFieldGroupFormatter($fieldGroupFormatter): void {
        // keep default formatter object
        if ($fieldGroupFormatter !== null) {
            $this->fieldGroupFormatter = $fieldGroupFormatter;
        }
    }
}