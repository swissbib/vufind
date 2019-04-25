<?php
namespace SwissbibRdfDataApi\VuFind\Service\RdfDataApiAdapter\SearchBuilder;

use ElasticsearchAdapter\Params\Params;
use ElasticsearchAdapter\Search\Search;
use ElasticsearchAdapter\Search\TemplateSearch;
use InvalidArgumentException;

/**
 * TemplateSearchBuilder
 *
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>, Markus Mächler <markus.maechler@students.fhnw.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php
 * @link     http://linked.swissbib.ch
 */
class TemplateSearchBuilder
{
    /**
     * @var array
     */
    protected $templates = [];

    /**
     * @var Params
     */
    protected $params;

    /**
     * @param array $templates
     * @param Params $params
     */
    public function __construct(array $templates, Params $params = null)
    {
        $this->templates = $templates;
        $this->params = $params;
    }

    /**
     * @param string $template
     *
     * @return Search
     *
     * @throws InvalidArgumentException if template is not found
     */
    public function buildSearchFromTemplate(string $template) : Search
    {
        if (!isset($this->templates[$template])) {
            throw new InvalidArgumentException('No template with name "' . $template . '" found.');
        }

        $templateSearch = new TemplateSearch($this->templates[$template], $this->params);

        $templateSearch->prepare();

        return $templateSearch;
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
    public function setParams(Params $params)
    {
        $this->params = $params;
    }
}
