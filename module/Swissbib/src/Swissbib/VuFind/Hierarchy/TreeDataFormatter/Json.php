<?php
/**
 * Hierarchy Tree Data Formatter (JSON)
 *
 * PHP version 5
 *
 * Copyright (C) Villanova University 2015.
 *
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
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @category VuFind2
 * @package  HierarchyTree_DataFormatter
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @author   Markus Mächler <markus.maechler@bithost.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:hierarchy_components Wiki
 */
namespace Swissbib\VuFind\Hierarchy\TreeDataFormatter;

use VuFind\Hierarchy\TreeDataFormatter\Json as VuFindJson;

/**
 * Hierarchy Tree Data Formatter (JSON)
 *
 * @category VuFind2
 * @package  HierarchyTree_DataFormatter
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @author   Markus Mächler <markus.maechler@bithost.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:hierarchy_components Wiki
 */
class Json extends VuFindJson
{
    /**
     * Choose a title for the record.
     *
     * @param object $record   Solr record to format
     * @param string $parentID The starting point for the current recursion
     * (equivalent to Solr field hierarchy_parent_id)
     *
     * @return string
     */
    protected function pickTitle($record, $parentID)
    {
        $titles = $this->getTitlesInHierarchy($record);
        // TODO: handle missing titles more gracefully (title not available?)
        $title = isset($record->title) ? $record->title : $record->id;

        if (is_array($title)) {
            $title = array_shift($title);
        }

        return null != $parentID && isset($titles[$parentID])
            ? $titles[$parentID] : $title;
    }

    /**
     * Sort Nodes, special sort for Swissbib purposes
     *
     * @param array $array The Array to Sort
     *
     * @return array
     */
    protected function sortNodes($array)
    {
        $sorter = function ($a, $b) {
            // consider first element for the sort: $a[0]
            if (preg_match("/^(\d+)(\D+.*)?$/", $a[0], $allMatches)) {
                if (sizeof($allMatches) == 3) {
                    $first = $allMatches[1] . "." .
                        preg_replace("/\D/", "", ($allMatches[2]));
                } else {
                    $first = $allMatches[1];
                }
            } else {
                $first = '0'; // there is no numeric value to compare with
            }
            // consider first element for the sort: $b[0]
            if (preg_match("/^(\d+)(\D+.*)?$/", $b[0], $allMatches)) {
                if (sizeof($allMatches) == 3) {
                    $second = $allMatches[1] . "." .
                        preg_replace("/\D/", "", ($allMatches[2]));
                } else {
                    $second = $allMatches[1];
                }
            } else {
                $second = '0'; // there is no numeric value to compare with
            }

            // Sort arrays with precision of up to 6 decimals
            return $first === $second ? 0 : $first < $second ? -1 : 1;
            //don't use bccomp. needs a special compiler configuration for
            // PHP (works on Ubuntu but not on RedHat host
            // (PHP version 5.4 as well as 5.5)
            //PHP 5.5 was tested by myself on sb-vf16
            //return bccomp($first, $second, 6);
        };

        usort($array, $sorter);

        // Collapse array to remove sort values
        $mapper = function ($i) {
            return $i[1];
        };

        return array_map($mapper, $array);
    }
}
