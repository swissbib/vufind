<?php
/**
 * RecordCollectionFactory.php
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
// @codingStandardsIgnoreLineuse
namespace ElasticSearch\VuFindSearch\Backend\ElasticSearch\Response\AdapterClientResult;

use VuFindSearch\Response\RecordCollectionFactoryInterface;
use VuFindSearch\Response\RecordCollectionInterface;

/**
 * Class RecordCollectionFactory
 *
 * @category VuFind
 * @package  ElasticSearch\VuFindSearch\Backend\ElasticSearch\Response\AdapterClientResult
 * @author   Christoph Boehm <cbo@outermedia.de>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.vufind.org  Main Page
 */
class RecordCollectionFactory implements RecordCollectionFactoryInterface
{
    /**
     * Factory to turn data into a record object.
     *
     * @var Callable
     */
    protected $recordFactory;

    /**
     * Class of collection.
     *
     * @var string
     */
    protected $collectionClass;

    /**
     * Constructor.
     *
     * @param Callable $recordFactory   Callback to construct records
     * @param string   $collectionClass Class of collection
     *
     * @return void
     */
    public function __construct(
        $recordFactory = null,
        // @codingStandardsIgnoreLineuse
        $collectionClass = 'ElasticSearch\VuFindSearch\Backend\ElasticSearch\Response\AdapterClientResult\RecordCollection'
    ) {
        if (null === $recordFactory) {
            $this->recordFactory = function ($data) {
                return new Record($data);
            };
        } else {
            $this->recordFactory = $recordFactory;
        }
        $this->collectionClass = $collectionClass;
    }

    /**
     * Convert a response into a record collection.
     *
     * @param mixed $response The response data
     *
     * @return RecordCollectionInterface
     */
    public function factory($response)
    {
        $collection = new $this->collectionClass($response);
        $totalHits = $response->getTotal();
        foreach ($response->getHits() as $hit) {
            $collection->add(call_user_func($this->recordFactory, $hit));
        }
        $collection->setTotal($totalHits);

        return $collection;
    }
}
