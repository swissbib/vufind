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
    public function __construct(ServiceLocatorInterface $serviceLocator)
    {
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
     * @param int    $limit    The limit
     * @param int    $page     The page
     *
     * @return Results
     */
    public function searchElasticSearch(
        string $q, string $template, string $index = null, string $type = null,
        int $limit = 20, int $page = 1
    ): Results {
        // Set up the search:
        $searchClassId = "ElasticSearch";

        /**
         * Results
         *
         * @var Results
         */
        $results = $this->getResultsManager()->get($searchClassId);

        /**
         * Params
         *
         * @var Params
         */
        $params = $results->getParams();
        $params->setPage($page);
        $params->setLimit($limit);

        if (isset($index)) {
            $params->setIndex($index);
        }
        $params->setTemplate($template);

        /**
         * Query
         *
         * @var Query $query
         */
        $query = $params->getQuery();
        if (isset($type)) {
            $query->setHandler($type);
        }
        $query->setString($q);

        $results->performAndProcessSearch();

        return $results;
    }

    /**
     * From Ajax request
     *
     * @param string $id         The person id
     * @param int    $resultSize The result size
     * @param int    $searchSize The number of bibliographic resources to search in
     * @param int    $page       The page
     *
     * @return Results
     */
    public function searchCoContributorsOfPerson(
        string $id, int $resultSize, int $searchSize, int $page
    ): Results {
        $bibliographicResources = $this->searchElasticSearch(
            "http://data.swissbib.ch/person/" . $id,
            "bibliographicResources_by_author", "lsb", "bibliographicResource",
            $searchSize
        )->getResults();
        return $this->searchCoContributorsFrom(
            $bibliographicResources, $id, $resultSize, $page
        );
    }

    /**
     * From Ajax request
     *
     * @param string $id         The subject id
     * @param int    $resultSize The result size
     * @param int    $searchSize The number of bibliographic resources to search in
     * @param int    $page       The page
     *
     * @return Results
     */
    public function searchContributorsOfSubject(
        string $id, int $resultSize, int $searchSize, int $page
    ): Results {
        $bibliographicResources = $this->searchElasticSearch(
            "http://d-nb.info/gnd/" . $id,
            "bibliographicResources_by_subject", "lsb", "bibliographicResource",
            $searchSize
        )->getResults();
        return $this->searchCoContributorsFrom(
            $bibliographicResources, $id, $resultSize, $page
        );
    }

    /**
     * Initial call or chained from Ajax
     *
     * @param array  $bibliographicResources The bibliographic resources
     * @param string $id                     The id of the person
     * @param int    $resultSize             The size of result
     * @param int    $page                   The page
     *
     * @return Results
     */
    public function searchCoContributorsFrom(
        array $bibliographicResources, string $id, int $resultSize, int $page = 1
    ): Results {

        $contributorIds = $this->getCoContributorIds($bibliographicResources, $id);

        $start = $resultSize * ($page - 1);
        $searchIds = $start < count($contributorIds) ? array_slice(
            $contributorIds, $start, $resultSize
        ) : [];
        $results = $this->searchElasticSearch(
            $this->arrayToSearchString($searchIds), "id", "lsb", "person",
            $resultSize
        );

        $this->_fixResultForPagination($id, $page, $results, $contributorIds);
        return $results;
    }

    /**
     * Gets ids of co contributors
     *
     * @param array  $bibliographicResources The bibliographic resources
     * @param string $authorId               The author id
     *
     * @return array
     */
    protected function getCoContributorIds(array $bibliographicResources, $authorId
    ): array {
        $contributorIds = [];
        // @var \ElasticSearch\VuFind\RecordDriver\ESBibliographicResource
        // $bibliographicResource
        foreach ($bibliographicResources as $bibliographicResource) {
            $contributorIds = array_merge(
                $contributorIds, $bibliographicResource->getContributors()
            );
        }
        $contributorIds = array_unique($contributorIds);

        $contributorIds = array_filter(
            $contributorIds,
            function ($val) use ($authorId) {
                return $val !== $authorId;
            }
        );
        return $contributorIds;
    }

    /**
     * Converts array to search string
     *
     * @param array $ids Array of ids
     *
     * @return string
     */
    protected function arrayToSearchString(array $ids): string
    {
        return '[' . implode(",", $ids) . ']';
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

    /**
     * Fixes the results object to work with pagination
     *
     * @param string  $id             The id
     * @param int     $page           The page
     * @param Results $results        The results
     * @param array   $contributorIds The contributor ids
     *
     * @throws \ReflectionException
     *
     * @return void
     */
    private function _fixResultForPagination(
        string $id, int $page, Results &$results, array $contributorIds
    ) {
        $reflectionClass = new \ReflectionClass(Results::class);
        $reflectionProperty = $reflectionClass->getProperty("resultTotal");
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($results, count($contributorIds));
        $params = $results->getParams();
        $params->setPage($page);
        $query = $params->getQuery();
        $query->setString($id);
    }
}
