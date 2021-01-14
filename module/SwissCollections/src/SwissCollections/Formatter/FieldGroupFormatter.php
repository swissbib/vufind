<?php

namespace SwissCollections\Formatter;

use Laminas\View\Renderer\PhpRenderer;
use SwissCollections\RecordDriver\FieldGroupRenderContext;
use SwissCollections\RecordDriver\FieldRenderContext;
use SwissCollections\RenderConfig\AbstractRenderConfigEntry;
use SwissCollections\RenderConfig\FormatterConfig;

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
     * @param AbstractRenderConfigEntry[] $fieldDataList
     * @param FieldGroupRenderContext $context ;
     */
    public abstract function render(&$fieldDataList, &$context): void;

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
            if ($renderElem->getFormatterConfig()->isRepeated()) {
                echo $repeatStartHtml;
            }
            $fieldContext = new FieldRenderContext($context->fieldFormatterRegistry, $context->solrMarc,
                $context->subfieldFormatterRegistry);
            foreach ($fields as $field) {
                $renderElem->render($field, $fieldContext);
            }
            if ($renderElem->getFormatterConfig()->isRepeated()) {
                echo $repeatEndHtml;
            }
        }
    }

    public function labelKeyAsCssClass(String $labelKey): String {
        return preg_replace('/[. \/"§$%&()!=?+*~#\':,;]/', "_", $labelKey);
    }

    public function translateLabelKey(String $labelKey): String {
        $label = $this->phpRenderer->translate('page.detail.field.label.' . $labelKey);
        if (strpos($label, "page.detail.") !== FALSE) {
            $label = preg_replace("/[.]/", "-", $label);
        }
        return $label;
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
     * @param FormatterConfig $groupFormatter
     * @param AbstractRenderConfigEntry[] $data
     * @param FieldGroupRenderContext $context ;
     */
    public function applyFormatter($groupFormatter, &$data, &$context) {
        $context->formatterConfig = null;
        $ff = $this->get($groupFormatter->getFormatterName());
        if (!empty($ff)) {
            $context->formatterConfig = $groupFormatter;
            $ff->render($data, $context);
        } else {
            echo "<!-- ERROR: Unknown field group formatter: '" . $groupFormatter->getFormatterName() . "' -->\n";
        }
    }
}
