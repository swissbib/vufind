<?php
/**
 * Solr
 *
 * PHP version 5
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
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @category Swissbib_VuFind2
 * @package  VuFind_Autocomplete
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
namespace Swissbib\VuFind\Autocomplete;

use VuFind\Autocomplete\Solr as VFAutocompleteSolr;

/**
 * Solr
 *
 * @category Swissbib_VuFind2
 * @package  VuFind_Auth
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
class Solr extends VFAutocompleteSolr
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
            foreach ($this->displayField as $field) {
                if (isset($current[$field])) {
                    $bestMatch = $this->pickBestMatch(
                        $current[$field], $query, $exact
                    );
                    if ($bestMatch) {
                        $forbidden = [
                            ':', '&', '?', '*', '[',']', '"', '/','\\',';','.','='
                        ];
                        $bestMatch = str_replace($forbidden, " ", $bestMatch);

                        $results[] = $bestMatch;
                        break;
                    }
                }
            }
        }

        return $results;
    }
}
