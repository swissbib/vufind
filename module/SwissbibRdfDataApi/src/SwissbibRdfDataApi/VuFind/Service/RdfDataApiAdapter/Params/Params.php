<?php
namespace SwissbibRdfDataApi\VuFind\Service\RdfDataApiAdapter\Params;

use Iterator;

/**
 * Params interface
 *
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>, Markus Mächler <markus.maechler@students.fhnw.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php
 * @link     http://linked.swissbib.ch
 */
interface Params extends Iterator
{
    /**
     * @param string $name
     *
     * @return string|null
     */
    public function get(string $name);

    /**
     * @param string $name
     * @param string $value
     *
     * @return Params
     */
    public function set(string $name, string $value) : Params;

    /**
     * @param string $name
     *
     * @return bool
     */
    public function has(string $name) : bool;

    /**
     * @param string $name
     *
     * @return Params
     */
    public function remove(string $name) : Params;
}
