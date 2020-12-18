<?php

namespace SwissCollections\Formatter;

use Laminas\View\Renderer\PhpRenderer;
use SwissCollections\RecordDriver\FieldGroupRenderContext;
use SwissCollections\RecordDriver\FieldRenderContext;
use SwissCollections\RenderConfig\AbstractRenderConfigEntry;

abstract class FieldGroupFormatter {

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
     * @param AbstractRenderConfigEntry[] $fieldDataList
     * @param FieldGroupRenderContext $context ;
     */
    public abstract function render($fieldName, &$fieldDataList, &$context): void;

    public abstract function getName(): String;

    /**
     * @param AbstractRenderConfigEntry $renderElem
     * @param FieldGroupRenderContext $context
     * @param String $repeatStartHtml
     * @param String $repeatEndHtml
     */
    public function outputField(&$renderElem, &$context, $repeatStartHtml, $repeatEndHtml): void {
        $fields = $context->solrMarc->getMarcFields($renderElem->marcIndex);
        if (!empty($fields)) {
            if ($renderElem->repeated) {
                echo $repeatStartHtml;
            }
            $fieldContext = new FieldRenderContext($context->fieldFormatterRegistry, $context->solrMarc);
            foreach ($fields as $field) {
                $renderElem->render($field, $fieldContext);
            }
            if ($renderElem->repeated) {
                echo $repeatEndHtml;
            }
        }
    }
}

class FieldGroupFormatterRegistry {
    /**
     * @var array<String,FieldGroupFormatter>
     */
    protected $registry;

    public function register(FieldGroupFormatter $ff) {
        $this->registry[$ff->getName()] = $ff;
    }

    /**
     * @param String $name
     * @return null|FieldGroupFormatter
     */
    public function get(String $name) {
        return $this->registry[$name];
    }

    /**
     * @param String $formatterKey
     * @param String $fieldName
     * @param AbstractRenderConfigEntry[] $data
     * @param FieldGroupRenderContext $context ;
     */
    public function applyFormatter($formatterKey, $fieldName, &$data, &$context) {
        $ff = $this->get($formatterKey);
        if (!empty($ff)) {
            $ff->render($fieldName, $data, $context);
        } else {
            echo "<!-- ERROR: Unknown field group formatter: '$formatterKey' -->\n";
        }
    }
}
