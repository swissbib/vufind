<?php
namespace SwissbibRdfDataApi\VuFind\Service\RdfDataApiAdapter\Params;

use SwissbibRdfDataApi\VuFind\Service\RdfDataApiAdapter\Params\Modifiers\Modifier;

/**
 * ParamsReplacer
 *
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>, Markus Mächler <markus.maechler@students.fhnw.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php
 * @link     http://linked.swissbib.ch
 */
class ParamsReplacer
{
    /**
     * @var Params
     */
    protected $params;

    /**
     * @var Modifier[]
     */
    protected $modifiers = [];

    /**
     * @param Params $params
     */
    public function __construct(Params $params = null)
    {
        $this->params = $params;
    }

    /**
     * @param string|array $raw
     *
     * @return string|array
     */
    public function replace($raw)
    {
        if (is_array($raw)) {
            $replaced = [];

            foreach ($raw as $key => $value) {
                $key = $this->replaceSingleParam($key);
                $value = $this->replaceSingleParam($value);

                $replaced[$key] = $value;
            }

            return $replaced;
        } elseif (is_string($raw)) {
            return $this->replaceSingleParam($raw);
        }

        return $raw;
    }

    /**
     * @return Params
     */
    public function getParams() : Params
    {
        return $this->params;
    }

    /**
     * @param Params $params
     */
    public function setParams($params)
    {
        $this->params = $params;
    }

    /**
     * @param string $raw
     *
     * @return string|array
     */
    protected function replaceSingleParam(string $raw)
    {
        if ($this->params === null) {
            return $raw;
        }

        $matches = [];

        if (preg_match('/^{(\w*)}$/', $raw, $matches)) {
            $variableName = $matches[1];

            if ($this->params->has($variableName)) {
                return $this->params->get($variableName);
            }
        } elseif (preg_match('/^{(\w*)\(([\w\s,]*)\)}$/', $raw, $matches)) {
            $modifierName = $matches[1];
            $parameters = explode(',', $matches[2]);
            $parameters = array_map('trim', $parameters);

            if (!isset($this->modifiers[$modifierName])) {
                $modifierClass = '\\ElasticsearchAdapter\\Params\\Modifiers\\' . ucfirst($modifierName) . 'Modifier';
                $this->modifiers[$modifierName] = new $modifierClass();
            }

            return $this->modifiers[$modifierName]->modify($this->params, $parameters);
        }

        return $raw;
    }
}
