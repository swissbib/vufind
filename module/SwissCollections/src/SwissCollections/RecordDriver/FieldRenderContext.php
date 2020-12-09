<?php

namespace SwissCollections\RecordDriver;

use SwissCollections\RenderConfig\AbstractRenderConfigEntry;

class FieldRenderContext {

    // any already processed values
    public $processedSubMaps;

    /**
     * @var \File_MARC_Field | \File_MARC_Control_Field | array - "array" contains raw data objects with
     *  keys "data" and "tag"
     */
    public $field;

    /**
     * @var AbstractRenderConfigEntry
     */
    public $rc;

    /**
     * @var Callable $renderer called with $subMap, AbstractRenderConfigEntry, FieldRenderContext
     */
    public $renderer;

    public $firstListEntry;
    public $lastListEntry;

    public $firstCompoundEntry;
    public $lastCompoundEntry;

    public $firstSequenceEntry;
    public $lastSequenceEntry;

    /**
     * FieldRenderContext constructor.
     * @param int $marcFieldNumber
     * @param AbstractRenderConfigEntry $rc
     * @param Callable $renderer called with $subMap, $marcField, FieldRenderContext
     */
    public function __construct(AbstractRenderConfigEntry $rc, Callable $renderer) {
        $this->rc = $rc;
        $this->renderer = $renderer;
        $this->processedSubMaps = [];
    }

    /**
     * @param \File_MARC_Field | \File_MARC_Control_Field $field
     * @param int $fieldIndex
     */
    public function updateListState($field, bool $isFirst, bool $isLast) {
        $this->field = $field;
        $this->firstListEntry = $isFirst;
        $this->lastListEntry = $isLast;
    }

    public function updateCompoundState(bool $isFirst, bool $isLast) {
        $this->firstCompoundEntry = $isFirst;
        $this->lastCompoundEntry = $isLast;
    }

    public function updateSequenceState($matchedValues, bool $isFirst, bool $isLast) {
        $this->field = $matchedValues;
        $this->firstSequenceEntry = $isFirst;
        $this->lastSequenceEntry = $isLast;
    }

    public function alreadyProcessed(&$candidate): bool {
        return in_array($candidate, $this->processedSubMaps, true);
    }

    public function addProcessed(&$candidate) {
        $this->processedSubMaps[] = $candidate;
    }
}