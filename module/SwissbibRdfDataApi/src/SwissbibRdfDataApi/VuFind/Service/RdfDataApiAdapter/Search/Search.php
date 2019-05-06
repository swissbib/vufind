<?php
namespace SwissbibRdfDataApi\VuFind\Service\RdfDataApiAdapter\Search;

use SwissbibRdfDataApi\VuFind\Service\RdfDataApiAdapter\Query\Query;

/**
 * Request
 *
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>, Markus Mächler <markus.maechler@students.fhnw.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php
 * @link     http://linked.swissbib.ch
 */
interface Search
{

    /**
     * @param int $size
     *
     * @return void
     */
    public function setSize(int $size);

    /**
     * @return int
     */
    public function getSize() : int;

    /**
     * @param int $from
     *
     * @return void
     */
    public function setFrom(int $from);

    /**
     * @return int
     */
    public function getFrom() : int;

    /**
     * @param Query $query
     *
     * @return void
     */
    public function setQuery(Query $query);

    /**
     * @return Query
     */
    public function getQuery() : Query;


    public function getSearchType() : SearchType;


    public function setSearchType(SearchType $type);

}
