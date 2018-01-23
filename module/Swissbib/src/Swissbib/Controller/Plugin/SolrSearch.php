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
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA    02111-1307    USA
 *
 * @category VuFind
 * @package  Swissbib\Controller\Plugin
 * @author   Christoph Boehm <cbo@outermedia.de>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.vufind.org  Main Page
 */
namespace Swissbib\Controller\Plugin;

use ElasticSearch\VuFind\RecordDriver\ElasticSearch;
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
    public function __construct(ServiceLocatorInterface $serviceLocator
    ) {
        $this->_serviceLocator = $serviceLocator;
        $this->_config = $this->_serviceLocator->get('VuFind\Config')->get(
            'config'
        );
    }

    /**
     * SolrSearch constructor.
     */

    /**
     * Adds media of author to ViewModel
     *
     * @param string        $type   The type (Author or Subject)
     * @param ElasticSearch $record The record
     * @param int           $limit  The limit
     *
     * @return array
     */
    public function getMedia(string $type, ElasticSearch $record, int $limit = 20)
    {
        $name = $record->getName();
        if (isset($name)) {
            $results = $this->searchSolr($name, $type, $limit);
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
     * @return mixed
     */
    protected function searchSolr(string $query, string $type, int $limit): array
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

        return $results->getResults();
    }

    /**
     * Convenience method for accessing results
     *
     * @return \VuFind\Search\Results\PluginManager
     */
    protected function getResultsManager()
    {
        return $this->_serviceLocator->get('VuFind\SearchResultsPluginManager');
    }
}