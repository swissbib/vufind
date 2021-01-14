<?php

namespace SwissCollections\RecordDriver;

use SwissCollections\Formatter\FieldFormatterData;
use SwissCollections\Formatter\FieldFormatterRegistry;
use SwissCollections\Formatter\SubfieldFormatterRegistry;

class FieldRenderContext
{

    // any already processed values
    public $processedSubMaps;

    /**
     * @var FieldFormatterRegistry
     */
    protected $fieldFormatterRegistry;

    /**
     * @var SubfieldFormatterRegistry
     */
    protected $subfieldFormatterRegistry;

    /**
     * @var SolrMarc
     */
    public $solrMarc;

    /**
     * FieldRenderContext constructor.
     *
     * @param FieldFormatterRegistry $fieldFormatterRegistry
     * @param SolrMarc               $solrMarc
     */
    public function __construct($fieldFormatterRegistry, SolrMarc $solrMarc, $subfieldFormatterRegistry)
    {
        $this->solrMarc = $solrMarc;
        $this->fieldFormatterRegistry = $fieldFormatterRegistry;
        $this->subfieldFormatterRegistry = $subfieldFormatterRegistry;
        $this->processedSubMaps = [];
    }

    /**
     * @param  String $candidate
     * @return bool
     */
    public function alreadyProcessed(String $candidate): bool
    {
        return $this->processedSubMaps[$candidate] === true;
    }

    public function addProcessed(String $candidate)
    {
        $this->processedSubMaps[$candidate] = true;
    }

    /**
     * @param String               $lookupKey
     * @param FieldFormatterData[] $data
     * @param String               $renderMode
     * @param String               $labelKey
     * @param FieldRenderContext   $context
     */
    public function applyFieldFormatter($lookupKey, &$data, $renderMode, $labelKey, $context): void
    {
        $this->fieldFormatterRegistry->applyFormatter($renderMode, $labelKey, $data, $context);
        $this->addProcessed($lookupKey);
    }

    /**
     * @param String             $lookupKey
     * @param FieldFormatterData $data
     * @param String             $renderMode
     * @param String             $labelKey
     * @param FieldRenderContext $context
     */
    public function applySubfieldFormatter($lookupKey, &$data, $renderMode, $labelKey, $context)
    {
        $this->subfieldFormatterRegistry->applyFormatter($renderMode, $labelKey, $data, $context);
        if (!empty($lookupKey)) {
            $this->addProcessed($lookupKey);
        }
    }
}