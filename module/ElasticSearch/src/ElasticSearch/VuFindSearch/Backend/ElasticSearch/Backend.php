<?php
/**
 * Backend.php
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
 * @package  ElasticSearch\VuFindSearch\Backend\ElasticSearch
 * @author   Christoph Boehm <cbo@outermedia.de>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.vufind.org  Main Page
 */
namespace ElasticSearch\VuFindSearch\Backend\ElasticSearch;

// @codingStandardsIgnoreLineuse
use ElasticSearch\VuFindSearch\Backend\ElasticSearch\Response\AdapterClientResult\RecordCollectionFactory;
use ElasticsearchAdapter\Adapter;
use ElasticsearchAdapter\Result\ElasticsearchClientResult;
use VuFindSearch\Backend\AbstractBackend;
use VuFindSearch\Feature\RandomInterface;
use VuFindSearch\Feature\RecordCollectionInterface;
use VuFindSearch\Feature\RetrieveBatchInterface;
use VuFindSearch\Feature\SimilarInterface;
use VuFindSearch\ParamBag;
use VuFindSearch\Query\AbstractQuery;
use VuFindSearch\Response\RecordCollectionFactoryInterface;

/**
 * Class Backend
 *
 * @category VuFind
 * @package  ElasticSearch\VuFindSearch\Backend\ElasticSearch
 * @author   Christoph Boehm <cbo@outermedia.de>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.vufind.org  Main Page
 */
class Backend extends AbstractBackend
    implements SimilarInterface, RetrieveBatchInterface, RandomInterface
{
    /**
     * The Elastic Search Adapter
     *
     * @var \ElasticsearchAdapter\Adapter
     */
    protected $connector;

    // @var \ElasticSearch\VuFindSearch\Backend\ElasticSearch\SearchBuilder
    protected $searchBuilder;

    /**
     * Backend constructor.
     *
     * @param \ElasticsearchAdapter\Adapter $esAdapter The Elastic Search
     *                                                 Adapter
     * @param array                         $templates The search templates
     *
     * @internal param $esHosts
     */
    public function __construct(Adapter $esAdapter, array $templates)
    {
        $this->connector = $esAdapter;
        $this->searchBuilder = new SearchBuilder($templates);
    }

    /**
     * Return the record collection factory.
     * Lazy loads a generic collection factory.
     *
     * @return RecordCollectionFactoryInterface
     */
    public function getRecordCollectionFactory()
    {
        if (!$this->collectionFactory) {
            $this->collectionFactory = new RecordCollectionFactory();
        }
        return $this->collectionFactory;
    }

    /**
     * Perform a search and return record collection.
     *
     * @param AbstractQuery $query  Search query
     * @param int           $offset Search offset
     * @param int           $limit  Search limit
     * @param ParamBag      $params Search backend parameters
     *
     * @return \VuFindSearch\Response\RecordCollectionInterface
     */
    public function search(
        AbstractQuery $query,
        $offset,
        $limit,
        ParamBag $params = null
    ) {
        $search = $this->searchBuilder->buildSearch(
            $query, $offset, $limit, $params
        );

        $result = $this->connector->search($search);
        $collection = $this->createRecordCollection($result);

        return $collection;
    }

    /**
     * Retrieve a single document.
     *
     * @param string   $id     Document identifier
     * @param ParamBag $params Search backend parameters
     *
     * @return \VuFindSearch\Response\RecordCollectionInterface
     */
    public function retrieve($id, ParamBag $params = null)
    {
        // TODO: Implement retrieve() method.
    }

    /**
     * Return similar records.
     *
     * @param string   $id     Id of record to compare with
     * @param ParamBag $params Search backend parameters
     *
     * @return RecordCollectionInterface
     */
    public function similar($id, ParamBag $params = null)
    {
        // TODO: Implement similar() method.
    }

    /**
     * Return random records.
     *
     * @param AbstractQuery $query  Search query
     * @param int           $limit  Search limit
     * @param ParamBag      $params Search backend parameters
     *
     * @return \VuFindSearch\Response\RecordCollectionInterface
     */
    public function random(
        AbstractQuery $query, $limit, ParamBag $params = null
    ) {
        // TODO: Implement random() method.
    }

    /**
     * Retrieve a batch of documents.
     *
     * @param array    $ids    Array of document identifiers
     * @param ParamBag $params Search backend parameters
     *
     * @return \VuFindSearch\Response\RecordCollectionInterface
     */
    public function retrieveBatch($ids, ParamBag $params = null)
    {
        // TODO: Implement retrieveBatch() method.
    }

    /**
     * Create record collection.
     *
     * @param ElasticsearchClientResult $result The search result
     *
     * @return RecordCollectionInterface
     */
    protected function createRecordCollection($result)
    {
        return $this->getRecordCollectionFactory()->factory($result);
    }
}
