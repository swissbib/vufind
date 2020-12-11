<?php

namespace SwissCollections\RecordDriver;

use SwissCollections\Formatter\FieldFormatterData;
use SwissCollections\Formatter\FieldFormatterRegistry;

class FieldRenderContext {

    // any already processed values
    public $processedSubMaps;

    /**
     * @var FieldFormatterRegistry
     */
    protected $fieldFormatterRegistry;

    /**
     * @var SolrMarc
     */
    public $solrMarc;

    /**
     * FieldRenderContext constructor.
     * @param FieldFormatterRegistry $fieldFormatterRegistry
     * @param SolrMarc $solrMarc
     */
    public function __construct(FieldFormatterRegistry $fieldFormatterRegistry, SolrMarc $solrMarc) {
        $this->solrMarc = $solrMarc;
        $this->fieldFormatterRegistry = $fieldFormatterRegistry;
        $this->processedSubMaps = [];
    }

    /**
     * @param String $candidate
     * @return bool
     */
    public function alreadyProcessed(String $candidate): bool {
        return $this->processedSubMaps[$candidate] === TRUE;
    }

    public function addProcessed(String $candidate) {
        $this->processedSubMaps[$candidate] = TRUE;
    }

    /**
     * @param String $lookupKey
     * @param FieldFormatterData[] $data
     * @param String $renderMode
     * @param String $labelKey
     */
    public function applyFieldFormatter($lookupKey, &$data, $renderMode, $labelKey): void {
        $this->fieldFormatterRegistry->applyFormatter($renderMode, $labelKey, $data, $context);
        $this->addProcessed($lookupKey);
    }
}