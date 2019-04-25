<?php
namespace SwissbibRdfDataApi\VuFind\Service\RdfDataApiAdapter\Params\Modifiers;

use ElasticsearchAdapter\Params\Params;

/**
 * DefaultModifier
 *
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>, Markus Mächler <markus.maechler@students.fhnw.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php
 * @link     http://linked.swissbib.ch
 */
class DefaultModifier implements Modifier
{
    public function modify(Params $params, array $parameters) : string
    {
        if (isset($parameters[0]) && $params->has($parameters[0])) {
            return $params->get($parameters[0]);
        } elseif (isset($parameters[1])) {
            return $parameters[1];
        } else {
            return '';
        }
    }
}
