<?php
namespace SwissbibRdfDataApi\VuFind\Service\RdfDataApiAdapter\Result;

/**
 * TemplateQuery
 *
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>, Markus Mächler <markus.maechler@students.fhnw.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php
 * @link     http://linked.swissbib.ch
 */
class RdfDataApiResult implements Result
{
    /**
     * @var array
     */
    protected $result;

    /**
     * @param array $result
     */
    public function __construct(array $result)
    {
        $this->result = $result;
    }

    /**
     * @return int
     */
    public function getTotal() : int
    {
        //todo: implement this
        return 0;
        //return $this->result['hits']['total'];
    }

    /**
     * @return int
     */
    public function getTook() : int
    {
        return $this->result['took'];
    }

    /**
     * @return bool
     */
    public function getTimedOut() : bool
    {
        return $this->result['timed_out'];
    }

    /**
     * @return float
     */
    public function getMaxScore() : float
    {
        return $this->result['hits']['max_score'];
    }

    /**
     * @return array
     */
    public function getHits() : array
    {
        return $this->result['hits']['hits'] ?? [];
    }

    /**
     * @return array
     */
    public function getRawResult() : array
    {
        return $this->result;
    }
}
