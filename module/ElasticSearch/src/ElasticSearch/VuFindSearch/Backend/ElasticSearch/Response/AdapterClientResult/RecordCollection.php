<?php
/**
 *
 * @category linked-swissbib
 * @package  Backend_Eleasticsearch_Response
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://linked.swissbib.ch  Main Page
 */
namespace ElasticSearch\VuFindSearch\Backend\ElasticSearch\Response\AdapterClientResult;

use ElasticsearchAdapter\Result\ElasticsearchClientResult;
use VuFindSearch\Exception\InvalidArgumentException;
use VuFindSearch\Response\AbstractRecordCollection;

class RecordCollection extends AbstractRecordCollection {
    /**
     * Elasticsearch response.
     *
     * @var array
     */
    protected $response;

    private $resultTotal = 0;

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
        return $this->resultTotal;
    }

    public function setTotal($total) {
        $this->resultTotal = $total;
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
