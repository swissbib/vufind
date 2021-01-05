<?php

namespace SwissCollections\Formatter;

use Laminas\View\Renderer\PhpRenderer;
use SwissCollections\RecordDriver\FieldRenderContext;
use SwissCollections\RecordDriver\SubfieldRenderData;
use SwissCollections\RenderConfig\SingleEntry;

class FieldFormatterData {
    /**
     * @var SingleEntry
     */
    public $renderConfig;

    /**
     * @var SubfieldRenderData
     */
    public $subfieldRenderData;

    /**
     * FieldFormatterData constructor.
     * @param SingleEntry $renderConfig
     * @param SubfieldRenderData $subfieldRenderData
     */
    public function __construct(SingleEntry $renderConfig, SubfieldRenderData $subfieldRenderData) {
        $this->renderConfig = $renderConfig;
        $this->subfieldRenderData = $subfieldRenderData;
    }

    public function __toString() {
        return "FieldFormatterData{" . $this->renderConfig . "," . $this->subfieldRenderData . "}";
    }
}

abstract class FieldFormatter {

    /**
     * @var PhpRenderer
     */
    protected $phpRenderer;

    /**
     * FieldFormatter constructor.
     * @param PhpRenderer $phpRenderer
     */
    public function __construct(PhpRenderer $phpRenderer) {
        $this->phpRenderer = $phpRenderer;
    }

    /**
     * @param String $fieldName
     * @param FieldFormatterData[] $fieldDataList
     * @param FieldRenderContext $context ;
     */
    public abstract function render($fieldName, $fieldDataList, $context): void;

    public abstract function getName(): String;

    public function outputValue(FieldFormatterData $fd, FieldRenderContext $context): void {
        $formatterConfig = $fd->renderConfig->getFormatterConfig();
        // TODO is "null" OK?
        $context->applySubfieldFormatter(null, $fd, $formatterConfig->getFormatterName(), $fd->renderConfig->labelKey, $context);
    }
}

class FieldFormatterRegistry {
    /**
     * @var array<String,FieldFormatter>
     */
    protected $registry;

    public function register(FieldFormatter $ff) {
        $this->registry[$ff->getName()] = $ff;
    }

    /**
     * @param String $name
     * @return null|FieldFormatter
     */
    public function get(String $name) {
        return $this->registry[$name];
    }

    /**
     * @param String $formatterKey
     * @param String $fieldName
     * @param FieldFormatterData[] $data
     * @param FieldRenderContext $context ;
     */
    public function applyFormatter($formatterKey, $fieldName, $data, &$context) {
        $ff = $this->get($formatterKey);
        if (!empty($ff)) {
            $ff->render($fieldName, $data, $context);
        } else {
            echo "<!-- ERROR: Unknown field formatter: '$formatterKey' -->\n";
        }
    }
}
