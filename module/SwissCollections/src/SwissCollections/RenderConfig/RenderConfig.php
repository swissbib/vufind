<?php
/**
 * SwissCollections: RenderConfig.php
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
 * @package  SwissCollections\RenderConfig
 * @author   Lionel Walter <lionel.walter@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.swisscollections.org Project Wiki
 */

namespace SwissCollections\RenderConfig;

use SwissCollections\RecordDriver\ViewFieldInfo;

/**
 * Class RenderConfig.
 *
 * This class represents all configuration options of "detail-fields.csv".
 *
 * @category SwissCollections_VuFind
 * @package  SwissCollections\RenderConfig
 * @author   Lionel Walter <lionel.walter@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
class RenderConfig
{
    /**
     * All group configuration.
     *
     * @var RenderGroupConfig[]
     */
    protected $info = [];

    /**
     * Adds one group configuration.
     *
     * @param RenderGroupConfig $entry the entry to add
     *
     * @return void
     */
    public function add(RenderGroupConfig $entry)
    {
        $this->info[] = $entry;
    }

    /**
     * Returns all group configurations.
     *
     * @return RenderGroupConfig[]
     */
    public function entries()
    {
        return $this->info;
    }

    /**
     * Returns a string represenation.
     *
     * @return string
     */
    public function __toString()
    {
        $s = "RenderConfig{[\n";
        foreach ($this->info as $e) {
            $s = $s . "\t" . $e . ",\n";
        }
        return $s . "]}";
    }

    /**
     * Returns a group's configuration by name.
     *
     * @param string $groupName the group's name
     *
     * @return null|RenderGroupConfig
     */
    public function get(String $groupName)
    {
        foreach ($this->info as $renderGroupConfig) {
            if ($groupName === $renderGroupConfig->getName()) {
                return $renderGroupConfig;
            }
        }
        return null;
    }

    /**
     * Sort the groups and their fields by the information in
     * "detail-view-field-structure.yaml".
     *
     * @param ViewFieldInfo $viewFieldInfo the
     *
     * @return void
     */
    public function orderGroups($viewFieldInfo)
    {
        $newGroups = [];
        $groupOrder = $viewFieldInfo->groupNames();
        foreach ($groupOrder as $groupName) {
            $gc = $this->get($groupName);
            if ($gc) {
                $newGroups[] = $gc;
                $gc->orderFields($viewFieldInfo);
            }
        }
        foreach ($this->info as $renderGroupConfig) {
            if (!in_array($renderGroupConfig->getName(), $groupOrder)) {
                $newGroups[] = $renderGroupConfig;
                $renderGroupConfig->orderFields($viewFieldInfo);
            }
        }
        $this->info = $newGroups;
    }
}