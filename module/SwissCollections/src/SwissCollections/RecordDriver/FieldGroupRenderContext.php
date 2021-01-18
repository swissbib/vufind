<?php
/**
 * SwissCollections: FieldGroupRenderContext.php
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

use SwissCollections\Formatter\FieldFormatterRegistry;
use SwissCollections\Formatter\SubfieldFormatterRegistry;
use SwissCollections\RenderConfig\FormatterConfig;

/**
 * Render context for field groups.
 *
 * @category SwissCollections_VuFind
 * @package  SwissCollections\RecordDriver
 * @author   Lionel Walter <lionel.walter@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://vufind.org/wiki/development Wiki
 */
class FieldGroupRenderContext
{
    /**
     * The registry of all field formatters.
     *
     * @var FieldFormatterRegistry
     */
    public $fieldFormatterRegistry;

    /**
     * The registry of all subfield formatters.
     *
     * @var SubfieldFormatterRegistry
     */
    public $subfieldFormatterRegistry;

    /**
     * The marc record.
     *
     * @var SolrMarc
     */
    public $solrMarc;

    /**
     * The formatter configuration of the group.
     *
     * @var FormatterConfig|null
     */
    public $formatterConfig;

    /**
     * FieldRenderContext constructor.
     *
     * @param FieldFormatterRegistry    $fieldFormatterRegistry    the field registry
     * @param SubfieldFormatterRegistry $subfieldFormatterRegistry the subfield registry
     * @param SolrMarc                  $solrMarc                  the marc record
     */
    public function __construct(
        $fieldFormatterRegistry, $subfieldFormatterRegistry, SolrMarc $solrMarc
    ) {
        $this->solrMarc = $solrMarc;
        $this->fieldFormatterRegistry = $fieldFormatterRegistry;
        $this->subfieldFormatterRegistry = $subfieldFormatterRegistry;
        $this->processedSubMaps = [];
    }
}