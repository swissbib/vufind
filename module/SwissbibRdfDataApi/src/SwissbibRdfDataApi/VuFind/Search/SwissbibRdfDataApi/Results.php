<?php
/**
 * Results.php
 *
 * PHP Version 7
 *
 * Copyright (C) swissbib 2018
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License version 2,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.    See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category VuFind
 * @package  SwissbibRdfDataApi\VuFind\Search\ElasticSearch
 * @author   Günter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.vufind.org  Main Page
 */
namespace SwissbibRdfDataApi\VuFind\Search\SwissbibRdfDataApi;

use VuFind\Search\Base\Results as BaseResults;
use VuFindSearch\ParamBag;

/**
 * Class Results
 *
 * @category VuFind
 * @package  SwissbibRdfDataApi\VuFind\Search\ElasticSearch
 * @author   Günter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.vufind.org  Main Page
 */
class Results extends BaseResults
{
    /**
     * Search backend identifiers.
     *
     * @var string
     */
    protected $backendId = 'SwissbibRdfDataApi';

    /**
     * This method returns facet options related to the current search. You may
     * need to store extra values in performSearch() to allow the list to be
     * generated. It is possible that getFacetList will be called before a
     * search has been performed – in this case, you should call the
     * performAndProcessSearch method to fill in all the details. (See
     * \VuFind\Search\Solr\Results for an example of this). Returns the stored
     * list of facets for the last search
     *
     * @param array $filter Array of field => on-screen description listing
     *                      all of the desired facet fields; set to null to get
     *                      all configured values.
     *
     * @return array        Facets data arrays
     */
    public function getFacetList($filter = null)
    {
        // TODO: Implement getFacetList() method.
    }

    /**
     * This method reads the search parameters from the parameters and options
     * objects, uses them to perform a search against your chosen service, and
     * uses the results of the search to populate two object properties:
     * $this→resultTotal, the total number of search results found, and
     * $this→results, an array of record driver objects representing the
     * current page of results requested by the user. Abstract support method
     * for performAndProcessSearch -- perform a search based on the parameters
     * passed to the object.  This method is responsible for filling in all of
     * the key class properties: results, resultTotal, etc.
     *
     * @return void
     */
    protected function performSearch()
    {
        $query = $this->getParams()->getQuery();
        $limit = $this->getParams()->getLimit();
        $offset = $this->getStartRecord() - 1;
        //        TODO Is this required (see Solr)?
        //        $params = $this->getParams()->getBackendParameters();

        $params = new ParamBag();
        $params->add(
            "filter", array_merge(
                $this->getParams()->getFilters(),
                $this->getParams()->getHiddenFilters()
            )
        );
        $index = $this->getParams()->getIndex();
        if (strlen($index) > 0) {
            $params->add("index", $index);
        }
        $params->add("template", $this->getParams()->getTemplate());

        $searchService = $this->getSearchService();

        try {
            $collection = $searchService->search(
                $this->backendId, $query, $offset, $limit, $params
            );
        } catch (\VuFindSearch\Backend\Exception\BackendException $e) {
            throw $e;
        }

        $this->results = $collection->getRecords();
        $this->resultTotal = $collection->getTotal();
    }
}
