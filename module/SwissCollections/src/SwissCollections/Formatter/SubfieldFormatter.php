<?php

namespace SwissCollections\Formatter;

use Laminas\View\Renderer\PhpRenderer;
use SwissCollections\RecordDriver\FieldRenderContext;
use SwissCollections\RecordDriver\SubfieldRenderData;
use SwissCollections\RenderConfig\SingleEntry;


abstract class SubfieldFormatter {

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
     * @param FieldFormatterData $fieldDataList
     * @param FieldRenderContext $context ;
     */
    public abstract function render($fieldName, $fieldData, $context): void;

    public abstract function getName(): String;
}

class SubfieldFormatterRegistry {
    /**
     * @var array<String,SubfieldFormatter>
     */
    protected $registry;

    public function register(SubfieldFormatter $ff) {
        $this->registry[$ff->getName()] = $ff;
    }

    /**
     * @param String $name
     * @return null|SubfieldFormatter
     */
    public function get(String $name) {
        return $this->registry[$name];
    }

    /**
     * @param String $formatterKey
     * @param String $fieldName
     * @param FieldFormatterData $fieldData
     * @param FieldRenderContext $context ;
     */
    public function applyFormatter($formatterKey, $fieldName, $fieldData, &$context) {
        $ff = $this->get($formatterKey);
        if (!empty($ff)) {
            $ff->render($fieldName, $fieldData, $context);
        } else {
            echo "<!-- ERROR: Unknown subfield formatter: '$formatterKey' -->\n";
        }
    }
}
