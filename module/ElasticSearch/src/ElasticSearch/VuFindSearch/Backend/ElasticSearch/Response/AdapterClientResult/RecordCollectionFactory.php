<?php
/**
 *
 * @category linked-swissbib
 * @package  Backend_Eleasticsearch_Response
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @author   Philipp Kuntschik <Philipp.Kuntschik@HTWChur.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://linked.swissbib.ch  Main Page
 */

namespace ElasticSearch\VuFindSearch\Backend\ElasticSearch\Response\AdapterClientResult;

use ElasticSearch\VuFind\RecordDriver\ElasticSearch;
use ElasticsearchAdapter\Result\ElasticsearchClientResult;
use VuFindSearch\Response\RecordCollectionFactoryInterface;
use VuFindSearch\Response\RecordCollectionInterface;

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
     * @param Callable $recordFactory Callback to construct records
     * @param string $collectionClass Class of collection
     *
     * @return void
     */
    public function __construct(
      $recordFactory = null,
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
     * @param ElasticsearchClientResult $response response data
     *
     * @return RecordCollectionInterface
     */
    public function factory($responses)
    {
        $collection = new $this->collectionClass($responses);
        $totalHits = $responses->getTotal();
        foreach ($responses->getHits() as $hit) {
            $collection->add(call_user_func($this->recordFactory, $hit));
        }
        $collection->setTotal($totalHits);

        return $collection;
    }
}
