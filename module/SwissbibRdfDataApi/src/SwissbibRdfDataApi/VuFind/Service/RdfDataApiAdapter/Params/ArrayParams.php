<?php
namespace SwissbibRdfDataApi\VuFind\Service\RdfDataApiAdapter\Params;

use ArrayIterator;

/**
 * ArrayParams
 *
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>, Markus Mächler <markus.maechler@students.fhnw.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php
 * @link     http://linked.swissbib.ch
 */
class ArrayParams extends ArrayIterator implements Params
{
    /**
     * @inheritdoc
     */
    public function get(string $name)
    {
        return $this->offsetExists($name) ? $this->offsetGet($name) : null;
    }

    /**
     * @inheritdoc
     */
    public function set(string $name, string $value) : Params
    {
        $this->offsetSet($name, $value);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function has(string $name) : bool
    {
        return $this->offsetExists($name);
    }

    /**
     * @inheritdoc
     */
    public function remove(string $name) : Params
    {
        if ($this->offsetExists($name)) {
            $this->offsetUnset($name);
        }

        return $this;
    }
}
