<?php
/**
 * SwissCollections: SolrMarc.php
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

use Laminas\Log\Logger;
use Swissbib\RecordDriver\SolrMarc as SwissbibSolrMarc;
use SwissCollections\RenderConfig\AbstractFieldCondition;
use SwissCollections\RenderConfig\CompoundEntry;
use SwissCollections\RenderConfig\ConstSubfieldCondition;
use SwissCollections\RenderConfig\FormatterConfig;
use SwissCollections\RenderConfig\IndicatorCondition;
use SwissCollections\RenderConfig\SequencesEntry;
use SwissCollections\RenderConfig\RenderConfig;
use SwissCollections\RenderConfig\AbstractRenderConfigEntry;
use SwissCollections\RenderConfig\RenderGroupConfig;
use SwissCollections\RenderConfig\SingleEntry;


/**
 * Enhanced record driver which parses "detail-fields.csv".
 *
 * @category SwissCollections_VuFind
 * @package  SwissCollections\RecordDriver
 * @author   Lionel Walter <lionel.walter@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://vufind.org/wiki/development Wiki
 */
class SolrMarc extends SwissbibSolrMarc
{
    /**
     * The information parsed from "detail-fields.csv".
     *
     * @var RenderConfig
     */
    protected $renderConfig;

    /**
     * The information read from "detail-view-field-structure.yaml".
     *
     * @var ViewFieldInfo
     */
    protected $detailViewFieldInfo;

    protected $fieldMarcMapping;

    public static $MARC_MAPPING_GROUP_KEY = 'Gruppierungsname / Oberbegriff';
    public static $MARC_MAPPING_FIELD_KEY = 'Bezeichnung';
    public static $MARC_MAPPING_SUBFIELD_KEY = 'Unterbezeichnung';
    public static $MARC_MAPPING_MARC_INDEX = 'datafield tag';
    public static $MARC_MAPPING_MARC_IND1 = 'datafield ind1';
    public static $MARC_MAPPING_MARC_IND2 = 'datafield ind2';
    public static $MARC_MAPPING_MARC_SUBFIELD = 'subfield code';
    public static $MARC_MAPPING_CONDITION = 'subfield match condition';

    /**
     * SolrMarc constructor.
     *
     * @param mixed $mainConfig           the main config
     * @param mixed $recordConfig         the record config
     * @param mixed $searchSettings       the search settings
     * @param mixed $holdingsHelper       the holdings helper
     * @param mixed $solrDefaultAdapter   the solr adapter
     * @param mixed $availabilityHelper   the availability helper
     * @param mixed $libraryNetworkLookup the network helper
     * @param mixed $logger               the logger
     */
    public function __construct(
        $mainConfig = null, $recordConfig = null,
        $searchSettings = null, $holdingsHelper = null,
        $solrDefaultAdapter = null,
        $availabilityHelper = null, $libraryNetworkLookup = null, $logger = null
    ) {
        parent::__construct(
            $mainConfig, $recordConfig, $searchSettings, $holdingsHelper,
            $solrDefaultAdapter,
            $availabilityHelper, $libraryNetworkLookup, $logger
        );
    }

    /**
     * Get the information from "detail-fields.csv".
     *
     * @return RenderGroupConfig[]
     */
    public function getRenderConfig()
    {
        return $this->renderConfig->entries();
    }

    /**
     * Delegates to parent's method.
     *
     * @param int $index the marc index
     *
     * @return \Swissbib\RecordDriver\Array[]
     */
    public function getMarcSubfieldsRaw($index)
    {
        return parent::getMarcSubfieldsRaw($index);
    }

    /**
     * Delegates to parent's method.
     *
     * @param int $index the marc index
     *
     * @return \File_MARC_Data_Field[]|\File_MARC_List
     */
    public function getMarcFields($index)
    {
        return parent::getMarcFields($index);
    }

    /**
     * Delegates to parent's method.
     *
     * @param int    $index        the marc index
     * @param string $subFieldCode the name of the subfield
     *
     * @return bool|String
     */
    public function getSimpleMarcSubFieldValue($index, $subFieldCode)
    {
        return parent::getSimpleMarcSubFieldValue($index, $subFieldCode);
    }

    /**
     * Returns a map of subfield values.
     *
     * @param int                         $index               the marc index
     * @param AbstractFieldCondition|null $fieldCondition      field's condition
     * @param string[]                    $hiddenMarcSubfields all hidden marc subfields
     *
     * @return array array of maps of marc subfield names to values
     * @throws \File_MARC_Exception
     */
    public function getMarcFieldsRawMap(
        int $index, $fieldCondition, $hiddenMarcSubfields
    ) {
        /**
         * Fields
         *
         * @var \File_MARC_Data_Field[] $fields
         */
        $fields = $this->getMarcRecord()->getFields($index);
        $fieldsData = [];

        foreach ($fields as $field) {
            $tempFieldData = $this->getMarcFieldRawMap(
                $field, $fieldCondition, $hiddenMarcSubfields
            );
            if (count($tempFieldData) > 0) {
                $fieldsData[] = $tempFieldData;
            }
        }

        return $fieldsData;
    }

    /**
     * Sets the information from "detail-view-field-structure.yaml".
     *
     * @param mixed $detailViewFieldInfo the data to set
     *
     * @return void
     */
    public function setDetailViewFieldInfo($detailViewFieldInfo)
    {
        $this->detailViewFieldInfo = new ViewFieldInfo($detailViewFieldInfo);
    }

    /**
     * Sets the information parsed from "detail-fields.csv".
     *
     * @param mixed $fieldMarcMapping the csv's lines
     *
     * @return void
     */
    public function setFieldMarcMapping($fieldMarcMapping)
    {
        $this->fieldMarcMapping = $fieldMarcMapping;
    }

    /**
     * Parse data read from "detail-fields.csv".
     *
     * @return void
     */
    public function buildRenderInfo()
    {
        //        echo "<!-- CSV:\n " . print_r($this->fieldMarcMapping, true) . "-->\n";
        //        echo "<!-- DI:\n " . print_r($this->detailViewFieldInfo, true) . "-->\n";

        $this->renderConfig = new RenderConfig();

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
        foreach ($this->fieldMarcMapping as $field) {
            $groupName = trim(
                $field[SolrMarc::$MARC_MAPPING_GROUP_KEY]
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
                $field[SolrMarc::$MARC_MAPPING_FIELD_KEY]
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
            $subFieldName = trim($field[SolrMarc::$MARC_MAPPING_SUBFIELD_KEY]);
            if (empty($subFieldName)) {
                if (empty($lastSubfieldName)) {
                    $lastSubfieldName = $fieldName;
                    $lastSubfieldCount = 0;
                }
                $lastSubfieldCount++;
                $subFieldName = $lastSubfieldName . $lastSubfieldCount;
            }

            $marcIndex = trim($field[SolrMarc::$MARC_MAPPING_MARC_INDEX]);
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
                $field[SolrMarc::$MARC_MAPPING_MARC_SUBFIELD]
            );

            $indicator1Condition = IndicatorCondition::buildIndicator1Condition(
                $field[SolrMarc::$MARC_MAPPING_MARC_IND1]
            );
            $marcIndicator1 = empty($indicator1Condition)
                ? IndicatorCondition::$UNKNOWN_INDICATOR
                : $indicator1Condition->expectedValue;

            $indicator2Condition = IndicatorCondition::buildIndicator2Condition(
                $field[SolrMarc::$MARC_MAPPING_MARC_IND2]
            );
            $marcIndicator2 = empty($indicator2Condition)
                ? IndicatorCondition::$UNKNOWN_INDICATOR
                : $indicator2Condition->expectedValue;

            $allFieldConditions = AbstractFieldCondition::buildAndCondition(
                $indicator1Condition, $indicator2Condition
            );
            $fieldCondition = ConstSubfieldCondition::parse(
                $field[SolrMarc::$MARC_MAPPING_CONDITION], $marcIndicator1,
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
            $groupViewInfo = $this->detailViewFieldInfo->getGroup($groupName);
            $fieldGroupFormatter = null;

            if ($groupViewInfo) {
                $fieldViewInfo = $this->detailViewFieldInfo->getField(
                    $groupViewInfo, $fieldName, $marcIndex
                );
                if ($fieldViewInfo) {
                    $formatterConfig
                        = $this->detailViewFieldInfo->getFormatterConfig(
                        null, $fieldViewInfo
                    );
                    if ($this->detailViewFieldInfo->hasType($fieldViewInfo)) {
                        $renderType
                            = $this->detailViewFieldInfo->getType(
                            $fieldViewInfo
                        );
                    }
                }
            }
            $fieldGroupFormatter
                = $this->detailViewFieldInfo->getFieldGroupFormatter(
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
                            $this->detailViewFieldInfo->getSubfieldSequences(
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
     * Sort groups by the order defined in "detail-view-field-structure.yaml".
     *
     * @return void
     */
    protected function orderGroups()
    {
        $this->renderConfig->orderGroups($this->detailViewFieldInfo);
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

    /**
     * Helper method to finish the parsing of group of fields.
     *
     * @param RenderGroupConfig|null         $renderGroup      the group to "finish"
     * @param AbstractRenderConfigEntry|null $renderGroupEntry the field to "finish"
     *
     * @return void
     */
    public function finishGroup(&$renderGroup, &$renderGroupEntry): void
    {
        if ($renderGroup) {
            $this->renderConfig->add($renderGroup);
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
    public function finishField(&$renderGroup, &$renderGroupEntry): void
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
     * Get the required data from the given marc field. Values are only used
     * if the element's condition is fulfilled.
     *
     * Be aware that this method may return a list of values if the marc field
     * contains several values.
     *
     * @param \File_MARC_Data_Field|\File_MARC_Control_Field $field the marc field
     * @param SingleEntry                                    $elem  contains the required field names
     *
     * @return null|SubfieldRenderData
     */
    public function getRenderFieldData($field, $elem)
    {
        try {
            if ($elem->checkCondition($field, $this)) {
                if ($field instanceof \File_MARC_Data_Field) {
                    $ind1 = IndicatorCondition::parse($field->getIndicator(1));
                    $ind2 = IndicatorCondition::parse($field->getIndicator(2));
                    $fieldMap = $elem->buildMap();
                    $fieldData = $this->getMappedFieldData(
                        $field, $fieldMap, true
                    );
                    $subfieldRenderData = new SubfieldRenderData(
                        $fieldData['value'], true, $ind1, $ind2
                    );
                } else {
                    if ($field instanceof \File_MARC_Control_Field) {
                        $subfieldRenderData = $this->buildGenericSubMap(
                            $field->getData(), true
                        );
                    } else {
                        echo "<!-- ERROR: Can't handle field type: "
                            . get_class(
                                $field
                            ) . " of " . $elem . " -->\n";
                        $subfieldRenderData = null;
                    }
                }
                // echo "<!-- GRFD: " . $elem->marcIndex . " " . $elem->getSubfieldName() . " " . print_r($subfieldRenderData, true) . " -->\n";
                if (!$this->isEmptyValue($subfieldRenderData)) {
                    return $subfieldRenderData;
                }
            }
            return null;
        } catch (\Throwable $exception) {
            echo "<!-- ERROR: Exception " . $exception->getMessage() . "\n"
                . $exception->getTraceAsString() . " -->\n";
        }
        return null;
    }

    /**
     * Factory method to build a new {@link SubfieldRenderData} instance
     * without indicator limitations.
     *
     * @param string $value      a subfield's value
     * @param bool   $escapeHtml if html escaping is required (if false the value is already html escaped)
     *
     * @return SubfieldRenderData
     */
    public function buildGenericSubMap($value, bool $escapeHtml
    ): SubfieldRenderData {
        return new SubfieldRenderData(
            $value,
            $escapeHtml,
            IndicatorCondition::$UNKNOWN_INDICATOR,
            IndicatorCondition::$UNKNOWN_INDICATOR
        );
    }

    /**
     * Checks if the given "value" is empty.
     *
     * @param SubfieldRenderData $subfieldRenderData the value to check
     *
     * @return bool
     */
    protected function isEmptyValue(SubfieldRenderData $subfieldRenderData
    ): bool {
        if (empty($subfieldRenderData)) {
            return false;
        }
        return $subfieldRenderData->emptyValue();
    }

    /**
     * Returns a map of subfield names to their values if the condition is
     * fulfilled.
     *
     * @param \File_MARC_Data_Field|\File_MARC_Control_Field $field               the marc field
     * @param AbstractFieldCondition|null                    $fieldCondition      the field's conditions
     * @param string[]                                       $hiddenMarcSubfields hidden marc subfields
     *
     * @return array array of subfield names to values
     */
    public function getMarcFieldRawMap(
        $field, $fieldCondition, $hiddenMarcSubfields
    ): array {
        $tempFieldData = [];

        if (empty($fieldCondition)
            || $fieldCondition->assertTrue($field, $this)
        ) {
            if ($field instanceof \File_MARC_Data_Field) {
                /**
                 * Subfields
                 *
                 * @var \File_MARC_Subfield[] $subfields
                 */
                $subfields = $field->getSubfields();
                foreach ($subfields as $marcSubfield) {
                    if (!in_array(
                        $marcSubfield->getCode(), $hiddenMarcSubfields
                    )
                    ) {
                        $tempFieldData["" . $marcSubfield->getCode()]
                            = $marcSubfield->getData();
                    }
                }
            } else {
                if ($field instanceof \File_MARC_Control_Field) {
                    $tempFieldData["a"] = $field->getData();
                } else {
                    echo "<!-- WARN (getMarcSubfieldsRawMap): Can't handle field type: "
                        . get_class($field) . " -->\n";
                }
            }
        }
        return $tempFieldData;
    }

    /**
     * Get all types of the document.
     *
     * @return string[]
     */
    protected function getDocumentTypes()
    {
        return $this->fields["format_str_mv"];
    }

    /**
     * Get a field's value provider.
     *
     * @param string $groupName a field group's name
     * @param string $fieldName a field's name
     *
     * @return null|string
     */
    protected function getValueProvider($groupName, $fieldName)
    {
        $groupView = $this->detailViewFieldInfo->getGroup($groupName);
        return $this->detailViewFieldInfo->getFieldValueProvider(
            $groupView, $fieldName
        );
    }

    /**
     * Abstract method to get field values for marc fields and other value
     * providers.
     *
     * @param AbstractRenderConfigEntry $renderElem the render element
     *
     * @return \File_MARC_Data_Field[]|\File_MARC_List
     */
    public function getFieldValues($renderElem)
    {
        $valueProvider = $this->getValueProvider(
            $renderElem->groupName, $renderElem->fieldName
        );
        if (empty($valueProvider)) {
            $fields = $this->getMarcFields($renderElem->marcIndex);
        } else {
            $fields = call_user_func_array(
                $valueProvider, array(&$renderElem, &$this)
            );
        }
        return $fields;
    }

    /**
     * Get the document type of a given render element. Fakes a marc field
     * with a marc subfield "a".
     *
     * @param AbstractRenderConfigEntry $renderElem the render element
     * @param SolrMarc                  $solrMarc   the render context
     *
     * @return \File_MARC_Data_Field[]|\File_MARC_List
     */
    public static function documentTypeProvider($renderElem, $solrMarc)
    {
        $docTypes = [];
        $types = $solrMarc->getDocumentTypes();
        if (!empty($types)) {
            foreach ($types as $t) {
                $subfield = new \File_MARC_Subfield('a', $t);
                $docTypes[] = new \File_MARC_Data_Field("000", [$subfield]);
            }
        }
        return $docTypes;
    }
}