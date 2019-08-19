<?php
/**
 * SolrCombineFields
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
 * @package  VuFind_Autocomplete
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
namespace Swissbib\VuFind\Autocomplete;

/**
 * SolrCombineFields
 *
 * Combine multiple fields in the suggestions
 *
 * @category Swissbib_VuFind
 * @package  VuFind_Auth
 * @author   Lionel Walter <lionel.walter@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
class SolrCombineFields extends Solr
{
    /**
     * GetSuggestionsFromSearch
     *
     * @param array  $searchResults SearchResults
     * @param String $query         Query
     * @param String $exact         Exact
     *
     * @return array
     */
    protected function getSuggestionsFromSearch($searchResults, $query, $exact)
    {
        $results = [];

        foreach ($searchResults as $object) {
            $current = $object->getRawData();
            $combination = '';
            foreach ($this->displayField as $field) {
                $displayText = '';
                $fieldValues = $current[$field];
                if (is_array($fieldValues) && !empty($fieldValues)) {
                    $displayText =  $fieldValues[0];
                } else {
                    $displayText = $fieldValues;
                }
                $forbidden = [
                    ':', '&', '?', '*', '[', ']', '"', '/', '\\', ';', '.',
                    '='
                ];
                $displayText = str_replace($forbidden, " ", $displayText);

                if ($displayText) {
                    if ($combination != '') {
                        $combination .= ' / ' . $displayText;
                    } else {
                        $combination = $displayText;
                    }
                }
            }
            if (!$this->isDuplicate($combination, $results)) {
                $results[]=$combination;
            }
        }

        return $results;
    }
}
