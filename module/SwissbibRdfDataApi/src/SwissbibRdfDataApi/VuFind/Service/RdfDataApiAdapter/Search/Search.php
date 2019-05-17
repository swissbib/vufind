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
     * @param string $url
     *
     * @return void
     */
    public function setUrl(string $url);

    /**
     * @return string
     */
    public function getUrl() : string;


}
