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
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category VuFind
 * @package  SwissbibRdfDataApi\VuFindSearch\Backend\ElasticSearch
 * @author   Günter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.vufind.org  Main Page
 */
namespace SwissbibRdfDataApi\VuFindSearch\Backend\SwissbibRdfDataApi;

use SwissbibRdfDataApi\VuFind\Service\RdfDataApiAdapter\Result\RdfDataApiResult;
// @codingStandardsIgnoreLineuse
use SwissbibRdfDataApi\VuFind\Service\RdfDataApiAdapter\SearchBuilder\RdfDataApiSearchBuilder;

use SwissbibRdfDataApi\VuFind\Service\RdfDataApiAdapter\Adapter;
// @codingStandardsIgnoreLineuse
use SwissbibRdfDataApi\VuFindSearch\Backend\SwissbibRdfDataApi\Response\AdapterClientResult\RecordCollectionFactory;
use VuFindSearch\Backend\AbstractBackend;
use VuFindSearch\Feature\RandomInterface;
use VuFindSearch\Response\RecordCollectionInterface;
use VuFindSearch\Feature\RetrieveBatchInterface;
use VuFindSearch\Feature\SimilarInterface;
use VuFindSearch\ParamBag;
use VuFindSearch\Query\AbstractQuery;
use VuFindSearch\Response\RecordCollectionFactoryInterface;
// @codingStandardsIgnoreLineuse
use SwissbibRdfDataApi\VuFind\Service\RdfDataApiAdapter\Search\Search as RdfApiSearchInterface;

use SwissbibRdfDataApi\VuFind\Service\RdfDataApiAdapter\Result\Result as IApiResult;

/**
 * Class Backend
 *
 * @category VuFind
 * @package  SwissbibRdfDataApi\VuFindSearch\Backend\ElasticSearch
 * @author   Günter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.vufind.org  Main Page
 */
class Backend extends AbstractBackend
    implements SimilarInterface, RetrieveBatchInterface, RandomInterface
{
    /**
     * The Api Adapter
     *
     * @var Adapter
     */
    protected $apiAdapter;

    /**
     * SearchObjectBuilder
     *
     * @var RdfDataApiSearchBuilder $searchBuilder
     */
    protected $searchBuilder;

    /**
     * Backend constructor.
     *
     * @param Adapter $adapter the api search
     *
     * @internal param $esHosts
     */
    public function __construct(Adapter $adapter)
    {
        $this->apiAdapter = $adapter;
        $this->searchBuilder = new RdfDataApiSearchBuilder();

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

        /**
         * SearchObject
         *
         * @var RdfApiSearchInterface $search
         */
        $search = $this->searchBuilder->buildSearch(
            $query, $offset, $limit, $params
        );

        $result = $this->apiAdapter->search($search);
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
     * @param IApiResult $result The search result
     *
     * @return RecordCollectionInterface
     */
    protected function createRecordCollection($result)
    {
        return $this->getRecordCollectionFactory()->factory($result);
    }
}
