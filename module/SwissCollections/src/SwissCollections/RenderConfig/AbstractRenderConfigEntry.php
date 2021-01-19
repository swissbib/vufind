<?php
/**
 * SwissCollections: AbstractRenderConfigEntry.php
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
 * Class AbstractRenderConfigEntry.
 *
 * This class represents all common configuration options of
 * a marc field. The class fields correspond to the columns in
 * detail-fields.csv.
 *
 * @category SwissCollections_VuFind
 * @package  SwissCollections\RenderConfig
 * @author   Lionel Walter <lionel.walter@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
abstract class AbstractRenderConfigEntry
{
    /**
     * The group's name from detail-fields.csv, column "Gruppierungsname / Oberbegriff".
     *
     * @var String
     */
    public $groupName;
    /**
     * The field's name from detail-fields.csv, column "Bezeichnung".
     *
     * @var String
     */
    public $fieldName;
    /**
     * The subfield's name from detail-fields.csv, column "Unterbezeichnung".
     *
     * @var String
     */
    public $subfieldName;
    /**
     * A synthetic key used especially for translations of this field's name.
     *
     * @var String
     */
    public $labelKey;
    /**
     * The marc index from from detail-fields.csv, column "datafield tag".
     *
     * @var int
     */
    public $marcIndex;
    /**
     * The first indicator from from detail-fields.csv, column "datafield ind1".
     *
     * @var int
     */
    public $indicator1;
    /**
     * The second indicator from from detail-fields.csv, column "datafield ind2".
     *
     * @var int
     */
    public $indicator2;
    /**
     * The condition  from from detail-fields.csv, column "subfield match condition".
     *
     * @var AbstractFieldCondition|null
     */
    public $subfieldCondition;

    /**
     * The field formatter to apply.
     *
     * @var FormatterConfig
     */
    protected $formatterConfig;

    /**
     * The group formater to apply.
     *
     * @var FormatterConfig
     */
    protected $fieldGroupFormatter;

    /**
     * AbstractRenderConfigEntry constructor.
     *
     * @param string                      $groupName       the group's name from detail-fields.csv, column "Gruppierungsname / Oberbegriff"
     * @param string                      $fieldName       the field's name from detail-fields.csv, column "Bezeichnung"
     * @param string                      $subfieldName    the subfield's name from detail-fields.csv, column "Unterbezeichnung"
     * @param int                         $marcIndex       the marc index from from detail-fields.csv, column "datafield tag"
     * @param FormatterConfig             $formatterConfig the formatter to apply
     * @param int                         $indicator1      the first indicator from from detail-fields.csv, column "datafield ind1"
     * @param int                         $indicator2      the second indicator from from detail-fields.csv, column "datafield ind2"
     * @param AbstractFieldCondition|null $condition       the condition  from from detail-fields.csv, column "subfield match condition"
     */
    public function __construct(
        $groupName, $fieldName, $subfieldName, $marcIndex,
        $formatterConfig, $indicator1, $indicator2, $condition
    ) {
        $this->groupName = $groupName;
        $this->fieldName = $fieldName;
        $this->subfieldName = $subfieldName;
        $this->marcIndex = $marcIndex;
        $this->indicator1 = $indicator1;
        $this->indicator2 = $indicator2;
        $this->formatterConfig = $formatterConfig;
        $this->subfieldCondition = $condition;
        $this->labelKey = AbstractRenderConfigEntry::buildLabelKey(
            $groupName, $subfieldName
        );
    }

    /**
     * Build the translation lookup key.
     *
     * @param string $groupName    the group's name
     * @param string $subfieldName the subfield's name
     *
     * @return string
     */
    public static function buildLabelKey(string $groupName, string $subfieldName
    ): string {
        return $groupName . "." . $subfieldName;
    }

    /**
     * Returns the formatter's name.
     *
     * @return string
     */
    public function getRenderMode(): string
    {
        return $this->formatterConfig->getFormatterName();
    }

    /**
     * Returns a string represenation.
     *
     * @return string
     */
    public function __toString()
    {
        return "AbstractRenderConfigEntry{"
            . $this->labelKey . ","
            . $this->groupName . ","
            . $this->fieldName . ","
            . $this->subfieldName . ","
            . $this->marcIndex . ","
            . $this->indicator1 . ","
            . $this->indicator2 . ","
            . $this->subfieldCondition . ","
            . $this->formatterConfig . ","
            . $this->fieldGroupFormatter . "}";
    }

    /**
     * Sort "elements".
     *
     * @return void
     */
    public function orderEntries()
    {
        // NOP
    }

    /**
     * Returns all subfield values to render to html which fit this field
     * configuration.
     *
     * @param \File_MARC_Control_Field|\File_MARC_Field $field   all available marc subfield values
     * @param FieldRenderContext                        $context the render context
     *
     * @return FieldFormatterData[]
     */
    public function getAllRenderData(&$field, &$context): array
    {
        return [];
    }

    /**
     * Contains the given marc field subfields to render to html?
     *
     * @param \File_MARC_Control_Field|\File_MARC_Field $field    the marc field
     * @param SolrMarc                                  $solrMarc the marc record
     *
     * @return bool
     */
    public function hasRenderData(&$field, $solrMarc): bool
    {
        return true;
    }

    /**
     * Exist values to render to html for this configuration?
     *
     * @param SolrMarc $solrMarc the marc record
     *
     * @return bool
     */
    public function isEmpty(SolrMarc $solrMarc): bool
    {
        $fields = $solrMarc->getFieldValues($this);
        if (!empty($fields)) {
            foreach ($fields as $field) {
                if ($this->hasRenderData($field, $solrMarc)) {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * Create a lookup key to avoid the output of duplicate values to html.
     *
     * @param FieldFormatterData[] $fieldFormatterDataList the values to render to html
     *
     * @return string
     */
    public function calculateRenderDataLookupKey($fieldFormatterDataList
    ): string {
        $key = "";
        foreach ($fieldFormatterDataList as $ffd) {
            $key = $key . "{}" . $ffd->subfieldRenderData->asLookupKey();
        }
        return $key;
    }

    /**
     * Set the html code to output around all list items.
     *
     * @param string $start contains the html to render before all list items
     * @param string $end   contains the html to render after all list items
     *
     * @return void
     */
    public function setListHtml(string $start, string $end): void
    {
        $this->formatterConfig->setListHtml($start, $end);
    }

    /**
     * Render the given field values to html.
     *
     * @param FieldFormatterData[] $values  the field values to rendern
     * @param FieldRenderContext   $context the render context
     *
     * @return void
     */
    public function renderImpl(&$values, &$context)
    {
        $lookupKey = $this->calculateRenderDataLookupKey($values);
        if (count($values) > 0 && !$context->alreadyProcessed($lookupKey)) {
            if ($this->formatterConfig->isRepeated()) {
                echo $this->formatterConfig->listStartHml;
            }
            $this->applyFormatter($lookupKey, $values, $context);
            if ($this->formatterConfig->isRepeated()) {
                echo $this->formatterConfig->listEndHml;
            }
        } else {
            if (count($values) > 0 && $context->alreadyProcessed($lookupKey)) {
                echo "<!-- DEDUP: " . print_r($values, true) . " -->\n";
            }
        }
    }

    /**
     * Apply the configured formatter to given field values.
     *
     * @param String               $lookupKey a hash key of the values for quick lookup
     * @param FieldFormatterData[] $values    the field values to render to html
     * @param FieldRenderContext   $context   the render context
     *
     * @return void
     */
    public function applyFormatter($lookupKey, &$values, $context)
    {
        $context->applyFieldFormatter(
            $lookupKey, $values, $this->getRenderMode(), $this->labelKey,
            $context
        );
    }

    /**
     * Render the given field values to html.
     *
     * @param \File_MARC_Control_Field|\File_MARC $field   the marc field to render
     * @param FieldRenderContext                  $context the render context
     *
     * @return void
     */
    public function render(&$field, &$context)
    {
        $values = $this->getAllRenderData($field, $context);
        $this->renderImpl($values, $context);
    }

    /**
     * Returns an object of config options from detail-view-field-structure.yaml for this
     * marc field.
     *
     * @return FormatterConfig
     */
    public function getFormatterConfig()
    {
        return $this->formatterConfig;
    }

    /**
     * Get the configured field group formatter.
     *
     * @return FormatterConfig|null
     */
    public function getFieldGroupFormatter()
    {
        return $this->fieldGroupFormatter;
    }

    /**
     * Set the field group formatter.
     *
     * @param FormatterConfig|null $fieldGroupFormatter an instance
     *
     * @return void
     */
    public function setFieldGroupFormatter($fieldGroupFormatter): void
    {
        // keep default formatter object
        if ($fieldGroupFormatter !== null) {
            $this->fieldGroupFormatter = $fieldGroupFormatter;
        }
    }

    /**
     * Checks the given field and all and'ed conditions. Returns true if all
     * conditions are fulfilled.
     *
     * @param \File_MARC_Data_Field|\File_MARC_Control_Field $field    the marc field
     * @param SolrMarc                                       $solrMarc the marc record
     *
     * @return bool
     */
    public function checkCondition($field, $solrMarc): bool
    {
        if (empty($this->subfieldCondition)) {
            return true;
        }
        return $this->subfieldCondition->assertTrue($field, $solrMarc);
    }
}