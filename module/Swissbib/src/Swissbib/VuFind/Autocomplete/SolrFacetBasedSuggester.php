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

use Swissbib\VuFind\Search\Solr\Params as Params;
use Swissbib\VuFind\Search\Solr\Results;
use VuFind\Autocomplete\Solr as VFAutocompleteSolr;

/**
 * SolrFacetBasedSuggester
 *
 * This Suggester provides suggestions based on a given facet
 *
 * @category Swissbib_VuFind
 * @package  VuFind_Autocomplete
 * @author   Lionel Walter <lionel.walter@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public
 *           License
 * @link     http://vufind.org
 */
class SolrFacetBasedSuggester extends VFAutocompleteSolr
{
    /**
     * Facet to use to retrieve suggestions
     *
     * @var string
     */
    protected $facet;

    /**
     * If true, only terms which contains the first word of the
     * search query will be suggested
     *
     * @var bool
     */
    protected $facetContains;

    /**
     * Number of Suggestions to retrieve
     *
     * @var int
     */
    protected $numberSuggestions;

    /**
     * Set parameters that affect the behavior of the autocomplete handler.
     * These values normally come from the search configuration file.
     *
     * @param string $params Parameters to set
     *
     * @return void
     */
    public function setConfig($params)
    {
        // Save the basic parameters:
        $params = explode(':', $params);
        $this->handler = (isset($params[0]) && !empty($params[0])) ?
            $params[0] : null;
        $this->facet = (isset($params[1]) && !empty($params[1])) ?
            $params[1] : null;
        $this->displayField = $this->facet;
        if (isset($params[2]) && !empty($params[2]) && $params[2] == 'true') {
            $this->facetContains = true;
        } else {
            $this->facetContains = false;
        }
        if (isset($params[3]) && !empty($params[3])) {
            $value = intval($params[3]);
            if ($value < 1) {
                //default value
                $this->numberSuggestions = 3;
            } else {
                $this->numberSuggestions = $value;
            }
        }
        $this->filters = [];

        // Set up the Search Object:
        $this->initSearchObject();
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

            //this is the facet we will search and display
            $facet = $this->facet;

            /**
             * Search Params
             *
             * @var Params $params
             */
            $params = $this->searchObject->getParams();

            //no results, only facets
            $params->setLimit(0);

            //only the first 3 facets
            $params->setFacetLimit($this->numberSuggestions);
            $params->addFacet($facet);

            if ($this->facetContains) {
                //only facet values which contains the first typed word
                $firstWord = explode(" ", $query)[0];

                //or the word before apostrophe
                $firstWord = explode("'", $firstWord)[0];
                $firstWord = rtrim($firstWord, '*');

                $params->setFacetContains($firstWord);
                $params->setFacetContainsIgnoreCase(true);
            }
            $this->searchObject->setParams($params);

            $facets = $this->searchObject->getFacetList();

            $suggestions = [];

            if (isset($facets[$facet])) {
                foreach ($facets[$facet]['list'] as $facet_values) {
                    $suggestions[] = $facet_values['value'];
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
