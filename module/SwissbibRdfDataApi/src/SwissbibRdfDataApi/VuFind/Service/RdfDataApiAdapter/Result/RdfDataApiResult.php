<?php
namespace SwissbibRdfDataApi\VuFind\Service\RdfDataApiAdapter\Result;


use ML\JsonLD\JsonLD;

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
     * @var stdClass
     */
    protected $result;

    /**
     * @param array $result
     */
    public function __construct(\stdClass $result)
    {
        $this->result = $result;

    }

    /**
     * @return int
     */
    public function getTotal() : int
    {
        return isset($this->result->totalItems) ? $this->result->totalItems : 0;
        //return $this->result['hits']['total'];
    }

    /**
     * @return int
     */
    public function getTook() : int
    {
        throw new \Exception("not implemented so far");

        //return $this->result['took'];
    }

    /**
     * @return bool
     */
    public function getTimedOut() : bool
    {
        throw new \Exception("not implemented so far");

        //return $this->result['timed_out'];
    }

    /**
     * @return float
     */
    public function getMaxScore() : float
    {
        throw new \Exception("not implemented so far");
        //return $this->result['hits']['max_score'];
    }

    /**
     * @return array
     */
    public function getHits() : array
    {
        return $this->result->member ?? [];
    }

    /**
     * @return array
     */
    public function getRawResult() : array
    {
        //do we need this with the new API?
        throw new \Exception("not implemented so far");

        //return $this->result;
    }
}
