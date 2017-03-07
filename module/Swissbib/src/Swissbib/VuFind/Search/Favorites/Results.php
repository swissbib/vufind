<?php
/**
 * Favorites aspect of the Search Multi-class (Results)
 *
 * PHP version 5
 *
 * Copyright (C) Villanova University 2010.
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
 * @package  VuFind_Search_Favorites
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org   Main Site
 */
namespace Swissbib\VuFind\Search\Favorites;

use VuFind\Exception\ListPermission as ListPermissionException,
    VuFind\Search\Base\Results as BaseResults;

use VuFind\Search\Favorites\Results as VFFavoriteResults;

/**
 * Search Favorites Results
 *
 * @category VuFind2
 * @package  VuFind_Search_Favorites
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org   Main Site
 */
class Results extends VFFavoriteResults
{
    /**
     * Returns the stored list of facets for the last search
     *
     * @param array $filter Array of field => on-screen description listing
     * all of the desired facet fields; set to null to get all configured values.
     *
     * @return array        Facets data arrays
     */
    public function getFacetList($filter = null)
    {
        // Make sure we have processed the search before proceeding:
        if (is_null($this->user)) {
            $this->performAndProcessSearch();
        }

        // If there is no filter, we'll use all facets as the filter:
        if (is_null($filter)) {
            $filter = $this->getParams()->getFacetConfig();
        }

        // Start building the facet list:
        $retVal = [];

        // Loop through every requested field:
        $validFields = array_keys($filter);
        foreach ($validFields as $field) {
            if (!isset($this->facets[$field])) {
                $this->facets[$field] = [
                    'label' => $this->getParams()->getFacetLabel($field),
                    'list' => []
                ];
                switch ($field) {
                case 'lists':
                    $lists = $this->user ? $this->user->getLists() : [];
                    foreach ($lists as $list) {
                        $this->facets[$field]['list'][] = [
                            'value' => $list->id,
                            'displayText' => $list->title,
                            'count' => $list->cnt,
                            'isApplied' =>
                                $this->getParams()->hasFilter("$field:" . $list->id)
                        ];
                    }
                    break;

                case 'tags':
                    if ($this->list) {
                        $tags = $this->list->getTags();
                    } else {
                        $tags = $this->user ? $this->user->getTags() : [];
                    }
                    foreach ($tags as $tag) {
                        $this->facets[$field]['list'][] = [
                            'value' => $tag->tag,
                            'displayText' => $tag->tag,
                            'count' => $tag->cnt,
                            'isApplied' =>
                                $this->getParams()->hasFilter("$field:" . $tag->tag)
                        ];
                    }
                    break;
                }
            }
            if (isset($this->facets[$field])) {
                $retVal[$field] = $this->facets[$field];
            }
        }

        return $retVal;
    }
}
