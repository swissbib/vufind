<?php
/**
 * RecordCollection.php
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
 * @package  ElasticSearch\VuFindSearch\Backend\ElasticSearch\Response\AdapterClientResult
 * @author   Christoph Boehm <cbo@outermedia.de>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.vufind.org  Main Page
 */
namespace ElasticSearch\VuFindSearch\Backend\ElasticSearch\Response\AdapterClientResult;

use ElasticsearchAdapter\Result\ElasticsearchClientResult;
use VuFindSearch\Exception\InvalidArgumentException;
use VuFindSearch\Response\AbstractRecordCollection;

/**
 * Class RecordCollection
 *
 * @category VuFind
 * @package  ElasticSearch\VuFindSearch\Backend\ElasticSearch\Response\AdapterClientResult
 * @author   Christoph Boehm <cbo@outermedia.de>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.vufind.org  Main Page
 */
class RecordCollection extends AbstractRecordCollection
{
    /**
     * Elasticsearch response.
     *
     * @var array
     */
    protected $response;

    /**
     * The number of records
     *
     * @var int
     */
    private $_resultTotal = 0;

    /**
     * Constructor.
     *
     * @param array $response Deserialized SOLR response
     *
     * @return void
     */
    public function __construct(ElasticsearchClientResult $response)
    {
        //$this->response = array_replace_recursive(static::$template, $response);
        //todo: how to get the offset from the ES response
        $this->offset = 0;
        $this->rewind();
    }

    /**
     * Return total number of records found.
     *
     * @return int
     */
    public function getTotal()
    {
        return $this->_resultTotal;
    }

    /**
     * Sets the number of records
     *
     * @param int $total The number of records
     *
     * @return void
     */
    public function setTotal(int $total)
    {
        $this->_resultTotal = $total;
    }

    /**
     * Return available facets.
     *
     * Returns an associative array with the internal field name as key. The
     * value is an associative array of the available facets for the field,
     * indexed by facet value.
     *
     * @return array
     */
    public function getFacets()
    {
        throw new \Exception('method currently not supported');
    }

    /**
     * Return record collection.
     *
     * @param array $response Deserialized JSON response
     *
     * @return RecordCollection
     */
    public function factory($response)
    {
        if (!is_array($response)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Unexpected type of value: Expected array, got %s',
                    gettype($response)
                )
            );
        }
        $collection = new $this->collectionClass($response);
        if (isset($response['response']['docs'])) {
            foreach ($response['response']['docs'] as $doc) {
                $collection->add(call_user_func($this->recordFactory, $doc));
            }
        }
        return $collection;
    }
}
