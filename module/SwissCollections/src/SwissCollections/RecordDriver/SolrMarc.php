<?php


namespace SwissCollections\RecordDriver;

use Laminas\Log\Logger;
use Swissbib\RecordDriver\SolrMarc as SwissbibSolrMarc;
use SwissCollections\RenderConfig\CompoundEntry;
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

    /**
     * Quite similar to applyRenderer() except final html creation.
     * @param $marcIndex
     * @param AbstractRenderConfigEntry $rc
     * @return bool
     */
    public function isEmptyField($marcIndex, $rc) {
        $fields = $this->getMarcFields($marcIndex);
        $renderFieldData = null;
        if (!empty($fields)) {
            foreach ($fields as $field) {
                if ($rc instanceof SingleEntry) {
                    $renderFieldData = $this->getRenderFieldData($field, $rc);
                } else if ($rc instanceof CompoundEntry) {
                    // includes SequencesEntry objects too
                    foreach ($rc->elements as $elem) {
                        $sm = $this->getRenderFieldData($field, $elem);
                        if (!empty($sm)) {
                            $renderFieldData = $sm;
                            break 2;
                        }
                    }
                }
            }
        }
        return empty($renderFieldData);
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
            $groupViewInfo = $this->detailViewFieldInfo->getGroup($groupName);
            $renderType = 'single';
            $repeated = false;
            $fieldViewInfo = null;

            if ($groupViewInfo) {
                $fieldViewInfo = $this->detailViewFieldInfo->getField($groupViewInfo, $fieldName, $marcIndex);
                if ($fieldViewInfo) {
                    if ($this->detailViewFieldInfo->hasType($fieldViewInfo)) {
                        $renderType = $this->detailViewFieldInfo->getType($fieldViewInfo);
                    }
                    if ($this->detailViewFieldInfo->hasRepeated($fieldViewInfo)) {
                        $repeated = $this->detailViewFieldInfo->getRepeated($fieldViewInfo);
                    }
                }
            }
            // echo "<!-- SPECIAL: $groupName > $fieldName: rt=$renderType rm=$renderMode rep=$repeated -->\n";

            if (!$renderGroupEntry && ($renderType === 'compound' || $renderType === 'sequences')) {
                if ($renderType === 'compound') {
                    $renderGroupEntry = new CompoundEntry($subFieldName, $marcIndex, $marcIndicator1, $marcIndicator2);
                    if ($fieldViewInfo) {
                        $renderGroupEntry->setEntryOrder($this->detailViewFieldInfo->getSubfieldEntries($fieldViewInfo));
                    }
                    $this->setLineMode($fieldViewInfo, $renderGroupEntry, 'line');
                    $renderGroupEntry->repeated = $repeated;
                }
                if ($renderType === 'sequences') {
                    $renderGroupEntry = new SequencesEntry($subFieldName, $marcIndex, $marcIndicator1, $marcIndicator2);
                    if ($fieldViewInfo) {
                        $renderGroupEntry->setEntryOrder($this->detailViewFieldInfo->getSubfieldEntries($fieldViewInfo));
                        $renderGroupEntry->setSequences($this->detailViewFieldInfo->getSubfieldSequences($fieldViewInfo));
                    }
                    $this->setLineMode($fieldViewInfo, $renderGroupEntry, 'inline');
                }
                $renderGroupEntry->setFieldViewInfo($fieldViewInfo);
            }
            if ($renderType === 'single') {
                if ($renderGroupEntry) {
                    $this->finishField($renderGroup, $renderGroupEntry);
                }
                $renderGroupEntry = new SingleEntry(
                    $subFieldName,
                    $marcIndex,
                    $marcSubfieldName,
                    $marcIndicator1,
                    $marcIndicator2);
                $renderGroupEntry->repeated = $repeated;
                $this->setLineMode($fieldViewInfo, $renderGroupEntry, 'line');
                $renderGroup->addSingle($renderGroupEntry);
                $renderGroupEntry = null;
            } else if ($renderType === 'compound' || $renderType === 'sequences') {
                // TODO add $marcIndicator1/2? different to compound/sequences entry?
                if ($renderType === 'compound' || !empty($marcSubfieldName)) {
                    $renderGroupEntry->addElement($subFieldName, $marcSubfieldName);
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
                // perhaps the csv contained some subfields, at the remaining from the sequences
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
     * @param AbstractRenderConfigEntry $elem
     * @return bool if OK
     */
    public function checkIndicators($field, $elem): bool {
        try {
            $ind1 = $this->normalizeIndicator($field->getIndicator(1));
            $ind2 = $this->normalizeIndicator($field->getIndicator(2));
            // match only if indicator was specified in csv file (-1 == undefined)
            if (($elem->indicator1 >= 0 && $ind1 !== $elem->indicator1)
                || ($elem->indicator2 >= 0 && $ind2 !== $elem->indicator2)) {
                echo "<!-- WARN: INDICATOR MISMATCH $elem, $ind1/$ind2 -->\n";
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
                if (!$this->checkIndicators($field, $elem)) {
                    return null;
                }
                $ind1 = $this->normalizeIndicator($field->getIndicator(1));
                $ind2 = $this->normalizeIndicator($field->getIndicator(2));
                $fieldMap = $elem->buildMap();
                if (count($fieldMap) === 0) {
                    $rawData = $this->getMarcSubfieldsRaw($elem->marcIndex);
                    // echo "<!-- RAW " . $elem->marcIndex . ": " . print_r($rawData, true) . " -->\n";
                    // ignore indicators for raw output in $subfieldRenderData
                    $subfieldRenderData = $this->buildGenericSubMap($this->mergeRawData($elem->marcIndex, $rawData, $ind1, $ind2), FALSE);
                } else {
                    $fieldData = $this->getMappedFieldData($field, $fieldMap, TRUE);
                    $subfieldRenderData = new SubfieldRenderData($fieldData['value'], TRUE, $ind1, $ind2);
                }
            } else if ($field instanceof \File_MARC_Control_Field) {
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

    protected function normalizeIndicator(String $ind): int {
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

    protected function setLineMode($fieldViewInfo, AbstractRenderConfigEntry $renderGroupEntry, String $default): void {
        $renderMode = $default;
        if ($fieldViewInfo && $this->detailViewFieldInfo->hasMode($fieldViewInfo)) {
            $renderMode = $this->detailViewFieldInfo->getMode($fieldViewInfo);
        }
        $renderGroupEntry->setRenderMode($renderMode);
    }
}