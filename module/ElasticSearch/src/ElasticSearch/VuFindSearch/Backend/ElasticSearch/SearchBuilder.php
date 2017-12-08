<?php
/**
 * Created by IntelliJ IDEA.
 * User: boehm
 * Date: 06.12.17
 * Time: 11:03
 */
namespace ElasticSearch\VuFindSearch\Backend\ElasticSearch;

use ElasticsearchAdapter\Params\ArrayParams;
use ElasticsearchAdapter\Search\Search;
use ElasticsearchAdapter\SearchBuilder\TemplateSearchBuilder;
use VuFindSearch\ParamBag;
use VuFindSearch\Query\Query;

class SearchBuilder
{
    /**
     * @var array
     */
    private $templates;

    /**
     * SearchBuilder constructor.
     */
    public function __construct(array $templates)
    {
        $this->templates = $templates;
    }

    public function buildSearch(
        Query $query,
        $offset,
        $limit,
        ParamBag $params = null
    ) : Search {
    
        if ($params === null) {
            $params = new ParamBag();
        }
        if (!$params->hasParam('template')) {
            $params->add('template', $this->templates['default_template']);
        }
        if (!$params->hasParam('index')) {
            $params->add('index', $this->templates['default_index']);
        }

        $elasticSearchParams = new ArrayParams(
            [
            'index' => $params->get('index')[0],
            'type' => $query->getHandler(),
            'size' => $limit,
            'from' => $offset,
            'id' => $this->getQueryString($query),
            ]
        );

        $searchBuilder = new TemplateSearchBuilder($this->templates, $elasticSearchParams);

        $search = $searchBuilder->buildSearchFromTemplate($params->get('template')[0]);

        return $search;
    }

    /**
     * @param AbstractQuery $query
     * @return mixed
     */
    protected function getQueryString(Query $query)
    {

        $queryString = $query->getString();
        if (preg_match('/\[(.*)\]/', $queryString, $matches)) {
            return array_map("trim", explode(',', $matches[1]));
        }
        return $queryString;
    }
}
