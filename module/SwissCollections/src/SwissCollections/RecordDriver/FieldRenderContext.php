<?php
/**
 * SwissCollections: FieldRenderContext.php
 *
 * PHP version 7
 *
 * Copyright (C) project swissbib, University Library Basel, Switzerland
 * http://www.swisscollections.org  / http://www.swisscollections.ch / http://www.ub.unibas.ch
 *
 * Date: 1/12/20
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License version 2,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category SwissCollections_VuFind
 * @package  SwissCollections\RecordDriver
 * @author   Lionel Walter <lionel.walter@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.swisscollections.org Project Wiki
 */

namespace SwissCollections\RecordDriver;

use SwissCollections\Formatter\FieldFormatter;
use SwissCollections\Formatter\FieldFormatterData;
use SwissCollections\Formatter\FieldFormatterRegistry;
use SwissCollections\Formatter\SubfieldFormatterRegistry;
use SwissCollections\RenderConfig\FormatterConfig;

/**
 * Context for field formatters.
 *
 * @category SwissCollections_VuFind
 * @package  SwissCollections\RecordDriver
 * @author   Lionel Walter <lionel.walter@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://vufind.org/wiki/development Wiki
 */
class FieldRenderContext
{

    // any already processed values
    public $processedSubMaps;

    /**
     * The registry of all field formatters.
     *
     * @var FieldFormatterRegistry
     */
    protected $fieldFormatterRegistry;

    /**
     * The registry of all subfield formatters.
     *
     * @var SubfieldFormatterRegistry
     */
    protected $subfieldFormatterRegistry;

    /**
     * The marc record.
     *
     * @var SolrMarc
     */
    public $solrMarc;

    /**
     * FieldRenderContext constructor.
     *
     * @param FieldFormatterRegistry    $fieldFormatterRegistry    the field formatter registry
     * @param SolrMarc                  $solrMarc                  the marc record
     * @param SubfieldFormatterRegistry $subfieldFormatterRegistry the subfield formatter registry
     */
    public function __construct(
        $fieldFormatterRegistry, $solrMarc, $subfieldFormatterRegistry
    ) {
        $this->solrMarc = $solrMarc;
        $this->fieldFormatterRegistry = $fieldFormatterRegistry;
        $this->subfieldFormatterRegistry = $subfieldFormatterRegistry;
        $this->processedSubMaps = [];
    }

    /**
     * Checks, whether a value was already rendered.
     *
     * @param string $candidate a lookup key build from values to render
     *
     * @return bool
     */
    public function alreadyProcessed(string $candidate): bool
    {
        return $this->processedSubMaps[$candidate] === true;
    }

    /**
     * Remember an already rendered value.
     *
     * @param string $candidate the value's lookup key
     *
     * @return void
     */
    public function addProcessed(string $candidate)
    {
        $this->processedSubMaps[$candidate] = true;
    }

    /**
     * Apply a field formatter.
     *
     * @param string               $lookupKey  a value's lookup key
     * @param FieldFormatterData[] $data       the values to render
     * @param string               $renderMode the field formatter's name
     * @param string               $labelKey   the field's translation key
     * @param FieldRenderContext   $context    the render context
     *
     * @return void
     */
    public function applyFieldFormatter(
        $lookupKey, &$data, $renderMode, $labelKey, $context
    ): void {
        $this->fieldFormatterRegistry->applyFormatter(
            $renderMode, $labelKey, $data, $context
        );
        $this->addProcessed($lookupKey);
    }

    /**
     * Apply a subfield formatter.
     *
     * @param String             $lookupKey       a value's lookup key
     * @param FieldFormatterData $data            the value to render
     * @param FormatterConfig    $formatterConfig the field formatter's config
     * @param String             $labelKey        the field's translation key
     *
     * @return void
     */
    public function applySubfieldFormatter(
        $lookupKey, &$data, $formatterConfig, $labelKey
    ) {
        $this->subfieldFormatterRegistry->applyFormatter(
            $formatterConfig, $labelKey, $data, $this
        );
        if (!empty($lookupKey)) {
            $this->addProcessed($lookupKey);
        }
    }
}