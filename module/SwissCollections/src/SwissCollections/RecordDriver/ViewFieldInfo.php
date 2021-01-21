<?php
/**
 * SwissCollections: ViewFieldInfo.php
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

use SwissCollections\RenderConfig\FormatterConfig;

/**
 * Represents all information read from "detail-view-field-structure.yaml"
 * which is used in the detail view.
 *
 * @category SwissCollections_VuFind
 * @package  SwissCollections\RecordDriver
 * @author   Lionel Walter <lionel.walter@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://vufind.org/wiki/development Wiki
 */
class ViewFieldInfo
{
    protected $detailViewFieldInfo;

    public static $RENDER_INFO_FIELDS = "fields";
    public static $RENDER_INFO_GROUPS = "groups";

    public static $RENDER_INFO_FIELD_TYPE = "type";
    public static $RENDER_INFO_FIELD_MODE = "formatter";
    public static $RENDER_INFO_FIELD_SUBFIELD_SEQUENCES = "sequences";
    public static $RENDER_INFO_FIELD_VALUE_PROVIDER = "provider";

    /**
     * ViewFieldInfo constructor.
     *
     * @param mixed $detailViewFieldInfo the read in information
     */
    public function __construct($detailViewFieldInfo)
    {
        $this->detailViewFieldInfo = $detailViewFieldInfo;
    }

    /**
     * Checks if a {@link ViewFieldInfo::$RENDER_INFO_FIELD_TYPE} is specified.
     *
     * @param array $fieldViewInfo data returned by getField()
     *
     * @return bool
     */
    public function hasType($fieldViewInfo): bool
    {
        return array_key_exists(
            ViewFieldInfo::$RENDER_INFO_FIELD_TYPE, $fieldViewInfo
        );
    }

    /**
     * Returns the value of {@link ViewFieldInfo::$RENDER_INFO_FIELD_TYPE}.
     *
     * @param array $fieldViewInfo data returned by getField()
     *
     * @return String|null either single | compound | sequences
     */
    public function getType($fieldViewInfo)
    {
        return $fieldViewInfo[ViewFieldInfo::$RENDER_INFO_FIELD_TYPE];
    }

    /**
     * Get a group's configuration.
     *
     * @param string $name the group's name
     *
     * @return mixed | null
     */
    public function getGroup(string $name)
    {
        return $this->detailViewFieldInfo['structure'][$name];
    }

    /**
     * Field info with marc index postfix is preferred to info without.
     * Read from {@link ViewFieldInfo::$RENDER_INFO_FIELDS}.
     *
     * @param array  $groupViewInfo data returned by getGroup()
     * @param string $name          the field's name
     * @param int    $marcIndex     optional marc index
     *
     * @return mixed | null
     */
    public function getField($groupViewInfo, $name, $marcIndex = -1)
    {
        $fields = $groupViewInfo[ViewFieldInfo::$RENDER_INFO_FIELDS];
        $fieldViewInfo = $fields[$name . "-" . $marcIndex];
        if (empty($fieldViewInfo)) {
            $fieldViewInfo = $fields[$name];
        }
        return $fieldViewInfo;
    }

    /**
     * Returns a formatter's config (from
     * {@link ViewFieldInfo::$RENDER_INFO_FIELD_MODE}).
     *
     * @param array|null $groupViewInfo data returned by getGroup()
     * @param string     $fieldName     the field's name
     *
     * @return FormatterConfig
     */
    public function getFieldGroupFormatter($groupViewInfo, $fieldName)
    {
        $config = [];
        $cfg = $this->groupInfo($groupViewInfo, $fieldName);
        if (!empty($cfg)) {
            $config = $cfg[ViewFieldInfo::$RENDER_INFO_FIELD_MODE];
        }
        return new FormatterConfig('default', $config);
    }

    /**
     * Returns a value provider's name or null (from
     * {@link ViewFieldInfo::$RENDER_INFO_FIELD_VALUE_PROVIDER}).
     *
     * @param array|null $groupViewInfo data returned by getGroup()
     * @param string     $fieldName     the field's name
     *
     * @return string | null
     */
    public function getFieldValueProvider($groupViewInfo, $fieldName)
    {
        $provider = null;
        $cfg = $this->getField($groupViewInfo, $fieldName);
        if (!empty($cfg)) {
            $provider = $cfg[ViewFieldInfo::$RENDER_INFO_FIELD_VALUE_PROVIDER];
        }
        return $provider;
    }

    /**
     * Returns a group's view config. Read from
     * {@link ViewFieldInfo::$RENDER_INFO_GROUPS}.
     *
     * @param array|null $groupViewInfo data returned by getGroup()
     * @param string     $fieldName     the field's name
     *
     * @return mixed | null
     */
    protected function groupInfo($groupViewInfo, $fieldName)
    {
        $config = [];
        if ($groupViewInfo) {
            $fieldGroups = $groupViewInfo[ViewFieldInfo::$RENDER_INFO_GROUPS];
            if ($fieldGroups) {
                $cfg = $fieldGroups[$fieldName];
                if (empty($cfg)) {
                    $cfg = [];
                }
                return $cfg;
            }
        }
        return $config;
    }

    /**
     * Belongs a field to group of fields (with different conditions, marc
     * indexes)?
     * Checks {@link ViewFieldInfo::$RENDER_INFO_GROUPS}.
     *
     * @param string $groupName the group's name
     * @param string $fieldName the field's name
     *
     * @return bool
     */
    public function isMultiMarcField($groupName, $fieldName)
    {
        $groupViewInfo = $this->getGroup($groupName);
        if ($groupViewInfo) {
            $fieldGroups = $groupViewInfo[ViewFieldInfo::$RENDER_INFO_GROUPS];
            if ($fieldGroups) {
                return key_exists($fieldName, $fieldGroups);
            }
        }
        return false;
    }

    /**
     * Get a formatter's config. Read from
     * {@link ViewFieldInfo::$RENDER_INFO_FIELD_MODE}.
     *
     * @param string $defaultFormatterName the default formatter to use
     * @param array  $fieldViewInfo        data returned by getField()
     *
     * @return FormatterConfig
     */
    public function getFormatterConfig($defaultFormatterName, $fieldViewInfo
    ): FormatterConfig {
        $config = [];
        if (array_key_exists(
            ViewFieldInfo::$RENDER_INFO_FIELD_MODE, $fieldViewInfo
        )
        ) {
            $config = $fieldViewInfo[ViewFieldInfo::$RENDER_INFO_FIELD_MODE];
        }
        return new FormatterConfig($defaultFormatterName, $config);
    }

    /**
     * Especially for sequence view configs.
     * Read from {@link ViewFieldInfo::$RENDER_INFO_FIELD_SUBFIELD_SEQUENCES}.
     *
     * @param array $fieldViewInfo data returned by getField()
     *
     * @return string[][]
     */
    public function getSubfieldSequences($fieldViewInfo)
    {
        return $fieldViewInfo[ViewFieldInfo::$RENDER_INFO_FIELD_SUBFIELD_SEQUENCES];
    }

    /**
     * Get all configured group names.
     *
     * @return string[]
     */
    public function groupNames()
    {
        return array_keys($this->detailViewFieldInfo['structure']);
    }

    /**
     * Gets field names for a given group.
     * Removes optional marc index postfix from field names infos and
     * prevents duplicates (first occurrence is used).
     * Reads from {@link ViewFieldInfo::$RENDER_INFO_FIELDS}.
     *
     * @param string $groupName the group's name
     *
     * @return string[]
     */
    public function fieldNames($groupName)
    {
        $fieldNames = [];
        $groupViewInfo = $this->getGroup($groupName);
        if ($groupViewInfo) {
            $fieldNameCandidates = array_keys(
                $groupViewInfo[ViewFieldInfo::$RENDER_INFO_FIELDS]
            );
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