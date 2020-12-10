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

    /**
     * @param AbstractRenderConfigEntry $rc
     * @param Callable $renderer called with $subMap, FieldRenderContext
     */
    public function applyRenderer($rc, $renderer) {
        $fields = $this->getMarcFields($rc->marcIndex);
        if (!empty($fields)) {
            $marcFieldNumber = count($fields);
            $context = new FieldRenderContext($rc, $renderer);
            foreach ($fields as $fieldIndex => $field) {
                $isFirst = $fieldIndex === 0;
                $isLast = ($fieldIndex + 1) === $marcFieldNumber;
                $context->updateListState($field, $isFirst, $isLast);
                if ($rc instanceof SingleEntry) {
                    $this->renderSingle($context);
                } else if ($rc instanceof SequencesEntry) {
                    $this->renderSequences($context);
                } else if ($rc instanceof CompoundEntry) {
                    $this->renderCompound($context);
                }
            }
        }
    }

    /**
     * @param FieldRenderContext $context
     */
    protected function renderSequences(&$context) {
        /**
         * @var SequencesEntry $rc
         */
        $rc = $context->rc;
        $rawData = $this->getMarcSubfieldsRaw($rc->marcIndex);

        $sequences = [];
        foreach ($rawData as $entry) {
            $entryLen = count($entry);
            $index = 0;
            while ($index < $entryLen) {
                $matchedValues = $rc->matchesSubfieldSequence($entry, $index);
                if (!empty($matchedValues)) {
                    $sequences[] = $matchedValues;
                    $index += count($matchedValues);
                } else {
                    $index++;
                }
            }
        }

        $oldFirstList = $context->firstListEntry;
        $oldLastList = $context->lastListEntry;
        $context->updateListState($context->field, false, false);

        foreach ($sequences as $index => $matchedValues) {
            $isFirst = $index === array_key_first($sequences);
            $isLast = $index === array_key_last($sequences);
            $context->updateSequenceState($matchedValues, $isFirst, $isLast);
            $this->renderCompound($context);
        }

        $context->updateListState($context->field, $oldFirstList, $oldLastList);
    }

    /**
     * @param FieldRenderContext $context
     */
    protected function renderSingle(&$context) {
        /**
         * @var SingleEntry $rc
         */
        $rc = $context->rc;
        $subMap = $this->getRenderFieldData($context->field, $rc);
        if (!empty($subMap) && !$context->alreadyProcessed($subMap)) {
            $context->updateCompoundState(true, true);
            $renderer = $context->renderer;
            $renderer($subMap, $rc, $context);
            $context->addProcessed($subMap);
        }
    }

    /**
     * @param FieldRenderContext $context
     */
    protected function renderCompound(&$context) {
        /**
         * @var CompoundEntry $rc
         */
        $rc = $context->rc;
        $array = $rc->elements;
        $field = $context->field;
        $renderer = $context->renderer;
        $values = [];
        $subMaps = [];
        if ($rc instanceof SequencesEntry) {
            // $field is an array of raw data objects
            foreach ($array as $elem) {
                $value = $rc->valueForSubfield($elem, $field);
                if (!empty($value)) {
                    $subMap = $this->buildGenericSubMap($value, TRUE);
                    $values[] = ['subMap' => $subMap, 'renderConfig' => $elem];
                    $subMaps[] = $subMap;
                }
            }
        } else {
            // CompoundEntry
            // $field is a File_MARC_* object
            // filter out empty values
            foreach ($array as $elem) {
                $subMap = $this->getRenderFieldData($field, $elem);
                if (!empty($subMap)) {
                    $values[] = ['subMap' => $subMap, 'renderConfig' => $elem];
                    $subMaps[] = $subMap;
                }
            }
        }
        if (!$context->alreadyProcessed($subMaps)) {
            foreach ($values as $key => $v) {
                $isFirst = $key === array_key_first($values);
                $isLast = $key === array_key_last($values);
                $context->updateCompoundState($isFirst, $isLast);
                $renderer($v['subMap'], $v['renderConfig'], $context);
            }
            $context->addProcessed($subMaps);
        }
    }

    /**
     * Quite similar to applyRenderer() except final html creation.
     * @param $marcIndex
     * @param AbstractRenderConfigEntry $rc
     * @return bool
     */
    public function isEmptyField($marcIndex, $rc) {
        $fields = $this->getMarcFields($marcIndex);
        $subMap = null;
        if (!empty($fields)) {
            foreach ($fields as $field) {
                if ($rc instanceof SingleEntry) {
                    $subMap = $this->getRenderFieldData($field, $rc);
                } else if ($rc instanceof SequencesEntry) {
                    foreach ($rc->elements as $elem) {
                        $sm = $this->getRenderFieldData($field, $elem);
                        if (!empty($sm)) {
                            $subMap = $sm;
                            break 2;
                        }
                    }
                } else
                    if ($rc instanceof CompoundEntry) {
                        foreach ($rc->elements as $elem) {
                            $sm = $this->getRenderFieldData($field, $elem);
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
                echo "<!-- SKIPPING BAD MARC INDEX: $groupName > $fieldName > $subFieldName: '$marcIndex' -->\n";
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
            $renderMode = '';
            $repeated = false;
            $fieldViewInfo = null;

            if ($groupViewInfo) {
                $fieldViewInfo = $this->detailViewFieldInfo->getField($groupViewInfo, $fieldName, $marcIndex);
                if ($fieldViewInfo) {
                    if ($this->detailViewFieldInfo->hasType($fieldViewInfo)) {
                        $renderType = $this->detailViewFieldInfo->getType($fieldViewInfo);
                    }
                    if ($this->detailViewFieldInfo->hasMode($fieldViewInfo)) {
                        $renderMode = $this->detailViewFieldInfo->getMode($fieldViewInfo);
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
                    if ($renderMode === AbstractRenderConfigEntry::$RENDER_MODE_INLINE) {
                        $renderGroupEntry->setInlineRenderMode();
                    }
                    $renderGroupEntry->repeated = $repeated;
                }
                if ($renderType === 'sequences') {
                    $renderGroupEntry = new SequencesEntry($subFieldName, $marcIndex, $marcIndicator1, $marcIndicator2);
                    if ($fieldViewInfo) {
                        $renderGroupEntry->setSequences($this->detailViewFieldInfo->getSubfieldSequences($fieldViewInfo));
                    }
                    if ($renderMode === AbstractRenderConfigEntry::$RENDER_MODE_LINE) {
                        $renderGroupEntry->setLineRenderMode();
                    }
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
                $renderGroupEntry->repeated = $repeated;
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
    public function getRenderFieldData($field, $elem) {
        try {
            if ($field instanceof \File_MARC_Data_Field) {
                $ind1 = $this->normalizeIndicator($field->getIndicator(1));
                $ind2 = $this->normalizeIndicator($field->getIndicator(2));
                if ($ind1 !== $elem->indicator1 || $ind2 !== $elem->indicator2) {
                    echo "<!-- INDICATOR MISMATCH: $elem, $ind1/$ind2 -->\n";
                    return null;
                }

                $fieldMap = $elem->buildMap();
                if (count($fieldMap) === 0) {
                    $rawData = $this->getMarcSubfieldsRaw($elem->marcIndex);
                    // echo "<!-- RAW " . $elem->marcIndex . ": " . print_r($rawData, true) . " -->\n";
                    // ignore indicators for raw output in $subMap
                    $subMap = $this->buildGenericSubMap($this->mergeRawData($elem->marcIndex, $rawData, $ind1, $ind2), FALSE);
                } else {
                    $subMap = $this->getMappedFieldData($field, $fieldMap, true);
                    $subMap['escHtml'] = TRUE;
                    $subMap['@ind1'] = $ind1;
                    $subMap['@ind2'] = $ind2;
                }
            } else if ($field instanceof \File_MARC_Control_Field) {
                $subMap = $this->buildGenericSubMap($field->getData(), TRUE);
            } else {
                echo "<!-- UNKNOWN: Can't handle field type: " . get_class($field) . " of " . $elem . " -->\n";
                $subMap = null;
            }
            // echo "<!-- GRFD: " . $elem->marcIndex . " " . $elem->getSubfieldName() . " " . print_r($subMap, true) . " -->\n";
            if (!$this->isEmptyValue($subMap)) {
                return $subMap;
            }
            return null;
        } catch (\Throwable $exception) {
            echo "<!-- FAIL: " . $exception->getMessage() . "\n" . $exception->getTraceAsString() . " -->\n";
        }
        return null;
    }

    protected function buildGenericSubMap($value, bool $escapeHtml) {
        $subMap = [
            'value' => $value,
            'escHtml' => $escapeHtml,
            '@ind1' => AbstractRenderConfigEntry::$UNKNOWN_INDICATOR,
            '@ind2' => AbstractRenderConfigEntry::$UNKNOWN_INDICATOR
        ];
        return $subMap;
    }

    protected function normalizeIndicator(String $ind): int {
        $ind = trim($ind);
        if (strlen($ind) === 0 || !ctype_digit($ind)) {
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