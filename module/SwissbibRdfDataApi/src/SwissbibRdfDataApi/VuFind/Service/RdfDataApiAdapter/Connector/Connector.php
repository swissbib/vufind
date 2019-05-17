<?php
namespace SwissbibRdfDataApi\VuFind\Service\RdfDataApiAdapter\Connector;

use SwissbibRdfDataApi\VuFind\Service\RdfDataApiAdapter\Result\Result;
use SwissbibRdfDataApi\VuFind\Service\RdfDataApiAdapter\Search\Search;

/**
 * Connector interface
 *
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>, Markus Mächler <markus.maechler@students.fhnw.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php
 * @link     http://linked.swissbib.ch
 */
interface Connector
{
    public function search(Search $search) : Result;
}
