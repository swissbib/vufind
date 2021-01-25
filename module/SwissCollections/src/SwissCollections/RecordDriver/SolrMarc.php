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
 * Enhanced record driver.
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
     * @return array array of subfield names to array of values
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
                        $v = trim($marcSubfield->getData());
                        $k = "" . $marcSubfield->getCode();
                        if (key_exists($k, $tempFieldData)) {
                            $tempFieldData[$k][] = $v;
                        } else {
                            $tempFieldData[$k] = [$v];
                        }
                    }
                }
            } else {
                if ($field instanceof \File_MARC_Control_Field) {
                    $tempFieldData["a"] = [$field->getData()];
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
    public function getDocumentTypes()
    {
        return $this->fields["format_str_mv"];
    }

    /**
     * Abstract method to get field values for marc fields and other value
     * providers.
     *
     * @param AbstractRenderConfigEntry $renderElem   the render element
     * @param RenderConfig              $renderConfig contains the provider information
     *
     * @return \File_MARC_Data_Field[]|\File_MARC_List
     */
    public function getFieldValues($renderElem, $renderConfig)
    {
        $valueProvider = $renderConfig->getValueProvider(
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