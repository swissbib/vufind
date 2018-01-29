<?php
/**
 * ElasticSearchSearch.php
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

use ElasticSearch\VuFind\Search\ElasticSearch\Params;
use ElasticSearch\VuFind\Search\ElasticSearch\Results;
use VuFindSearch\Query\Query;
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
class ElasticSearchSearch extends AbstractPlugin
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
     * ElasticSearchSearch constructor.
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
     * Execute the search
     *
     * @param string $q        The query string
     * @param string $template The template
     * @param string $index    The index
     * @param string $type     The type
     *
     * @return Results
     */
    public function searchElasticSearch(
        string $q, string $template, string $index = null, string $type = null
    ) {
        // Set up the search:
        $searchClassId = "ElasticSearch";

        // @var Results
        $results = $this->getResultsManager()->get($searchClassId);

        // @var Params
        $params = $results->getParams();

        if (isset($index)) {
            $params->setIndex($index);
        }
        $params->setTemplate($template);

        // @var Query $query
        $query = $params->getQuery();
        if (isset($type)) {
            $query->setHandler($type);
        }
        $query->setString($q);

        $results->performAndProcessSearch();

        return $results;
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
