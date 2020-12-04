<?php


namespace SwissCollections\RecordDriver;

use Laminas\Log\Logger;
use Swissbib\RecordDriver\SolrMarc as SwissbibSolrMarc;
use SwissCollections\RenderConfig\CompoundEntry;
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

    public static $RENDER_INFO_FIELD_TYPE = "type";
    public static $RENDER_INFO_FIELD_MODE = "mode";
    public static $RENDER_INFO_FIELD_SUBFIELDS = "entries";

    public function __construct($mainConfig = null, $recordConfig = null,
                                $searchSettings = null, $holdingsHelper = null, $solrDefaultAdapter = null,
                                $availabilityHelper = null, $libraryNetworkLookup = null, $logger = null
    ) {
        parent::__construct($mainConfig, $recordConfig, $searchSettings, $holdingsHelper, $solrDefaultAdapter,
            $availabilityHelper, $libraryNetworkLookup, $logger);
    }

    public function getShortTitle() {
        // $this->logger->log(Logger::ERR, "Hallo1!");
        // TODO Remove (proof of concept)
        return parent::getShortTitle();
    }

    /**
     * @return RenderGroupConfig[]
     */
    public function getRenderConfig() {
        return $this->renderConfig->entries();
    }

    /**
     * @param int $marcIndex
     * @param CompoundEntry|SingleEntry $rc
     * @param Callable $renderer called with $subMap, $marcField, CompoundEntry|SingleEntry, $isFirst, $isLast
     */
    public function applyRenderer($marcIndex, $rc, $renderer) {
        $field = $this->getMarcField($marcIndex);
        // echo "<!-- AR:\n " . $marcIndex . "\n" . print_r($field, true) . " -->\n";
        if (!empty($field)) {
//            echo "<!-- FIELD CLASS " . get_class($field) . " -->\n";
            if ($rc instanceof SingleEntry) {
                $subMap = $this->renderField($field, $rc);
                if (!empty($subMap) && !empty($subMap['value'])) {
                    $renderer($subMap, $field, $rc, true, true);
                }
            } else if ($rc instanceof CompoundEntry) {
                $array = $rc->elements;
                $values = [];
                // filter out empty values
                foreach ($array as $elem) {
                    $subMap = $this->renderField($field, $elem);
                    if (!empty($subMap) && !empty($subMap['value'])) {
                        $values[] = ['subMap' => $subMap, 'renderConfig' => $elem];
                    }
                }
                foreach ($values as $key => $v) {
                    $isFirst = $key === array_key_first($values);
                    $isLast = $key === array_key_last($values);
                    $renderer($v['subMap'], $field, $v['renderConfig'], $isFirst, $isLast);
                }
            }
        }
    }

    /**
     * @param $marcIndex
     * @return bool
     */
    public function isEmptyField($marcIndex) {
        $field = $this->getMarcField($marcIndex);
        return empty($field);
    }

    public function setDetailViewFieldInfo($detailViewFieldInfo) {
        $this->detailViewFieldInfo = $detailViewFieldInfo;
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
         * @var CompoundEntry|SingleEntry
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

            // calculate render type and mode ...
            $groupViewInfo = $this->detailViewFieldInfo['structure'][$groupName];
            $renderType = 'single';
            $renderMode = '';
            if ($groupViewInfo) {
                $fieldViewInfo = $groupViewInfo[$fieldName];
                if ($fieldViewInfo && in_array($subFieldName, $fieldViewInfo[SolrMarc::$RENDER_INFO_FIELD_SUBFIELDS])) {
                    $renderType = $fieldViewInfo[SolrMarc::$RENDER_INFO_FIELD_TYPE];
                    $renderMode = $fieldViewInfo[SolrMarc::$RENDER_INFO_FIELD_MODE];
                }
            }

            $marcIndex = $field[SolrMarc::$MARC_MAPPING_MARC_INDEX];
            $marcSubfieldName = $field[SolrMarc::$MARC_MAPPING_MARC_SUBFIELD];
            $marcIndicator1 = $field[SolrMarc::$MARC_MAPPING_MARC_IND1];
            $marcIndicator2 = $field[SolrMarc::$MARC_MAPPING_MARC_IND2];
            // TODO handle condition

            if (!is_numeric($marcIndex)) {
                echo "<!-- SKIPPING BAD MARC INDEX: $groupName > $fieldName > $subFieldName: '$marcIndex' -->\n";
                continue;
            }
            $marcIndex = intval($marcIndex);
            $marcIndicator1 = $this->parseIndicator($marcIndicator1, $groupName, $fieldName);
            $marcIndicator2 = $this->parseIndicator($marcIndicator2, $groupName, $fieldName);

            // echo "<!-- MARC: $groupName > $fieldName > $subFieldName: $marcIndex|$marcIndicator1|$marcIndicator2|$marcSubfieldName -->\n";
            if (!$renderGroupEntry && $renderType === 'compound') {
                $renderGroupEntry = new CompoundEntry($subFieldName, $marcIndex, $marcIndicator1, $marcIndicator2);
                if ($renderMode === AbstractRenderConfigEntry::$RENDER_MODE_INLINE) {
                    $renderGroupEntry->setInlineRenderMode();
                } else {
                    // default render mode
                    $renderGroupEntry->setLineRenderMode();
                }
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
                $renderGroup->addSingle($renderGroupEntry);
                $renderGroupEntry = null;
            } else if ($renderType === 'compound') {
                // TODO add $marcIndicator1/2? different to compound entry?
                $renderGroupEntry->addElement($subFieldName, $marcSubfieldName);
            }
        }
        $this->finishGroup($renderGroup, $renderGroupEntry);

        $this->orderGroups();
        // $this->logger->log(Logger::ERR, "RC: " . $this->renderConfig);
        // echo "<!-- RC:\n " . $this->renderConfig . "-->\n";
    }

    protected function orderGroups() {
        $groupOrder = array_keys($this->detailViewFieldInfo['structure']);
        $fieldOrderProvider = function (String $groupName) {
            $fields = $this->detailViewFieldInfo['structure'][$groupName];
            if (empty($fields)) {
                return [];
            }
            return array_keys($fields);
        };
        $subfieldOrderProvider = function (String $groupName, String $fieldName) {
            $fields = $this->detailViewFieldInfo['structure'][$groupName];
            if (empty($fields)) {
                return [];
            }
            $subfield = $fields[$fieldName];
            if (empty($subfield)) {
                return [];
            }
            $entries = $subfield['entries'];
            if (empty($subfield)) {
                return [];
            }
            return $entries;
        };
        $this->renderConfig->orderGroups($groupOrder, $fieldOrderProvider, $subfieldOrderProvider);
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
            $renderGroup->addEntry($renderGroupEntry);
            $renderGroupEntry = null;
        }
    }

    protected function parseIndicator(String $s, $groupName, $fieldName) {
        if (empty($s)) {
            return AbstractRenderConfigEntry::$UNKNOWN_INDICATOR;
        }
        if (!is_numeric($s)) {
            echo "<!-- SKIPPING BAD MARC INDICATOR: $groupName > $fieldName: '$s' -->\n";
            return AbstractRenderConfigEntry::$UNKNOWN_INDICATOR;
        }
        return intval($s);
    }

    /**
     * @param \File_MARC_Data_Field|\File_MARC_Control_Field $field
     * @param SingleEntry $elem
     * @return null|array
     */
    public function renderField($field, $elem) {
        try {
            $getIndicatorsAndSubfields = $field instanceof \File_MARC_Data_Field;
            if ($getIndicatorsAndSubfields) {
                $subMap = $this->getMappedFieldData($field, $elem->buildMap(), $getIndicatorsAndSubfields);
            } else if ($field instanceof \File_MARC_Control_Field) {
                $subMap = ['value' => $field->getData()];
            } else {
                echo "<!-- UNKNOWN: Can't handle field type: " . get_class($field) . " of " . $elem . " -->\n";
                $subMap = null;
            }
            if (!$this->isEmptyValue($subMap)) {
                echo "<!-- VALUE: " . $subMap['value'] . " " . $elem . " -->";
                return $subMap;
            }
            return null;
        } catch (\Throwable $exception) {
            echo "<!-- FAIL: " . $exception->getMessage() . "\n" . $exception->getTraceAsString() . " -->\n";
        }
        return null;
    }

    protected function isEmptyValue($subMap): bool {
        if (empty($subMap)) {
            return true;
        }
        $v = $subMap['value'];
        if (empty($v)) {
            return true;
        }
        return empty(trim("" . $v));
    }
}