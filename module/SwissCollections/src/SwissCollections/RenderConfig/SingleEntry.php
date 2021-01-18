<?php
/**
 * SwissCollections: SingleEntry.php
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
 * @package  SwissCollections\RenderConfig
 * @author   Lionel Walter <lionel.walter@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.swisscollections.org Project Wiki
 */

namespace SwissCollections\RenderConfig;

use SwissCollections\Formatter\FieldFormatterData;
use SwissCollections\RecordDriver\FieldRenderContext;
use SwissCollections\RecordDriver\SolrMarc;

/**
 * Class SingleEntry. Represents one marc subfield of one marc field.
 *
 * @category SwissCollections_VuFind
 * @package  SwissCollections\RenderConfig
 * @author   Lionel Walter <lionel.walter@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
class SingleEntry extends AbstractRenderConfigEntry
{
    /**
     * The marc subfield name.
     *
     * @var null|string
     */
    protected $marcSubfieldName;

    /**
     * SingleEntry constructor.
     *
     * @param string                      $groupName        the group's name from detail-fields.csv, column "Gruppierungsname / Oberbegriff"
     * @param string                      $fieldName        the field's name from detail-fields.csv, column "Bezeichnung"
     * @param string                      $subfieldName     the subfield's name from detail-fields.csv, column "Unterbezeichnung"
     * @param int                         $marcIndex        the marc index from from detail-fields.csv, column "datafield tag"
     * @param FormatterConfig             $formatterConfig  from "detail-view-field-structure.yaml"
     * @param String                      $marcSubfieldName the marc subfield's name
     * @param int                         $indicator1       the first indicator from from detail-fields.csv, column "datafield ind1"; set to -1 if not relevant; set to -1 if not relevant
     * @param int                         $indicator2       the second indicator from from detail-fields.csv, column "datafield ind2"; set to -1 if not relevant; set to -1 if not relevant
     * @param AbstractFieldCondition|null $condition        the condition  from from detail-fields.csv, column "subfield match condition"
     */
    public function __construct(
        $groupName, $fieldName, $subfieldName, $marcIndex, $formatterConfig,
        $marcSubfieldName = null,
        $indicator1 = -1, $indicator2 = -1, $condition = ""
    ) {
        parent::__construct(
            $groupName, $fieldName, $subfieldName, $marcIndex, $formatterConfig,
            $indicator1,
            $indicator2, $condition
        );
        $this->marcSubfieldName = $marcSubfieldName;
        if (empty($this->formatterConfig->formatterNameDefault)) {
            $this->formatterConfig->formatterNameDefault = "simple";
        }
    }

    /**
     * Returns empty array if no subfield name is set.
     *
     * @return array
     */
    public function buildMap()
    {
        $result = [];
        if (!empty($this->marcSubfieldName)) {
            $result[$this->marcSubfieldName] = "value";
        }
        return $result;
    }

    /**
     * Returns a string represenation.
     *
     * @return string
     */
    public function __toString()
    {
        return "SingleEntry{" . parent::__toString() . ","
            . $this->marcSubfieldName . "}";
    }

    /**
     * Get the marc subfield's name.
     *
     * @return string
     */
    public function getMarcSubfieldName(): string
    {
        return $this->marcSubfieldName;
    }

    /**
     * Returns the subfield's value to render to html which fit this field
     * configuration.
     *
     * @param \File_MARC_Control_Field|\File_MARC_Field $field   all available marc subfield values
     * @param FieldRenderContext                        $context the render context
     *
     * @return FieldFormatterData[]
     */
    public function getAllRenderData(&$field, &$context): array
    {
        $values = [];
        $renderFieldData = $context->solrMarc->getRenderFieldData(
            $field, $this
        );
        if (!empty($renderFieldData) && !$renderFieldData->emptyValue()) {
            $values = [new FieldFormatterData($this, $renderFieldData)];
        }
        return $values;
    }

    /**
     * Contains the given marc field the subfield to render to html?
     *
     * @param \File_MARC_Control_Field|\File_MARC_Field $field    the marc field
     * @param SolrMarc                                  $solrMarc the marc record
     *
     * @return bool
     */
    public function hasRenderData(&$field, $solrMarc): bool
    {
        if (empty($this->marcSubfieldName)) {
            return $solrMarc->checkIndicators(
                $field, $this->indicator1, $this->indicator2
            );
        } else {
            $renderFieldData = $solrMarc->getRenderFieldData($field, $this);
            return !empty($renderFieldData) && !$renderFieldData->emptyValue();
        }
    }

    /**
     * Apply the formatter.
     *
     * @param String               $lookupKey a hash key of the values for quick lookup
     * @param FieldFormatterData[] $values    the subfield's value (array has length of 1)
     * @param FieldRenderContext   $context   the render context
     *
     * @return void
     */
    public function applyFormatter($lookupKey, &$values, $context)
    {
        $renderMode = $this->getRenderMode();
        $context->applySubfieldFormatter(
            $lookupKey, $values[0], $renderMode, $this->labelKey, $context
        );
    }
}