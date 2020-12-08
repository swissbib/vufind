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
    public static $RENDER_INFO_FIELD_REPEATED = "repeated";
    public static $RENDER_INFO_FIELD_SUBFIELDS = "entries";

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

    /**
     * @param int $marcIndex
     * @param CompoundEntry|SingleEntry $rc
     * @param Callable $renderer called with $subMap, $marcField, CompoundEntry|SingleEntry, $isFirst, $isLast, $firstListEntry, $lastListEntry
     */
    public function applyRenderer($marcIndex, $rc, $renderer) {
        $fields = $this->getMarcFields($marcIndex);
        if (!empty($fields)) {
            $processedSubMaps = [];
            foreach ($fields as $fieldIndex => $field) {
                $firstListEntry = $fieldIndex === 0;
                $lastListEntry = ($fieldIndex + 1) === count($fields);
                // echo "<!-- FIELD CLASS " . get_class($field) . " fl=$firstListEntry ll=$lastListEntry rc=$rc -->\n";
                if ($rc instanceof SingleEntry) {
                    $subMap = $this->renderField($field, $rc);
                    if (!empty($subMap) && !$this->alreadyProcessed($processedSubMaps, $subMap)) {
                        $renderer($subMap, $field, $rc, true, true, $firstListEntry, $lastListEntry);
                        $processedSubMaps[] = $subMap;
                    }
                } else if ($rc instanceof CompoundEntry) {
                    $array = $rc->elements;
                    $values = [];
                    $subMaps = [];
                    // filter out empty values
                    foreach ($array as $elem) {
                        $subMap = $this->renderField($field, $elem);
                        if (!empty($subMap)) {
                            $values[] = ['subMap' => $subMap, 'renderConfig' => $elem];
                            $subMaps[] = $subMap;
                        }
                    }
                    if (!$this->alreadyProcessed($processedSubMaps, $subMaps)) {
                        foreach ($values as $key => $v) {
                            $isFirst = $key === array_key_first($values);
                            $isLast = $key === array_key_last($values);
                            $renderer($v['subMap'], $field, $v['renderConfig'], $isFirst, $isLast, $firstListEntry, $lastListEntry);
                        }
                        $processedSubMaps[] = $subMaps;
                    }
                }
            }
        }
    }

    protected function alreadyProcessed(&$processed, $candidate): bool {
        return in_array($candidate, $processed, true);
    }

    /**
     * Quite similar to applyRenderer() except final html creation.
     * @param $marcIndex
     * @param (\SwissCollections\RenderConfig\CompoundEntry|\SwissCollections\RenderConfig\SingleEntry)[] $rc
     * @return bool
     */
    public function isEmptyField($marcIndex, $rc) {
        $fields = $this->getMarcFields($marcIndex);
        $subMap = null;
        if (!empty($fields)) {
            foreach ($fields as $field) {
                if ($rc instanceof SingleEntry) {
                    $subMap = $this->renderField($field, $rc);
                } else if ($rc instanceof CompoundEntry) {
                    foreach ($rc->elements as $elem) {
                        $sm = $this->renderField($field, $elem);
                        if (!empty($sm)) {
                            $subMap = $sm;
                            break 2;
                        }
                    }
                }
            }
        }
        return empty($subMap);
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
            $repeated = false;
            if ($groupViewInfo) {
                $fieldViewInfo = $groupViewInfo[$fieldName];
                if ($fieldViewInfo) {
                    if (array_key_exists(SolrMarc::$RENDER_INFO_FIELD_TYPE, $fieldViewInfo)) {
                        $renderType = $fieldViewInfo[SolrMarc::$RENDER_INFO_FIELD_TYPE];
                    }
                    if (array_key_exists(SolrMarc::$RENDER_INFO_FIELD_MODE, $fieldViewInfo)) {
                        $renderMode = $fieldViewInfo[SolrMarc::$RENDER_INFO_FIELD_MODE];
                    }
                    if (array_key_exists(SolrMarc::$RENDER_INFO_FIELD_REPEATED, $fieldViewInfo)) {
                        $repeated = $fieldViewInfo[SolrMarc::$RENDER_INFO_FIELD_REPEATED];
                    }
                }
            }
            // echo "<!-- SPECIAL: $groupName > $fieldName: rt=$renderType rm=$renderMode rep=$repeated -->\n";

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
                $renderGroupEntry->repeated = $repeated;
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
                $fieldMap = $elem->buildMap();
                if (count($fieldMap) === 0) {
                    $rawData = $this->getMarcSubfieldsRaw($elem->marcIndex);
                    $subMap = ['value' => $this->mergeRawData($elem->marcIndex, $rawData),
                        'escHtml' => FALSE,
                        '@ind1' => AbstractRenderConfigEntry::$UNKNOWN_INDICATOR,
                        '@ind2' => AbstractRenderConfigEntry::$UNKNOWN_INDICATOR];
                } else {
                    $subMap = $this->getMappedFieldData($field, $fieldMap, $getIndicatorsAndSubfields);
                    $subMap['escHtml'] = TRUE;
                    $subMap['@ind1'] = $this->normalizeIndicator($subMap['@ind1']);
                    $subMap['@ind2'] = $this->normalizeIndicator($subMap['@ind2']);
                }
            } else if ($field instanceof \File_MARC_Control_Field) {
                $subMap = ['value' => $field->getData(),
                    'escHtml' => TRUE,
                    '@ind1' => AbstractRenderConfigEntry::$UNKNOWN_INDICATOR,
                    '@ind2' => AbstractRenderConfigEntry::$UNKNOWN_INDICATOR];
            } else {
                echo "<!-- UNKNOWN: Can't handle field type: " . get_class($field) . " of " . $elem . " -->\n";
                $subMap = null;
            }
            if (!$this->isEmptyValue($subMap)) {
                if ($subMap['@ind1'] === $elem->indicator1 && $subMap['@ind2'] === $elem->indicator2) {
                    return $subMap;
                } else {
                    echo "<!-- INDICATOR MISMATCH: $elem, " . $subMap['@ind1'] . "/" . $subMap['@ind2'] . " -->\n";
                }
            }
            return null;
        } catch (\Throwable $exception) {
            echo "<!-- FAIL: " . $exception->getMessage() . "\n" . $exception->getTraceAsString() . " -->\n";
        }
        return null;
    }

    protected function normalizeIndicator(String $ind): int {
        $ind = trim($ind);
        if (strlen($ind) === 0 || !is_int($ind)) {
            return AbstractRenderConfigEntry::$UNKNOWN_INDICATOR;
        }
        return intval($ind);
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

    protected function mergeRawData($marcIndex, $rawData) {
        $result = "RAW $marcIndex <ul>";
        foreach ($rawData as $entry) {
            foreach ($entry as $innerEntry) {
                $subfieldName = $innerEntry['tag'];
                $subfieldValue = $innerEntry['data'];
                if (!empty($subfieldValue)) {
                    $result .= "<li><b>" . $subfieldName . "</b>: " . htmlspecialchars($subfieldValue) . "</li>";
                }
            }
        }
        return $result . "</ul>";
    }
}