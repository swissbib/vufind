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
    ): Search {

        if ($params === null) {
            $params = new ParamBag();
        }

        $elasticSearchParams = new ArrayParams(
            [
            'index' => $this->getIndex($query, $params),
            'type' => $query->getHandler(),
            'size' => $limit,
            'from' => $offset,
            'q' => $this->getQueryString($query),
            'fields' => $this->getFilters($query, $params)
            ]
        );

        $searchBuilder = new TemplateSearchBuilder($this->templates, $elasticSearchParams);

        $search = $searchBuilder->buildSearchFromTemplate($this->getTemplate($query, $params));

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

    /**
     * @param Query $query
     * @return string
     */
    protected function getTemplate(Query $query, ParamBag $params)
    {
        // TODO Get default template from config
        //        return $query->getHandler() ? $query->getHandler() : "id";
        return $this->getFromParams("template", $params);
    }

    /**
     * @param Query    $query
     * @param ParamBag $params
     * @return mixed
     */
    private function getFilters(Query $query, ParamBag $params)
    {
        return "";
    }

    /**
     * @param Query    $query
     * @param ParamBag $params
     * @return mixed
     */
    protected function getIndex(Query $query, ParamBag $params)
    {
        return $this->getFromParams('index', $params);
    }

    /**
     * @param $name
     * @param ParamBag $params
     * @return mixed
     */
    protected function getFromParams($name, ParamBag $params)
    {
        return $params->get($name)[0];
    }
}
