<?php
/**
 * Created by IntelliJ IDEA.
 * User: ballmann
 * Date: 12/4/20
 * Time: 8:29 AM
 */

namespace SwissCollections\RenderConfig;

use SwissCollections\RecordDriver\FieldRenderContext;
use SwissCollections\RecordDriver\SolrMarc;

class CompoundEntry extends AbstractRenderConfigEntry {

    /**
     * @var SingleEntry[]
     */
    public $elements = [];

    /**
     * @var String[]
     */
    protected $entryOrder = [];

    /**
     * GroupEntry constructor.
     * @param String $labelKey
     * @param int $marcIndex
     * @param int $indicator1 set to -1 if not relevant
     * @param int $indicator2 set to -1 if not relevant
     */
    public function __construct(String $labelKey, int $marcIndex, int $indicator1 = -1, int $indicator2 = -1) {
        parent::__construct($labelKey, $marcIndex, $indicator1, $indicator2);
        $this->setLineRenderMode();
    }

    public function addElement(String $labelKey, String $subfieldName) {
        $singleEntry = new SingleEntry($labelKey, $this->marcIndex, $subfieldName, $this->indicator1, $this->indicator2);
        $singleEntry->renderMode = $this->renderMode;
        $singleEntry->repeated = $this->repeated;
        array_push($this->elements, $singleEntry);
    }

    /**
     * @param String[] $order
     */
    public function setEntryOrder($order) {
        $this->entryOrder = $order;
    }

    public function __toString() {
        $s = "CompoundEntry{[\n";
        foreach ($this->elements as $e) {
            $s = $s . "\t\t\t" . $e . ",\n";
        }
        return $s . "] ," . $this->repeated . "}";
    }

    public function get(String $name) {
        foreach ($this->elements as $element) {
            if ($name === $element->labelKey) {
                return $element;
            }
        }
        return null;
    }

    public function orderEntries() {
        $newEntries = [];
        foreach ($this->entryOrder as $fieldName) {
            $e = $this->get($fieldName);
            if ($e) {
                $newEntries[] = $e;
            }
        }
        foreach ($this->elements as $element) {
            if (!in_array($element->labelKey, $this->entryOrder)) {
                $newEntries[] = $element;
            }
        }
        $this->elements = $newEntries;
    }

    /**
     * @param SolrMarc $solrMarc
     * @param array|\File_MARC_Control_Field|\File_MARC_Data_Field $field
     * @param SingleEntry $elem
     * @return null|\SwissCollections\RecordDriver\SubfieldRenderData
     */
    protected function getApplyRendererData(SolrMarc &$solrMarc, $field, SingleEntry $elem) {
        return $solrMarc->getRenderFieldData($field, $elem);
    }

    public function applyRenderer(SolrMarc &$solrMarc, FieldRenderContext &$context) {
        $array = $this->elements;
        $field = $context->field;
        $renderer = $context->renderer;
        $values = [];
        $lookupKey = "";
        foreach ($array as $elem) {
            $renderFieldData = $this->getApplyRendererData($solrMarc, $field, $elem);
            if (!empty($renderFieldData) && !$renderFieldData->emptyValue()) {
                $values[] = ['subMap' => $renderFieldData, 'renderConfig' => $elem];
                $lookupKey .= "{}" . $renderFieldData->asLookupKey();
            }
        }
        if (!$context->alreadyProcessed($lookupKey)) {
            foreach ($values as $key => $v) {
                $isFirst = $key === array_key_first($values);
                $isLast = $key === array_key_last($values);
                $context->updateCompoundState($isFirst, $isLast);
                $renderer($v['subMap'], $v['renderConfig'], $context);
            }
            $context->addProcessed($lookupKey);
        }
    }
}