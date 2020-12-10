<?php

namespace SwissCollections\RecordDriver;

class ViewFieldInfo {
    protected $detailViewFieldInfo;

    public static $RENDER_INFO_FIELD_TYPE = "type";
    public static $RENDER_INFO_FIELD_MODE = "mode";
    public static $RENDER_INFO_FIELD_REPEATED = "repeated";
    public static $RENDER_INFO_FIELD_SUBFIELDS = "entries";
    public static $RENDER_INFO_FIELD_SUBFIELD_SEQUENCES = "sequences";

    public function __construct($detailViewFieldInfo) {
        $this->detailViewFieldInfo = $detailViewFieldInfo;
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
        $fieldViewInfo = $groupViewInfo[$name . "-" . $marcIndex];
        if (empty($fieldViewInfo)) {
            $fieldViewInfo = $groupViewInfo[$name];
        }
        return $fieldViewInfo;
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
     * @return bool
     */
    public function hasMode($fieldViewInfo): bool {
        return array_key_exists(ViewFieldInfo::$RENDER_INFO_FIELD_MODE, $fieldViewInfo);
    }

    /**
     * @param array $fieldViewInfo - data returned by getField()
     * @return bool
     */
    public function hasRepeated($fieldViewInfo): bool {
        return array_key_exists(ViewFieldInfo::$RENDER_INFO_FIELD_REPEATED, $fieldViewInfo);
    }

    /**
     * @param array $fieldViewInfo - data returned by getField()
     * @return String - either single | compound | sequences
     */
    public function getType($fieldViewInfo): String {
        return $fieldViewInfo[ViewFieldInfo::$RENDER_INFO_FIELD_TYPE];
    }

    /**
     * @param array $fieldViewInfo - data returned by getField()
     * @return String - either line | inline
     */
    public function getMode($fieldViewInfo): String {
        return $fieldViewInfo[ViewFieldInfo::$RENDER_INFO_FIELD_MODE];
    }

    /**
     * @param array $fieldViewInfo - data returned by getField()
     * @return String
     */
    public function getRepeated($fieldViewInfo): bool {
        return $fieldViewInfo[ViewFieldInfo::$RENDER_INFO_FIELD_REPEATED];
    }

    /**
     * Especially for compound view configs.
     * @param array $fieldViewInfo - data returned by getField()
     * @return String[]
     */
    public function getSubfieldEntries($fieldViewInfo) {
        $v = $fieldViewInfo[ViewFieldInfo::$RENDER_INFO_FIELD_SUBFIELDS];
        if (empty($v)) {
            return [];
        }
        return $v;
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
            $fieldNameCandidates = array_keys($groupViewInfo);
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