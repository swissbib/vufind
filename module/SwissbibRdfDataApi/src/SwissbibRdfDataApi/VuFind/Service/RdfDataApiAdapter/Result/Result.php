<?php
namespace SwissbibRdfDataApi\VuFind\Service\RdfDataApiAdapter\Result;

/**
 * Response
 *
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>, Markus Mächler <markus.maechler@students.fhnw.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php
 * @link     http://linked.swissbib.ch
 */
interface Result
{
    /**
     * @return int
     */
    public function getTotal() : int;

    /**
     * @return int
     */
    public function getTook() : int;

    /**
     * @return bool
     */
    public function getTimedOut() : bool;

    /**
     * @return float
     */
    public function getMaxScore() : float;

    /**
     * @return array
     */
    public function getHits() : array;

    /**
     * @return array
     */
    public function getRawResult() : array;
}
