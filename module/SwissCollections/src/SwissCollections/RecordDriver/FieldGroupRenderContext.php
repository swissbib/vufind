<?php

namespace SwissCollections\RecordDriver;

use SwissCollections\Formatter\FieldFormatterRegistry;
use SwissCollections\Formatter\SubfieldFormatterRegistry;

class FieldGroupRenderContext
{
    /**
     * @var FieldFormatterRegistry
     */
    public $fieldFormatterRegistry;

    /**
     * @var SubfieldFormatterRegistry
     */
    public $subfieldFormatterRegistry;

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
     *
     * @param FieldFormatterRegistry    $fieldFormatterRegistry
     * @param SubfieldFormatterRegistry $subfieldFormatterRegistry
     * @param SolrMarc                  $solrMarc
     */
    public function __construct($fieldFormatterRegistry, $subfieldFormatterRegistry, SolrMarc $solrMarc)
    {
        $this->solrMarc = $solrMarc;
        $this->fieldFormatterRegistry = $fieldFormatterRegistry;
        $this->subfieldFormatterRegistry = $subfieldFormatterRegistry;
        $this->processedSubMaps = [];
    }
}