<?php
/**
 * SolrSearch.php
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
 * @package  Swissbib\Controller\Plugin
 * @author   Christoph Boehm <cbo@outermedia.de>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.vufind.org  Main Page
 */
namespace Swissbib\Controller\Plugin;

use ElasticSearch\VuFind\RecordDriver\ElasticSearch;
use VuFind\Search\Results\PluginManager;
use VuFind\Search\Solr\Results;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class SolrSearch
 *
 * @category VuFind
 * @package  Swissbib\Controller\Plugin
 * @author   Christoph Boehm <cbo@outermedia.de>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.vufind.org  Main Page
 */
class SolrSearch extends AbstractPlugin
{
    /**
     * The service locator
     *
     * @var ServiceLocatorInterface
     */
    private $_serviceLocator;

    /**
     * The search config
     *
     * @var Config
     */
    private $_config;

    /**
     * SolrSearch constructor.
     *
     * @param ServiceLocatorInterface $serviceLocator The service locator
     */
    public function __construct(ServiceLocatorInterface $serviceLocator)
    {
        $this->_serviceLocator = $serviceLocator;
        $this->_config = $this->_serviceLocator->get('VuFind\Config')->get(
            'config'
        );
    }

    /**
     * Adds medias of author to ViewModel
     *
     * @param string              $type  The type (Author or Subject)
     * @param ElasticSearch|array $input The record or an array of records
     * @param int                 $limit The limit
     *
     * @return Results
     */
    public function getMedias(
        string $type, $input, int $limit = 20
    ): Results {
        if (is_array($input)) {
            $query = array_map(
                function ($el) {
                    return $el->getName();
                }, $input
            );
            $query = '[' . implode(",", $query) . ']';
        } else {
            $query = $input->getName();
        }

        //when there is a : in the solr search, it generates a solr error
        //some gnd subjects have : in their name (for example conferences)
        //for example https://lobid.org/gnd/1113781610
        //see also https://github.com/swissbib/vufind/issues/737
        $query = str_replace(":", " ", $query);

        if (isset($query)) {
            $results = $this->searchSolr($query, $type, $limit);
            return $results;
        }
        return [];
    }

    /**
     * Retrieves list of media by query
     *
     * @param string $query The author
     * @param string $type  The type
     * @param int    $limit The limit
     *
     * @return \Swissbib\VuFind\Search\Solr\Results
     */
    protected function searchSolr(string $query, string $type, int $limit): Results
    {
        // Set up the search:
        $searchClassId = "Solr";

        // @var \Swissbib\VuFind\Search\Solr\Results $results
        $results = $this->getResultsManager()->get($searchClassId);

        // @var \Swissbib\VuFind\Search\Solr\Params $params
        $params = $results->getParams();
        $params->setBasicSearch($query, $type);
        $params->setLimit($limit);

        // Attempt to perform the search; if there is a problem, inspect any Solr
        // exceptions to see if we should communicate to the user about them.
        try {
            // Explicitly execute search within controller -- this allows us to
            // catch exceptions more reliably:
            $results->performAndProcessSearch();
        } catch (\VuFindSearch\Backend\Exception\BackendException $e) {
            if ($e->hasTag('VuFind\Search\ParserError')) {
                // We need to create and process an "empty results" object to
                // ensure that recommendation modules and templates behave
                // properly when displaying the error message.
                $results = $this->getResultsManager()->get('EmptySet');
                $results->setParams($params);
                $results->performAndProcessSearch();
            } else {
                throw $e;
            }
        }

        return $results;
    }

    /**
     * Convenience method for accessing results
     *
     * @return \VuFind\Search\Results\PluginManager
     */
    protected function getResultsManager(): PluginManager
    {
        return $this->_serviceLocator->get('VuFind\Search\Results\PluginManager');
    }
}
