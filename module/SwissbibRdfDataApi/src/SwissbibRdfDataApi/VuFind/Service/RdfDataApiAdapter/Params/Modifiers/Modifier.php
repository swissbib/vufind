<?php
namespace SwissbibRdfDataApi\VuFind\Service\RdfDataApiAdapter\Params\Modifiers;

use ElasticsearchAdapter\Params\Params;

/**
 * Modifier
 *
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>, Markus Mächler <markus.maechler@students.fhnw.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php
 * @link     http://linked.swissbib.ch
 */
interface Modifier
{
    public function modify(Params $params, array $parameters) : string;
}
