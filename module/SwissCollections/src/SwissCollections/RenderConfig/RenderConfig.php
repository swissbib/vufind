<?php
/**
 * SwissCollections: RenderConfig.php
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

use Laminas\View\Helper\AbstractHelper;
use SwissCollections\RecordDriver\ViewFieldInfo;

/**
 * Class RenderConfig.
 *
 * This class represents all configuration options of "detail-fields.csv".
 *
 * @category SwissCollections_VuFind
 * @package  SwissCollections\RenderConfig
 * @author   Lionel Walter <lionel.walter@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
class RenderConfig extends AbstractHelper
{
    public static $MARC_MAPPING_GROUP_KEY = 'Gruppierungsname / Oberbegriff';
    public static $MARC_MAPPING_FIELD_KEY = 'Bezeichnung';
    public static $MARC_MAPPING_SUBFIELD_KEY = 'Unterbezeichnung';
    public static $MARC_MAPPING_MARC_INDEX = 'datafield tag';
    public static $MARC_MAPPING_MARC_IND1 = 'datafield ind1';
    public static $MARC_MAPPING_MARC_IND2 = 'datafield ind2';
    public static $MARC_MAPPING_MARC_SUBFIELD = 'subfield code';
    public static $MARC_MAPPING_CONDITION = 'subfield match condition';

    /**
     * All group configuration.
     *
     * @var RenderGroupConfig[]
     */
    protected $info = [];

    /**
     * The information read from "detail-view-field-structure.yaml".
     *
     * @var ViewFieldInfo
     */
    protected $detailViewFieldInfo;

    /**
     * RenderConfig constructor.
     *
     * @param string[][]    $fieldMarcMapping    the raw csv data from "detail-fields.csv".
     * @param ViewFieldInfo $detailViewFieldInfo the raw data from "detail-view-field-structure.yaml".
     */
    public function __construct($fieldMarcMapping, $detailViewFieldInfo)
    {
        $this->buildRenderInfo($fieldMarcMapping, $detailViewFieldInfo);
    }

    /**
     * Adds one group configuration.
     *
     * @param RenderGroupConfig $entry the entry to add
     *
     * @return void
     */
    public function add(RenderGroupConfig $entry)
    {
        $this->info[] = $entry;
    }

    /**
     * Returns all group configurations.
     *
     * @return RenderGroupConfig[]
     */
    public function entries()
    {
        return $this->info;
    }

    /**
     * Returns a string represenation.
     *
     * @return string
     */
    public function __toString()
    {
        $s = "RenderConfig{[\n";
        foreach ($this->info as $e) {
            $s = $s . "\t" . $e . ",\n";
        }
        return $s . "]}";
    }

    /**
     * Returns a group's configuration by name.
     *
     * @param string $groupName the group's name
     *
     * @return null|RenderGroupConfig
     */
    public function get(String $groupName)
    {
        foreach ($this->info as $renderGroupConfig) {
            if ($groupName === $renderGroupConfig->getName()) {
                return $renderGroupConfig;
            }
        }
        return null;
    }

    /**
     * Sort the groups and their fields by the information in
     * "detail-view-field-structure.yaml".
     *
     * @return void
     */
    public function orderGroups()
    {
        $newGroups = [];
        $groupOrder = $this->detailViewFieldInfo->groupNames();
        foreach ($groupOrder as $groupName) {
            $gc = $this->get($groupName);
            if ($gc) {
                $newGroups[] = $gc;
                $gc->orderFields($this->detailViewFieldInfo);
            }
        }
        foreach ($this->info as $renderGroupConfig) {
            if (!in_array($renderGroupConfig->getName(), $groupOrder)) {
                $newGroups[] = $renderGroupConfig;
                $renderGroupConfig->orderFields($this->detailViewFieldInfo);
            }
        }
        $this->info = $newGroups;
    }

    /**
     * Process data read from "detail-fields.csv" and "detail-view-field-structure.yaml".
     *
     * @param string[][]    $fieldMarcMapping    the raw csv data from "detail-fields.csv".
     * @param ViewFieldInfo $detailViewFieldInfo the raw data from "detail-view-field-structure.yaml".
     *
     * @return void
     */
    protected function buildRenderInfo($fieldMarcMapping, $detailViewFieldInfo)
    {
        //        echo "<!-- CSV:\n " . print_r($fieldMarcMapping, true) . "-->\n";
        //        echo "<!-- DI:\n " . print_r($detailViewFieldInfo, true) . "-->\n";

        $this->detailViewFieldInfo = $detailViewFieldInfo;

        /**
         * Contains the parsed data.
         *
         * @var RenderGroupConfig
         */
        $renderGroup = null;
        /**
         * The current field.
         *
         * @var AbstractRenderConfigEntry
         */
        $renderGroupEntry = null;
        $lastGroupName = "";
        $lastFieldName = "";
        $lastSubfieldName = "";
        $lastSubfieldCount = 0;
        foreach ($fieldMarcMapping as $field) {
            $groupName = trim(
                $field[self::$MARC_MAPPING_GROUP_KEY]
            ); // always non empty
            if (!empty($groupName) && $groupName !== $lastGroupName) {
                $this->finishGroup($renderGroup, $renderGroupEntry);
                $renderGroup = new RenderGroupConfig($groupName);
                $lastGroupName = $groupName;
                $lastFieldName = "";
                $lastSubfieldName = "";
                $lastSubfieldCount = 0;
            }
            if (empty($groupName)) {
                $groupName = $lastGroupName;
            }

            $fieldName = trim(
                $field[self::$MARC_MAPPING_FIELD_KEY]
            ); // always non empty
            if ($fieldName !== $lastFieldName) {
                $lastSubfieldName = "";
                $lastSubfieldCount = 0;
                if (!empty($lastFieldName)) {
                    $this->finishField($renderGroup, $renderGroupEntry);
                }
                $lastFieldName = $fieldName;
            }

            // calculate sub field name (may be missing)
            $subFieldName = trim($field[self::$MARC_MAPPING_SUBFIELD_KEY]);
            if (empty($subFieldName)) {
                if (empty($lastSubfieldName)) {
                    $lastSubfieldName = $fieldName;
                    $lastSubfieldCount = 0;
                }
                $lastSubfieldCount++;
                $subFieldName = $lastSubfieldName . $lastSubfieldCount;
            }

            $marcIndex = trim($field[self::$MARC_MAPPING_MARC_INDEX]);
            if (!ctype_digit($marcIndex)) {
                if ($marcIndex === 'INTERNAL') {
                    $marcIndex = -1;
                } else {
                    echo "<!-- ERROR: SKIPPING BAD MARC INDEX $groupName > $fieldName > $subFieldName: '$marcIndex' -->\n";
                    continue;
                }
            } else {
                $marcIndex = intval($marcIndex);
            }
            $marcSubfieldName = trim(
                $field[self::$MARC_MAPPING_MARC_SUBFIELD]
            );

            $indicator1Condition = IndicatorCondition::buildIndicator1Condition(
                $field[self::$MARC_MAPPING_MARC_IND1]
            );
            $marcIndicator1 = empty($indicator1Condition)
                ? IndicatorCondition::$UNKNOWN_INDICATOR
                : $indicator1Condition->expectedValue;

            $indicator2Condition = IndicatorCondition::buildIndicator2Condition(
                $field[self::$MARC_MAPPING_MARC_IND2]
            );
            $marcIndicator2 = empty($indicator2Condition)
                ? IndicatorCondition::$UNKNOWN_INDICATOR
                : $indicator2Condition->expectedValue;

            $allFieldConditions = AbstractFieldCondition::buildAndCondition(
                $indicator1Condition, $indicator2Condition
            );
            $fieldCondition = ConstSubfieldCondition::parse(
                $field[self::$MARC_MAPPING_CONDITION], $marcIndicator1,
                $marcIndicator2
            );
            $hiddenMarcSubfield = null;
            // hide the condition marc subfield only if a user doesn't want to
            // render the condition marc subfield
            if (!empty($fieldCondition)
                && $subFieldName !== $fieldCondition->marcSubfieldName
            ) {
                $hiddenMarcSubfield = $fieldCondition->marcSubfieldName;
            }
            $allFieldConditions = AbstractFieldCondition::buildAndCondition(
                $allFieldConditions, $fieldCondition
            );

            // echo "<!-- MARC: $groupName > $fieldName > $subFieldName: $marcIndex/$marcSubfieldName/"
            //     . (empty($allFieldConditions) ? "TRUE"
            //         : $allFieldConditions->allConditionsToString())
            //     . "/$hiddenMarcSubfield -->\n";

            // calculate render type and mode ...
            $renderType = 'single';
            $formatterConfig = new FormatterConfig(null, []);
            $groupViewInfo = $detailViewFieldInfo->getGroup($groupName);
            $fieldGroupFormatter = null;
            $fieldViewInfo = null;

            if ($groupViewInfo) {
                $fieldViewInfo = $detailViewFieldInfo->getField(
                    $groupViewInfo, $fieldName, $marcIndex
                );
                if ($fieldViewInfo) {
                    $formatterConfig
                        = $detailViewFieldInfo->getFormatterConfig(
                        null, $fieldViewInfo
                    );
                    if ($detailViewFieldInfo->hasType($fieldViewInfo)) {
                        $renderType
                            = $detailViewFieldInfo->getType($fieldViewInfo);
                    }
                }
            }
            $fieldGroupFormatter
                = $detailViewFieldInfo->getFieldGroupFormatter(
                $groupViewInfo, $fieldName
            );
            // echo "<!-- SPECIAL: $groupName > $fieldName: rt=$renderType fc=" . $formatterConfig . " gc=" . $fieldGroupFormatter . " -->\n";

            if (!$renderGroupEntry
                && ($renderType === 'compound' || $renderType === 'sequences')
            ) {
                if ($renderType === 'compound') {
                    $renderGroupEntry = new CompoundEntry(
                        $groupName, $fieldName, $subFieldName, $marcIndex,
                        $formatterConfig, $marcIndicator1, $marcIndicator2,
                        $allFieldConditions
                    );
                }
                if ($renderType === 'sequences') {
                    $renderGroupEntry = new SequencesEntry(
                        $groupName, $fieldName, $subFieldName, $marcIndex,
                        $formatterConfig, $marcIndicator1, $marcIndicator2,
                        $allFieldConditions
                    );
                    if ($fieldViewInfo) {
                        $renderGroupEntry->setSequences(
                            $detailViewFieldInfo->getSubfieldSequences(
                                $fieldViewInfo
                            )
                        );
                    }
                }
                $renderGroupEntry->setFieldGroupFormatter($fieldGroupFormatter);
            }
            if ($renderType === 'single') {
                if ($renderGroupEntry) {
                    $this->finishField($renderGroup, $renderGroupEntry);
                }
                // field simply prints one value; do it line-by-line if multiple values exist
                $hadNoDefaultFormatter = $formatterConfig->formatterNameDefault
                    === null;
                if (empty($marcSubfieldName)) {
                    if ($hadNoDefaultFormatter) {
                        $formatterConfig->formatterNameDefault = "inline";
                    }
                    // $formatterConfig->repeatedDefault = true;
                    $formatterConfig->separatorDefault = "; ";
                    $renderGroupEntry = new CompoundEntry(
                        $groupName,
                        $fieldName,
                        $subFieldName,
                        $marcIndex,
                        $formatterConfig,
                        $marcIndicator1,
                        $marcIndicator2,
                        $allFieldConditions
                    );
                    if ($hiddenMarcSubfield !== null) {
                        $renderGroupEntry->addHiddenMarcSubfield(
                            $hiddenMarcSubfield
                        );
                    }
                    $renderGroup->addCompound($renderGroupEntry);
                    $renderGroupEntry->setFieldGroupFormatter(
                        $fieldGroupFormatter
                    );
                    $this->finishField($renderGroup, $renderGroupEntry);
                } else {
                    if ($hadNoDefaultFormatter) {
                        $formatterConfig->formatterNameDefault = "simple-line";
                    }
                    $renderGroupEntry = new SingleEntry(
                        $groupName,
                        $fieldName,
                        $subFieldName,
                        $marcIndex,
                        $formatterConfig,
                        $marcSubfieldName,
                        $marcIndicator1,
                        $marcIndicator2,
                        $allFieldConditions
                    );
                    $renderGroup->addSingle($renderGroupEntry);
                    $renderGroupEntry->setFieldGroupFormatter(
                        $fieldGroupFormatter
                    );
                    $renderGroupEntry = null;
                }
            } else {
                if ($renderType === 'compound' || $renderType === 'sequences') {
                    // TODO add $marcIndicator1/2? different to compound/sequences entry?
                    if ($renderType === 'compound'
                        || !empty($marcSubfieldName)
                    ) {
                        $renderGroupEntry->addElement(
                            $subFieldName, $marcSubfieldName
                        );
                        if ($hiddenMarcSubfield !== null) {
                            $renderGroupEntry->addHiddenMarcSubfield(
                                $hiddenMarcSubfield
                            );
                        }
                    }
                    // use all marc subfields ...
                    if (empty($marcSubfieldName)) {
                        $this->finishField($renderGroup, $renderGroupEntry);
                    }
                }
            }
        }
        $this->finishGroup($renderGroup, $renderGroupEntry);

        // uncomment this line in order to sort groups/fields by the order
        // defined in detail-view-field-structure.yaml; otherwise the order
        // in detail-fields.csv is used
        // $this->orderGroups();

        // $this->logger->log(Logger::ERR, "RC: " . $this->renderConfig);
        // echo "<!-- RC:\n " . $this->renderConfig . "-->\n";
    }

    /**
     * Helper method to finish the parsing of group of fields.
     *
     * @param RenderGroupConfig|null         $renderGroup      the group to "finish"
     * @param AbstractRenderConfigEntry|null $renderGroupEntry the field to "finish"
     *
     * @return void
     */
    protected function finishGroup(&$renderGroup, &$renderGroupEntry): void
    {
        if ($renderGroup) {
            $this->add($renderGroup);
            $this->finishField($renderGroup, $renderGroupEntry);
        }
    }

    /**
     * Helper method to finish the parsing of one field.
     *
     * @param RenderGroupConfig              $renderGroup      the field's group
     * @param AbstractRenderConfigEntry|null $renderGroupEntry the field to "finish"
     *
     * @return void
     */
    protected function finishField(&$renderGroup, &$renderGroupEntry): void
    {
        if ($renderGroupEntry) {
            if ($renderGroupEntry instanceof SequencesEntry) {
                // perhaps the csv contained some subfields, add the remaining from the sequences
                $renderGroupEntry->addSubfieldsFromSequences();
            }
            $renderGroup->addEntry($renderGroupEntry);
            $renderGroupEntry = null;
        }
    }

    /**
     * Get a field's value provider.
     *
     * @param string $groupName a field group's name
     * @param string $fieldName a field's name
     *
     * @return null|string
     */
    public function getValueProvider($groupName, $fieldName)
    {
        $groupView = $this->detailViewFieldInfo->getGroup($groupName);
        return $this->detailViewFieldInfo->getFieldValueProvider(
            $groupView, $fieldName
        );
    }

    /**
     * Belongs a field to group of fields (with different conditions, marc indexes)?
     *
     * @param string $groupName the group's name
     * @param string $fieldName the field's name
     *
     * @return bool
     */
    public function isMultiMarcField($groupName, $fieldName)
    {
        return $this->detailViewFieldInfo->isMultiMarcField(
            $groupName, $fieldName
        );
    }
}