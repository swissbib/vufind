<?php
/**
 * SearchBuilder.php
 *
 * PHP Version 7
 *
 * Copyright (C) swissbib 2018
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License version 2,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.    See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category VuFind
 * @package  ElasticSearch\VuFindSearch\Backend\ElasticSearch
 * @author   Christoph Boehm <cbo@outermedia.de>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.vufind.org  Main Page
 */
namespace ElasticSearch\VuFindSearch\Backend\ElasticSearch;

use ElasticsearchAdapter\Params\ArrayParams;
use ElasticsearchAdapter\Search\Search;
use ElasticsearchAdapter\SearchBuilder\TemplateSearchBuilder;
use VuFindSearch\ParamBag;
use VuFindSearch\Query\Query;

/**
 * Class SearchBuilder
 *
 * @category VuFind
 * @package  ElasticSearch\VuFindSearch\Backend\ElasticSearch
 * @author   Christoph Boehm <cbo@outermedia.de>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.vufind.org  Main Page
 */
class SearchBuilder
{
    /**
     * The templates
     *
     * @var array
     */
    private $_templates;

    private $_type_2_index_mapping;

    /**
     * SearchBuilder constructor.
     *
     * @param array $templates The templates
     */
    public function __construct(array $templates)
    {
        $this->_templates
            = $templates['parameters']['elasticsearch_adapter.templates'];
        //we use this additional configuration to figure out the index
        // related to the type used with ES version 5
        $this->_type_2_index_mapping
            = $templates['parameters']['mappings']['type_2_index_mapping'];
    }

    /**
     * Build the Search
     *
     * @param \VuFindSearch\Query\Query   $query  The query
     * @param int                         $offset The offset
     * @param int                         $limit  The limit
     * @param \VuFindSearch\ParamBag|null $params The params
     *
     * @return \ElasticsearchAdapter\Search\Search
     */
    public function buildSearch(
        Query $query, int $offset, int $limit, ParamBag $params = null
    ): Search {
        if ($params === null) {
            $params = new ParamBag();
        }

        $elasticSearchParams = new ArrayParams(
            [
                'index'  => $this->getIndex($query, $params),
                'type'   => $query->getHandler(),
                'size'   => $limit,
                'from'   => $offset,
                'q'      => $this->getQueryString($query),
                'fields' => $this->getFilters($query, $params)
            ]
        );

        $searchBuilder = new TemplateSearchBuilder(
            $this->_templates, $this->_type_2_index_mapping, $elasticSearchParams
        );

        $search = $searchBuilder->buildSearchFromTemplate(
            $this->getTemplate($query, $params)
        );

        return $search;
    }

    /**
     * Gets the query string
     *
     * @param AbstractQuery $query The query
     *
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
     * Gets the template query
     *
     * @param Query    $query  The query
     * @param ParamBag $params The params
     *
     * @return string
     */
    protected function getTemplate(Query $query, ParamBag $params)
    {
        // TODO Get default template from config
        //        return $query->getHandler() ? $query->getHandler() : "id";
        return $this->getFromParams("template", $params);
    }

    /**
     * Gets the filters
     *
     * @param Query    $query  The query
     * @param ParamBag $params The params
     *
     * @return mixed
     */
    protected function getFilters(Query $query, ParamBag $params)
    {
        return "";
    }

    /**
     * Gets the index
     *
     * @param Query    $query  The query
     * @param ParamBag $params The params
     *
     * @return mixed
     */
    protected function getIndex(Query $query, ParamBag $params)
    {
        return $this->getFromParams('index', $params);
    }

    /**
     * Gets the from params
     *
     * @param string   $name   The name
     * @param ParamBag $params The params
     *
     * @return mixed
     */
    protected function getFromParams(string $name, ParamBag $params)
    {
        return $params->get($name)[0];
    }
}
