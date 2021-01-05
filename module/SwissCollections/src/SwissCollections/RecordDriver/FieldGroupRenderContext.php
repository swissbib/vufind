<?php

namespace SwissCollections\RecordDriver;

use SwissCollections\Formatter\FieldFormatterRegistry;

class FieldGroupRenderContext {
    /**
     * @var FieldFormatterRegistry
     */
    public $fieldFormatterRegistry;

    /**
     * @var SolrMarc
     */
    public $solrMarc;

    /**
     * @var FormatterConfig|null
     */
    public $formatterConfig;

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
}