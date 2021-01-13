<?php


namespace SwissCollections\RecordDriver;

use Laminas\Log\Logger;
use Swissbib\RecordDriver\SolrMarc as SwissbibSolrMarc;
use SwissCollections\RenderConfig\CompoundEntry;
use SwissCollections\RenderConfig\FormatterConfig;
use SwissCollections\RenderConfig\SequencesEntry;
use SwissCollections\RenderConfig\RenderConfig;
use SwissCollections\RenderConfig\AbstractRenderConfigEntry;
use SwissCollections\RenderConfig\RenderGroupConfig;
use SwissCollections\RenderConfig\SingleEntry;


/**
 * Class SolrMarc
 * @package SwissCollections\RecordDriver
 */
class SolrMarc extends SwissbibSolrMarc {
    /**
     * @var RenderConfig
     */
    protected $renderConfig;

    /**
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

    public function __construct($mainConfig = null, $recordConfig = null,
                                $searchSettings = null, $holdingsHelper = null, $solrDefaultAdapter = null,
                                $availabilityHelper = null, $libraryNetworkLookup = null, $logger = null
    ) {
        parent::__construct($mainConfig, $recordConfig, $searchSettings, $holdingsHelper, $solrDefaultAdapter,
            $availabilityHelper, $libraryNetworkLookup, $logger);
    }

    /**
     * @return RenderGroupConfig[]
     */
    public function getRenderConfig() {
        return $this->renderConfig->entries();
    }

    public function getMarcSubfieldsRaw($index) {
        return parent::getMarcSubfieldsRaw($index);
    }

    public function getMarcFields($index) {
        return parent::getMarcFields($index);
    }

    public function getSimpleMarcSubFieldValue($index, $subFieldCode) {
        return parent::getSimpleMarcSubFieldValue($index, $subFieldCode);
    }

    /**
     * @param int $index
     * @param int $indicator1 - required indicator
     * @param int $indicator2 - required indicator
     * @return array array of maps of marc subfield names to values
     * @throws \File_MARC_Exception
     */
    public function getMarcFieldsRawMap(int $index, int $indicator1, int $indicator2) {
        /**
         * Fields
         *
         * @var \File_MARC_Data_Field[] $fields
         */
        $fields = $this->getMarcRecord()->getFields($index);
        $fieldsData = [];

        foreach ($fields as $field) {
            $tempFieldData = $this->getMarcFieldRawMap($field, $indicator1, $indicator2);
            if (count($tempFieldData) > 0) {
                $fieldsData[] = $tempFieldData;
            }
        }

        return $fieldsData;
    }

    /**
     * Quite similar to applyRenderer() except final html creation.
     * @param $marcIndex
     * @param AbstractRenderConfigEntry $rc
     * @return bool
     */
    public function isEmptyField($marcIndex, $rc) {
        $fields = $this->getMarcFields($marcIndex);
        if (!empty($fields)) {
            foreach ($fields as $field) {
                if ($rc->hasRenderData($field, $this)) {
                    return false;
                }
            }
        }
        return true;
    }

    public function setDetailViewFieldInfo($detailViewFieldInfo) {
        $this->detailViewFieldInfo = new ViewFieldInfo($detailViewFieldInfo);
    }

    public function setFieldMarcMapping($fieldMarcMapping) {
        $this->fieldMarcMapping = $fieldMarcMapping;
    }

    public function buildRenderInfo() {
//        echo "<!-- CSV:\n " . print_r($this->fieldMarcMapping, true) . "-->\n";
//        echo "<!-- DI:\n " . print_r($this->detailViewFieldInfo, true) . "-->\n";

        $this->renderConfig = new RenderConfig();

        /**
         * @var RenderGroupConfig
         */
        $renderGroup = null;
        /**
         * @var AbstractRenderConfigEntry
         */
        $renderGroupEntry = null;
        $lastGroupName = "";
        $lastFieldName = "";
        $lastSubfieldName = "";
        $lastSubfieldCount = 0;
        foreach ($this->fieldMarcMapping as $field) {
            $groupName = $field[SolrMarc::$MARC_MAPPING_GROUP_KEY]; // always non empty
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

            $fieldName = $field[SolrMarc::$MARC_MAPPING_FIELD_KEY]; // always non empty
            if ($fieldName !== $lastFieldName) {
                $lastSubfieldName = "";
                $lastSubfieldCount = 0;
                if (!empty($lastFieldName)) {
                    $this->finishField($renderGroup, $renderGroupEntry);
                }
                $lastFieldName = $fieldName;
            }

            // calculate sub field name (may be missing)
            $subFieldName = $field[SolrMarc::$MARC_MAPPING_SUBFIELD_KEY];
            if (empty($subFieldName)) {
                if (empty($lastSubfieldName)) {
                    $lastSubfieldName = $fieldName;
                    $lastSubfieldCount = 0;
                }
                $lastSubfieldCount++;
                $subFieldName = $lastSubfieldName . $lastSubfieldCount;
            }
            $labelKey = $groupName . "." . $subFieldName;

            $marcIndex = $field[SolrMarc::$MARC_MAPPING_MARC_INDEX];
            if (!ctype_digit($marcIndex)) {
                echo "<!-- ERROR: SKIPPING BAD MARC INDEX $groupName > $fieldName > $subFieldName: '$marcIndex' -->\n";
                continue;
            }
            $marcIndex = intval($marcIndex);
            $marcSubfieldName = $field[SolrMarc::$MARC_MAPPING_MARC_SUBFIELD];
            $marcIndicator1Str = $field[SolrMarc::$MARC_MAPPING_MARC_IND1];
            $marcIndicator2Str = $field[SolrMarc::$MARC_MAPPING_MARC_IND2];
            $marcIndicator1 = $this->parseIndicator($marcIndicator1Str, $groupName, $fieldName);
            $marcIndicator2 = $this->parseIndicator($marcIndicator2Str, $groupName, $fieldName);

            // echo "<!-- MARC: $groupName > $fieldName > $subFieldName: $marcIndex|$marcIndicator1|$marcIndicator2|$marcSubfieldName -->\n";

            // calculate render type and mode ...
            $renderType = 'single';
            $formatterConfig = new FormatterConfig(null, []);
            $groupViewInfo = $this->detailViewFieldInfo->getGroup($groupName);
            $fieldGroupFormatter = null;

            if ($groupViewInfo) {
                $fieldViewInfo = $this->detailViewFieldInfo->getField($groupViewInfo, $fieldName, $marcIndex);
                if ($fieldViewInfo) {
                    $formatterConfig = $this->detailViewFieldInfo->getFormatterConfig(null, $fieldViewInfo);
                    if ($this->detailViewFieldInfo->hasType($fieldViewInfo)) {
                        $renderType = $this->detailViewFieldInfo->getType($fieldViewInfo);
                    }
                }
            }
            $fieldGroupFormatter = $this->detailViewFieldInfo->getFieldGroupFormatter($groupViewInfo, $fieldName);
            // echo "<!-- SPECIAL: $groupName > $fieldName: rt=$renderType fc=" . $formatterConfig . " gc=" . $fieldGroupFormatter . " -->\n";

            if (!$renderGroupEntry && ($renderType === 'compound' || $renderType === 'sequences')) {
                if ($renderType === 'compound') {
                    $renderGroupEntry = new CompoundEntry($fieldName, $subFieldName, $labelKey, $marcIndex, $formatterConfig, $marcIndicator1, $marcIndicator2);
                }
                if ($renderType === 'sequences') {
                    $renderGroupEntry = new SequencesEntry($fieldName, $subFieldName, $labelKey, $marcIndex, $formatterConfig, $marcIndicator1, $marcIndicator2);
                    if ($fieldViewInfo) {
                        $renderGroupEntry->setSequences($this->detailViewFieldInfo->getSubfieldSequences($fieldViewInfo));
                    }
                }
                $renderGroupEntry->setFieldGroupFormatter($fieldGroupFormatter);
            }
            if ($renderType === 'single') {
                if ($renderGroupEntry) {
                    $this->finishField($renderGroup, $renderGroupEntry);
                }
                // field simply prints one value; do it line-by-line if multiple values exist
                $hadNoDefaultFormatter = $formatterConfig->formatterNameDefault === null;
                if (empty($marcSubfieldName)) {
                    if ($hadNoDefaultFormatter) {
                        $formatterConfig->formatterNameDefault = "inline";
                    }
                    // $formatterConfig->repeatedDefault = true;
                    $formatterConfig->separatorDefault = "; ";
                    $renderGroupEntry = new CompoundEntry(
                        $fieldName,
                        $subFieldName,
                        $labelKey,
                        $marcIndex,
                        $formatterConfig,
                        $marcIndicator1,
                        $marcIndicator2);
                    $renderGroup->addCompound($renderGroupEntry);
                    $renderGroupEntry->setFieldGroupFormatter($fieldGroupFormatter);
                    $this->finishField($renderGroup, $renderGroupEntry);
                } else {
                    if ($hadNoDefaultFormatter) {
                        $formatterConfig->formatterNameDefault = "simple-line";
                    }
                    $renderGroupEntry = new SingleEntry(
                        $fieldName,
                        $subFieldName,
                        $labelKey,
                        $marcIndex,
                        $formatterConfig,
                        $marcSubfieldName,
                        $marcIndicator1,
                        $marcIndicator2);
                    $renderGroup->addSingle($renderGroupEntry);
                    $renderGroupEntry->setFieldGroupFormatter($fieldGroupFormatter);
                    $renderGroupEntry = null;
                }
            } else if ($renderType === 'compound' || $renderType === 'sequences') {
                // TODO add $marcIndicator1/2? different to compound/sequences entry?
                if ($renderType === 'compound' || !empty($marcSubfieldName)) {
                    $renderGroupEntry->addElement($labelKey, $marcSubfieldName);
                }
                // use all marc subfields ...
                if (empty($marcSubfieldName)) {
                    $this->finishField($renderGroup, $renderGroupEntry);
                }
            }
        }
        $this->finishGroup($renderGroup, $renderGroupEntry);

        $this->orderGroups();
        // $this->logger->log(Logger::ERR, "RC: " . $this->renderConfig);
        // echo "<!-- RC:\n " . $this->renderConfig . "-->\n";
    }

    protected function orderGroups() {
        $this->renderConfig->orderGroups($this->detailViewFieldInfo);
    }

    /**
     * @param RenderGroupConfig|null $renderGroup
     * @param AbstractRenderConfigEntry|null $renderGroupEntry
     */
    public function finishGroup(&$renderGroup, &$renderGroupEntry): void {
        if ($renderGroup) {
            $this->renderConfig->add($renderGroup);
            $this->finishField($renderGroup, $renderGroupEntry);
        }
    }

    /**
     * @param RenderGroupConfig $renderGroup
     * @param AbstractRenderConfigEntry|null $renderGroupEntry
     */
    public function finishField(&$renderGroup, &$renderGroupEntry): void {
        if ($renderGroupEntry) {
            if ($renderGroupEntry instanceof SequencesEntry) {
                // perhaps the csv contained some subfields, add the remaining from the sequences
                $renderGroupEntry->addSubfieldsFromSequences();
            }
            $renderGroup->addEntry($renderGroupEntry);
            $renderGroupEntry = null;
        }
    }

    protected function parseIndicator(String $s, $groupName, $fieldName) {
        $s = trim($s);
        if (strlen($s) === 0) {
            return AbstractRenderConfigEntry::$UNKNOWN_INDICATOR;
        }
        if (!ctype_digit($s)) {
            echo "<!-- ERROR: SKIPPING BAD MARC INDICATOR $groupName > $fieldName: '$s' -->\n";
            return AbstractRenderConfigEntry::$UNKNOWN_INDICATOR;
        }
        return intval($s);
    }

    /**
     * @param \File_MARC_Data_Field|\File_MARC_Control_Field $field
     * @param int $indicator1 required indicator
     * @param int $indicator2 required indicator
     * @return bool if OK
     */
    public function checkIndicators($field, $indicator1, $indicator2): bool {
        try {
            $ind1 = $this->normalizeIndicator($field->getIndicator(1));
            $ind2 = $this->normalizeIndicator($field->getIndicator(2));
            // match only if indicator was specified in csv file (-1 == undefined)
            if (($indicator1 >= 0 && $ind1 !== $indicator1)
                || ($indicator2 >= 0 && $ind2 !== $indicator2)) {
                echo "<!-- WARN: INDICATOR MISMATCH needed: $indicator1|$indicator2, got: $ind1|$ind2 -->\n";
                return FALSE;
            }
        } catch (\Throwable $exception) {
            echo "<!-- ERROR: Exception " . $exception->getMessage() . "\n" . $exception->getTraceAsString() . " -->\n";
            return FALSE;
        }
        return TRUE;
    }

    /**
     * @param \File_MARC_Data_Field|\File_MARC_Control_Field $field
     * @param SingleEntry $elem
     * @return null|SubfieldRenderData
     */
    public function getRenderFieldData($field, $elem) {
        try {
            if ($field instanceof \File_MARC_Data_Field) {
                if (!$this->checkIndicators($field, $elem->indicator1, $elem->indicator2)) {
                    return null;
                }
                $ind1 = $this->normalizeIndicator($field->getIndicator(1));
                $ind2 = $this->normalizeIndicator($field->getIndicator(2));
                $fieldMap = $elem->buildMap();
                $fieldData = $this->getMappedFieldData($field, $fieldMap, TRUE);
                $subfieldRenderData = new SubfieldRenderData($fieldData['value'], TRUE, $ind1, $ind2);
            } else if ($field instanceof \File_MARC_Control_Field) {
                if (!($elem->indicator1 === AbstractRenderConfigEntry::$UNKNOWN_INDICATOR
                    && $elem->indicator2 === AbstractRenderConfigEntry::$UNKNOWN_INDICATOR)) {
                    return null;
                }
                $subfieldRenderData = $this->buildGenericSubMap($field->getData(), TRUE);
            } else {
                echo "<!-- ERROR: Can't handle field type: " . get_class($field) . " of " . $elem . " -->\n";
                $subfieldRenderData = null;
            }
            // echo "<!-- GRFD: " . $elem->marcIndex . " " . $elem->getSubfieldName() . " " . print_r($subfieldRenderData, true) . " -->\n";
            if (!$this->isEmptyValue($subfieldRenderData)) {
                return $subfieldRenderData;
            }
            return null;
        } catch (\Throwable $exception) {
            echo "<!-- ERROR: Exception " . $exception->getMessage() . "\n" . $exception->getTraceAsString() . " -->\n";
        }
        return null;
    }

    public function buildGenericSubMap($value, bool $escapeHtml): SubfieldRenderData {
        return new SubfieldRenderData(
            $value,
            $escapeHtml,
            AbstractRenderConfigEntry::$UNKNOWN_INDICATOR,
            AbstractRenderConfigEntry::$UNKNOWN_INDICATOR);
    }

    public function normalizeIndicator(String $ind): int {
        $ind = trim($ind);
        if (strlen($ind) === 0 || !ctype_digit($ind)) {
            return AbstractRenderConfigEntry::$UNKNOWN_INDICATOR;
        }
        return intval($ind);
    }

    protected function isEmptyValue(SubfieldRenderData $subfieldRenderData): bool {
        if (empty($subfieldRenderData)) {
            return false;
        }
        return $subfieldRenderData->emptyValue();
    }

    public function isEmptyGroup(RenderGroupConfig $renderGroupConfig): bool {
        $groupIsEmpty = true;
        foreach ($renderGroupConfig->entries() as $renderElem) {
            if (!$this->isEmptyField($renderElem->marcIndex, $renderElem)) {
                $groupIsEmpty = false;
                break;
            }
        }
        return $groupIsEmpty;
    }

    /**
     * @param $field
     * @param int $indicator1
     * @param int $indicator2
     * @return array
     */
    public function getMarcFieldRawMap($field, int $indicator1, int $indicator2): array {
        $tempFieldData = [];

        if ($field instanceof \File_MARC_Data_Field) {
            if ($this->checkIndicators($field, $indicator1, $indicator2)) {
                /**
                 * Subfields
                 *
                 * @var \File_MARC_Subfield[] $subfields
                 */
                $subfields = $field->getSubfields();
                foreach ($subfields as $subfield) {
                    $tempFieldData["" . $subfield->getCode()] = $subfield->getData();
                }
            }
        } else if ($field instanceof \File_MARC_Control_Field) {
            // only if no indicator limitation is expected ...
            if ($indicator1 === AbstractRenderConfigEntry::$UNKNOWN_INDICATOR
                && $indicator2 === AbstractRenderConfigEntry::$UNKNOWN_INDICATOR) {
                $tempFieldData["a"] = $field->getData();
            }
        } else {
            echo "<!-- WARN (getMarcSubfieldsRawMap): Can't handle field type: " . get_class($field) . " -->\n";
        }
        return $tempFieldData;
    }

    /**
     * Helper method to output raw marc field.
     * @param int $marcIndex
     * @param $rawData
     * @param int $ind1
     * @param int $ind2
     * @return string
     */
    protected function mergeRawData(int $marcIndex, $rawData, int $ind1, int $ind2) {
        $ind1Str = "" . $ind1;
        $ind2Str = "" . $ind2;
        if ($ind1 < 0) {
            $ind1Str = "";
        }
        if ($ind2 < 0) {
            $ind2Str = "";
        }
        $result = "<b style='color: #ff888c;'>RAW</b> $marcIndex [$ind1Str|$ind2Str] <ul>";
        foreach ($rawData as $entry) {
            /** @noinspection CssUnknownTarget */
            $result .= "<li style='list-style: none; background: url(\"/themes/bootprint3/images/icons/arrow_right.png\") no-repeat 0 2px; padding-left: 20px;'><ul style='padding-left: 10px;'>";
            foreach ($entry as $innerEntry) {
                $subfieldName = $innerEntry['tag'];
                $subfieldValue = $innerEntry['data'];
                if (!empty($subfieldValue)) {
                    $result .= "<li style='list-style: disc;'><b>" . $subfieldName . "</b>: " . htmlspecialchars($subfieldValue) . "</li>";
                }
            }
            $result .= "</ul></li>";
        }
        return $result . "</ul>";
    }

}