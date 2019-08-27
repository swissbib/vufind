<?php
/**
 * Solr
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

use Swissbib\VuFind\Search\Solr\Options;
use Swissbib\VuFind\Search\Solr\Params as Params;
use VuFind\Autocomplete\Solr as VFAutocompleteSolr;

/**
 * Solr
 *
 * @category Swissbib_VuFind
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
                            ':', '&', '?', '*', '[', ']', '"', '/', '\\', ';', '.',
                            '='
                        ];
                        $bestMatch = str_replace($forbidden, " ", $bestMatch);
                        if (!$this->isDuplicate($bestMatch, $results)) {
                            $results[] = $bestMatch;
                            break;
                        }
                    }
                }
            }
        }
        return $results;
    }

    /**
     * This method returns an array of strings matching the user's query for
     * display in the autocomplete box.
     *
     * @param string $query The user query
     *
     * @return array        The suggestions for the provided query
     */
    public function getSuggestions($query)
    {
        if (!is_object($this->searchObject)) {
            throw new \Exception('Please set configuration first.');
        }

        try {
            $this->searchObject->getParams()->setBasicSearch(
                $this->mungeQuery($query), $this->handler
            );

            /**
             * Search Params
             *
             * @var Params $params
             */
            $params = $this->searchObject->getParams();

            /**
             * Search Options
             *
             * @var Options $options
             */

            //needed to handle OR query correctly
            $params->convertToAdvancedSearch();

            $options = $this->searchObject->getOptions();
            $options->disableHighlighting();
            $options->spellcheckEnabled(false);
            $options->setLimitOptions(5);

            $params->setOptions($options);

            $this->searchObject->setParams($params);
            $searchResults = $this->searchObject->getResults();

            // Build the recommendation list
            // at least one of the queried word must match
            $results = $this->getSuggestionsFromSearch(
                $searchResults, $query, false
            );
        } catch (\Exception $e) {
            // Ignore errors -- just return empty results if we must.
        }

        // Wrap in array as only values of result array are part of response
        $results = [
            [
                "suggestions" => $results ?? []
            ]
        ];

        return $results;
    }

    /**
     * Tests if an suggestion is already in the results
     *
     * @param string $bestMatch The string to test
     * @param array  $results   The result list
     *
     * @return bool
     */
    protected function isDuplicate(string $bestMatch, array &$results)
    {
        foreach ($results as $result) {
            if (strtolower($result) === strtolower($bestMatch)) {
                return true;
            }
        }
        return false;
    }


    /**
     * Process the user query to make it suitable for a Solr query.
     * Adds a * at the end.
     *
     * @param string $query Incoming user query
     *
     * @return string       Processed query
     */
    protected function mungeQuery($query)
    {
        // Modify the query so it makes a nice, truncated autocomplete query:
        $forbidden = [':', '(', ')', '*', '+', '"', "'"];
        $query = str_replace($forbidden, " ", $query);
        if (substr($query, -1) != " ") {
            //due to https://github.com/swissbib/vufind/issues/700
            //we do an OR wildcarded search except if the last character is
            //a space
            $query = $query . " OR " . $query . "*";
        }
        return $query;
    }
}
