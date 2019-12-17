<?php
/**
 * Authors
 *
 * PHP version 7
 *
 * Copyright (C) project swissbib, University Library Basel, Switzerland
 * http://www.swissbib.org  / http://www.swissbib.ch / http://www.ub.unibas.ch
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
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category Swissbib_VuFind
 * @package  View_Helper
 * @author   Matthias Edel <matthias.edel@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
namespace Swissbib\View\Helper;

use Zend\View\Helper\AbstractHelper;

/**
 * Sorter for facet- and library-datastructures by displayName/label
 *
 * @category Swissbib_VuFind
 * @package  View_Helper
 * @author   Matthias Edel <matthias.edel@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
class DisplayNameSorter extends AbstractHelper
{
    /**
     * Associative index of subarray to sort by
     *
     * @var string
     */
    protected $sortIndex;

    /**
     * Sort facets by display name
     *
     * @param array $facetList facet list
     * @param string $sortIndex sort index
     *
     * @return Array[]
     */
    public function __invoke(array $facetList, string $sortIndex)
    {
        $this->sortIndex = $sortIndex;
        uasort($facetList, [$this, "_sortByDisplayName"]);
        return $facetList;
    }

    /**
     * Sort two data-structures by display name
     *
     * @param string $a that one datastructure
     * @param string $b that other datastructure
     *
     * @return int which one is on top now?
     */
    private function _sortByDisplayName($a, $b)
    {
        $a = strtoupper($a[$this->sortIndex]);
        $b = strtoupper($b[$this->sortIndex]);
        if ($a == $b) {
            return 0;
        }
        return ($a < $b) ? -1 : 1;
    }
}
