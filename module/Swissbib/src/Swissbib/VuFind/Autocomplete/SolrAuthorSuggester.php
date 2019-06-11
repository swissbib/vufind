<?php

/**
 * SolrSuggester
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
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301 
 * USA
 *
 * @category Swissbib_VuFind2
 * @package  VuFind_Autocomplete
 * @author   Lionel Walter <lionel.walter@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public
 *           License
 * @link     http://www.swissbib.org
 */

namespace Swissbib\VuFind\Autocomplete;

use Swissbib\VuFind\Search\Solr\Results;
use VuFind\Autocomplete\Solr as VFAutocompleteSolr;
use Swissbib\VuFind\Search\Solr\Params as Params;

/**
 * SolrSuggester
 *
 * Suggestions based on Suggester Search Handler from SOLR
 *
 * @category Swissbib_VuFind
 * @package  VuFind_Autocomplete
 * @author   Lionel Walter <lionel.walter@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public
 *           License
 * @link     http://vufind.org
 */
class SolrAuthorSuggester extends VFAutocompleteSolr
{
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
                $this->mungeQuery($query), 'Author'
            );

            /**
             * Search Params
             *
             * @var Params $params
             */
            $params = $this->searchObject->getParams();

            //no results, only facets
            $params->setLimit(0);
            //only the first 3 facets
            $params->setFacetLimit(3);
            $params->addFacet('navAuthor_full');

            //only facet values which contains the first typed word
            $firstWord = explode(" ", $query)[0];

            //or the word before apostrophe
            $firstWord = explode("'", $query)[0];
            $firstWord = rtrim($firstWord, '*');
            $params->setFacetContains($firstWord);

            $params->setFacetContainsIgnoreCase(true);

            $this->searchObject->setParams($params);

            /**
             * Search Results
             *
             * @var Results $searchResults
             */
            $facets = $this->searchObject->getFacetList();
            //$facet = $searchResults->getFacetList();

            $suggestions = [];

            if (isset($facets['navAuthor_full'])) {
                foreach ($facets['navAuthor_full']['list'] as $facet) {
                    $suggestions [] = $facet['value'];
                }
            }


        } catch (\Exception $e) {
            // Ignore errors -- just return empty results if we must.
        }

        $results = [
            [
                "suggestions" => $suggestions,
            ]
        ];
        return $results;
    }
}
