<?php

namespace SwissCollections\RecordDriver;

use SwissCollections\RenderConfig\FormatterConfig;

class ViewFieldInfo {
    protected $detailViewFieldInfo;

    public static $RENDER_INFO_FIELDS = "fields";
    public static $RENDER_INFO_GROUPS = "groups";

    public static $RENDER_INFO_FIELD_TYPE = "type";
    public static $RENDER_INFO_FIELD_MODE = "formatter";
    public static $RENDER_INFO_FIELD_SUBFIELD_SEQUENCES = "sequences";

    public function __construct($detailViewFieldInfo) {
        $this->detailViewFieldInfo = $detailViewFieldInfo;
    }

    /**
     * @param array $fieldViewInfo - data returned by getField()
     * @return bool
     */
    public function hasType($fieldViewInfo): bool {
        return array_key_exists(ViewFieldInfo::$RENDER_INFO_FIELD_TYPE, $fieldViewInfo);
    }

    /**
     * @param array $fieldViewInfo - data returned by getField()
     * @return String|null - either single | compound | sequences
     */
    public function getType($fieldViewInfo) {
        return $fieldViewInfo[ViewFieldInfo::$RENDER_INFO_FIELD_TYPE];
    }

    /**
     * @param String $name
     * @return mixed | null
     */
    public function getGroup(String $name) {
        return $this->detailViewFieldInfo['structure'][$name];
    }

    /**
     * Field info with marc index postfix is preferred to info without.
     * @param array $groupViewInfo - data returned by getGroup()
     * @param $name
     * @param int $marcIndex - optional marc index
     * @return mixed | null
     */
    public function getField($groupViewInfo, $name, $marcIndex = -1) {
        $fields = $groupViewInfo[ViewFieldInfo::$RENDER_INFO_FIELDS];
        $fieldViewInfo = $fields[$name . "-" . $marcIndex];
        if (empty($fieldViewInfo)) {
            $fieldViewInfo = $fields[$name];
        }
        return $fieldViewInfo;
    }

    /**
     * Returns a formatter's name or null.
     * @param array|null $groupViewInfo - data returned by getGroup()
     * @param $fieldName
     * @return FormatterConfig | null
     */
    public function getFieldGroupFormatter($groupViewInfo, $fieldName) {
        $config = [];
        if ($groupViewInfo) {
            $fieldGroups = $groupViewInfo[ViewFieldInfo::$RENDER_INFO_GROUPS];
            if ($fieldGroups) {
                $cfg = $fieldGroups[$fieldName];
                if (!empty($cfg) && array_key_exists(ViewFieldInfo::$RENDER_INFO_FIELD_MODE, $cfg)) {
                    $config = $cfg[ViewFieldInfo::$RENDER_INFO_FIELD_MODE];
                }
            }
        }
        return new FormatterConfig('default', $config);
    }

    /**
     * @param String $defaultFormatterName
     * @param array $fieldViewInfo - data returned by getField()
     * @return FormatterConfig
     */
    public function getFormatterConfig($defaultFormatterName, $fieldViewInfo): FormatterConfig {
        $config = [];
        if (array_key_exists(ViewFieldInfo::$RENDER_INFO_FIELD_MODE, $fieldViewInfo)) {
            $config = $fieldViewInfo[ViewFieldInfo::$RENDER_INFO_FIELD_MODE];
        }
        return new FormatterConfig($defaultFormatterName, $config);
    }

    /**
     * Especially for sequence view configs.
     * @param array $fieldViewInfo - data returned by getField()
     * @return String[][]
     */
    public function getSubfieldSequences($fieldViewInfo) {
        return $fieldViewInfo[ViewFieldInfo::$RENDER_INFO_FIELD_SUBFIELD_SEQUENCES];
    }

    /**
     * @return String[]
     */
    public function groupNames() {
        return array_keys($this->detailViewFieldInfo['structure']);
    }

    /**
     * Removes optional marc index postfix from field names infos and
     * prevents duplicates (first occurrence is used).
     * @param String $groupName
     * @return String[]
     */
    public function fieldNames($groupName) {
        $fieldNames = [];
        $groupViewInfo = $this->getGroup($groupName);
        if ($groupViewInfo) {
            $fieldNameCandidates = array_keys($groupViewInfo[ViewFieldInfo::$RENDER_INFO_FIELDS]);
            if ($fieldNameCandidates) {
                foreach ($fieldNameCandidates as $s) {
                    if (preg_match("/(.+)-[0-9]+$/", $s, $matches) === 1) {
                        $fn = $matches[1];
                    } else {
                        $fn = $s;
                    }
                    if (!array_search($fn, $fieldNames)) {
                        $fieldNames[] = $s;
                    }
                }
            }
        }
        return $fieldNames;
    }
}