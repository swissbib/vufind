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
use SwissCollections\RecordDriver\SolrMarc;

abstract class AbstractRenderConfigEntry {
    /**
     * @var String
     */
    public $fieldName;
    /**
     * @var String
     */
    public $subfieldName;
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
     * @var String
     */
    public $subfieldCondition;

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
     * @param String $fieldName
     * @param String $subfieldName
     * @param String $labelKey
     * @param int $marcIndex
     * @param FormatterConfig $formatterConfig from "detail-view-field-structure.yaml"
     * @param int $indicator1
     * @param int $indicator2
     * @param String $condition
     */
    public function __construct(String $fieldName, String $subfieldName, String $labelKey, int $marcIndex,
                                $formatterConfig, int $indicator1, int $indicator2, $condition) {
        $this->fieldName = $fieldName;
        $this->subfieldName = $subfieldName;
        $this->labelKey = $labelKey;
        $this->marcIndex = $marcIndex;
        $this->indicator1 = $indicator1;
        $this->indicator2 = $indicator2;
        $this->formatterConfig = $formatterConfig;
        $this->subfieldCondition = $condition;
    }

    public function getRenderMode(): String {
        return $this->formatterConfig->getFormatterName();
    }

    public function __toString() {
        return "AbstractRenderConfigEntry{"
            . $this->labelKey . ","
            . $this->fieldName . ","
            . $this->subfieldName . ","
            . $this->marcIndex . "," . $this->indicator1 . "," . $this->indicator2 . ","
            . $this->formatterConfig . ","
            . $this->fieldGroupFormatter . "}";
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
     * Not used in this class.
     *
     * @param \File_MARC_Control_Field|\File_MARC_Field $field
     * @param SolrMarc $solrMarc
     * @return bool
     */
    public function hasRenderData(&$field, $solrMarc): bool {
        return true;
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
            $this->applyFormatter($lookupKey, $values, $context);
            if ($this->formatterConfig->isRepeated()) {
                echo $this->formatterConfig->listEndHml;
            }
        } else {
            if (count($values) > 0 && $context->alreadyProcessed($lookupKey)) {
                echo "<!-- DEDUP: " . print_r($values, true) . " -->\n";
            }
        }
    }

    /**
     * @param String $lookupKey
     * @param FieldFormatterData[] $values
     * @param FieldRenderContext $context
     */
    public function applyFormatter($lookupKey, &$values, $context) {
        $context->applyFieldFormatter($lookupKey, $values, $this->getRenderMode(), $this->labelKey, $context);
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