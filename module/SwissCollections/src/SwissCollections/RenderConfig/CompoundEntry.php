<?php
/**
 * SwissCollections: CompoundEntry.php
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
use SwissCollections\RecordDriver\SubfieldRenderData;

/**
 * Class CompoundEntry. Represents several non repeating marc subfields of one marc field.
 *
 * @category SwissCollections_VuFind
 * @package  SwissCollections\RenderConfig
 * @author   Lionel Walter <lionel.walter@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
class CompoundEntry extends AbstractRenderConfigEntry
{

    /**
     * All subfield configs.
     *
     * @var SingleEntry[]
     */
    public $elements = [];

    /**
     * All hidden marc subfields.
     *
     * @var string[]
     */
    protected $hiddenMarcSubfields = [];

    /**
     * CompoundEntry constructor.
     *
     * @param string                      $groupName       the group's name from detail-fields.csv, column "Gruppierungsname / Oberbegriff"
     * @param string                      $fieldName       the field's name from detail-fields.csv, column "Bezeichnung"
     * @param string                      $subfieldName    the subfield's name from detail-fields.csv, column "Unterbezeichnung"
     * @param int                         $marcIndex       the marc index from from detail-fields.csv, column "datafield tag"
     * @param FormatterConfig             $formatterConfig from "detail-view-field-structure.yaml"
     * @param int                         $indicator1      the first indicator from from detail-fields.csv, column "datafield ind1"; set to -1 if not relevant
     * @param int                         $indicator2      the second indicator from from detail-fields.csv, column "datafield ind2"; set to -1 if not relevant
     * @param AbstractFieldCondition|null $condition       the condition  from from detail-fields.csv, column "subfield match condition"
     */
    public function __construct(
        $groupName, $fieldName, $subfieldName, $marcIndex,
        $formatterConfig, $indicator1 = -1, $indicator2 = -1, $condition = ""
    ) {
        parent::__construct(
            $groupName, $fieldName, $subfieldName, $marcIndex, $formatterConfig,
            $indicator1,
            $indicator2, $condition
        );
        if (empty($this->formatterConfig->formatterNameDefault)) {
            $this->formatterConfig->formatterNameDefault = "line";
        }
    }

    /**
     * Add a new subfield.
     *
     * @param string $subfieldName     was constructed by a call to {@link AbstractRenderConfigEntry::buildLabelKey}
     * @param string $marcSubfieldName a marc subfield name (e.g. 'a')
     *
     * @return void
     */
    public function addElement(string $subfieldName, string $marcSubfieldName)
    {
        $singleEntry = $this->buildElement($subfieldName, $marcSubfieldName);
        array_push($this->elements, $singleEntry);
    }

    /**
     * Returns the formatter to use. Uses "simple" as default marc subfield formatter.
     *
     * @return FieldFormatterConfig[]
     */
    public function getEntryOrder()
    {
        return $this->formatterConfig->getEntryOrder("simple");
    }

    /**
     * Returns a string represenation.
     *
     * @return string
     */
    public function __toString()
    {
        $s = "CompoundEntry{" . parent::__toString() . ",[\n";
        foreach ($this->elements as $e) {
            $s = $s . "\t\t\t" . $e . ",\n";
        }
        return $s . "],"
            . "hidden=" . implode(",", $this->hiddenMarcSubfields)
            . "}";
    }

    /**
     * Get a subfield configuration by name.
     *
     * @param string $name the
     *
     * @return null|SingleEntry
     */
    protected function get(string $name)
    {
        foreach ($this->elements as $element) {
            if ($name === $element->labelKey) {
                return $element;
            }
        }
        return null;
    }

    /**
     * Sort subfields.
     *
     * @return void
     */
    public function orderEntries()
    {
        $newEntries = [];
        $entryOrder = $this->getEntryOrder();
        $fieldNames = [];
        foreach ($entryOrder as $fieldFormatter) {
            $fieldName = $fieldFormatter->fieldName;
            $fieldNames[] = $fieldName;
            $e = $this->get($fieldName); // TODO get() searchey by labelKey!
            if ($e) {
                $newEntries[] = $e;
            }
        }
        foreach ($this->elements as $element) {
            if (!in_array($element->labelKey, $fieldNames)) {
                $newEntries[] = $element;
            }
        }
        $this->elements = $newEntries;
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
        /**
         * The subfield values.
         *
         * @var FieldFormatterData[]
         */
        $values = [];
        // if no subfields are specified, get all
        if (empty($this->elements)) {
            $fieldValueMap = $context->solrMarc->getMarcFieldRawMap(
                $field, $this->subfieldCondition,
                $this->hiddenMarcSubfields
            );
            $ind1 = IndicatorCondition::$UNKNOWN_INDICATOR;
            $ind2 = IndicatorCondition::$UNKNOWN_INDICATOR;
            if ($field instanceof \File_MARC_Data_Field) {
                $ind1 = IndicatorCondition::parse(
                    $field->getIndicator(1)
                );
                $ind2 = IndicatorCondition::parse(
                    $field->getIndicator(2)
                );
            }
            foreach ($fieldValueMap as $marcSubfieldName => $value) {
                $elem = $this->buildElement(
                    $this->subfieldName, $marcSubfieldName
                );
                $renderFieldData = new SubfieldRenderData(
                    $value, true, $ind1, $ind2
                );
                $values[] = new FieldFormatterData($elem, $renderFieldData);
            }
        } else {
            // get only values for the specified fields
            foreach ($this->elements as $elem) {
                if (!in_array(
                    $elem->getMarcSubfieldName(), $this->hiddenMarcSubfields
                )
                ) {
                    $renderFieldData = $context->solrMarc->getRenderFieldData(
                        $field, $elem
                    );
                    if (!empty($renderFieldData)
                        && !$renderFieldData->emptyValue()
                    ) {
                        $values[] = new FieldFormatterData(
                            $elem, $renderFieldData
                        );
                    }
                }
            }
        }
        return $values;
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
        // all values matching the required indicators are shown if no subfields are specified
        if (empty($this->elements)) {
            $rawData = $solrMarc->getMarcFieldRawMap(
                $field, $this->subfieldCondition,
                $this->getHiddenMarcSubfields()
            );
            return !empty($rawData);
        } else {
            // show only the specified subfields
            foreach ($this->elements as $elem) {
                if ($elem->hasRenderData($field, $solrMarc)) {
                    return true;
                }
            }
            return false;
        }
    }

    /**
     * Contains this configuration the given subfield's name?
     *
     * @param string $name the marc subfield's name to check
     *
     * @return bool
     */
    public function knowsSubfield($name): bool
    {
        return $this->findSubfield($name) !== null;
    }

    /**
     * Searches a subfield's configuration by name.
     *
     * @param string $name the marc subfield's name to find
     *
     * @return null | SingleEntry
     */
    protected function findSubfield($name)
    {
        foreach ($this->elements as $element) {
            if ($name === $element->getMarcSubfieldName()) {
                return $element;
            }
        }
        return null;
    }

    /**
     * Creates a new {@link FieldFormatterData} instance.
     *
     * @param string   $marcSubfieldName the subfield's name
     * @param string   $text             the subfield's value
     * @param SolrMarc $solrMarc         the marc record
     *
     * @return FieldFormatterData
     */
    public function buildFieldFormatterData($marcSubfieldName, $text, &$solrMarc
    ) {
        $renderConfigEntry = $this->findSubfield($marcSubfieldName);
        if ($renderConfigEntry === null) {
            throw new \Exception("Didn't find $marcSubfieldName in " . $this);
        }
        $renderFieldData = $solrMarc->buildGenericSubMap($text, true);
        return new FieldFormatterData($renderConfigEntry, $renderFieldData);
    }

    /**
     * Create a copy without elements.
     *
     * @return CompoundEntry
     */
    public function flatCloneEntry()
    {
        return new CompoundEntry(
            $this->groupName, $this->fieldName, $this->subfieldName,
            $this->marcIndex,
            $this->formatterConfig, $this->indicator1, $this->indicator2,
            $this->subfieldCondition
        );
    }

    /**
     * Helper method to build a new subfield config instance
     * (@link SingleEntry).
     *
     * @param string $subfieldName     the translation key build by {@link AbstractRenderConfigEntry::buildLabelKey}
     * @param string $marcSubfieldName the marc subfield's name
     *
     * @return SingleEntry
     */
    protected function buildElement(
        string $subfieldName, string $marcSubfieldName
    ): SingleEntry {
        $formatter = $this->formatterConfig->singleFormatter(
            $subfieldName . "-" . $marcSubfieldName, "simple",
            $this->formatterConfig
        );
        $singleEntry = new SingleEntry(
            $this->groupName, $this->fieldName, $this->subfieldName,
            $this->marcIndex,
            $formatter, $marcSubfieldName, $this->indicator1, $this->indicator2,
            $this->subfieldCondition
        );
        $singleEntry->fieldGroupFormatter = $this->fieldGroupFormatter;
        return $singleEntry;
    }

    /**
     * Gets the hidden marc subfields.
     *
     * @return string[]
     */
    public function getHiddenMarcSubfields(): array
    {
        return $this->hiddenMarcSubfields;
    }

    /**
     * Hide a marc subfield name.
     *
     * @param string $name the marc subfield's name to add
     *
     * @return void
     */
    public function addHiddenMarcSubfield(string $name): void
    {
        if (!in_array($name, $this->hiddenMarcSubfields)) {
            $this->hiddenMarcSubfields[] = $name;
        }
    }
}