<?php

/**
 * RdfDataApiSearchBuilder
 *
 * PHP version 5
 *
 * Copyright (C) project swissbib, University Library Basel, Switzerland
 * http://www.swissbib.org  / http://www.swissbib.ch / http://www.ub.unibas.ch
 *
 * Date: 06.05.19
 * Time: 14:25
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License version 2,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @category Swissbib_VuFind2
 * @package  SwissbibRdfDataApi_VuFind_Service_RdfDataApiAdapter_SearchBuilder
 * @author   Günter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.swissbib.org
 */


namespace SwissbibRdfDataApi\VuFind\Service\RdfDataApiAdapter\SearchBuilder;

use SwissbibRdfDataApi\VuFind\Service\RdfDataApiAdapter\Query\AbstractQuery;
use SwissbibRdfDataApi\VuFind\Service\RdfDataApiAdapter\Search\RdfApiSearch;
//use ElasticsearchAdapter\Search\TemplateSearch;
use InvalidArgumentException;
use SwissbibRdfDataApi\VuFind\Service\RdfDataApiAdapter\Search\SearchTypeEnum;
use SwissbibRdfDataApi\VuFind\Service\RdfDataApiAdapter\Params\ParamBag;
//use VuFindSearch\Query\AbstractQuery;

/**
 * RdfDataApiSearchBuilder
 *
 * @category Swissbib_VuFind2
 * @package  SwissbibRdfDataApi_VuFind_Service_RdfDataApiAdapter_SearchBuilder
 * @author   Günter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org
 * @link     http://www.swissbib.ch
 */
class RdfDataApiSearchBuilder
{


    /**
     * @var \SwissbibRdfDataApi\VuFind\Service\RdfDataApiAdapter\Params\ParamBag
     */
    protected $params;

    /**
     * @var \SwissbibRdfDataApi\VuFind\Service\RdfDataApiAdapter\Search\SearchTypeEnum
     */
    protected $searchType;

    protected $config;

    protected $query;

    /**
     * RdfDataApiSearchBuilder constructor.
     * @param AbstractQuery $query
     * @param ParamBag $params
     * @param array $config
     * @throws \Exception
     */
    public function __construct(AbstractQuery $query,
                                ParamBag $params,
                                array $config)
    {
        $this->query = $query;
        $this->params = $params;
        $this->searchType = new SearchTypeEnum($query->getHandler());
        $this->config = $config;

    }

    /**
     * @param string $template
     *
     * @return RdfApiSearch
     *
     * @throws InvalidArgumentException if template is not found
     */
    public function buildSearch(
        $offset,
        $limit
    ) : RdfApiSearch {

        $searchTypeMethod = strtolower($this->searchType->getCurrectSearchType());
        $url = $this->{$searchTypeMethod}();

        //$templateSearch->prepare();
        $rdfApiSearch =  new RdfApiSearch($url);

        //todo: what about offset, limit and additional Query parameters

        return $rdfApiSearch;
    }

    /**
     * @return ParamBag
     */
    public function getParams() : ParamBag
    {
        return $this->params;
    }

    /**
     * @param ParamBag $params
     */
    public function setParams(ParamBag $params)
    {
        $this->params = $params;
    }

    private function getBaseUrl(): string
    {
        return $this->config["parameters"]["rdf_api_adapter.hosts"][0];
    }


    /**
     * Create Person search string
     *
     * @return string
     */
    public function person() : string
    {
        $urlPathPattern = $this->config["parameters"]["rdf_api_adapter.urls"]
            [strtoupper($this->searchType->getCurrectSearchType())];
        $urlPath = preg_replace(
            '/\?id/', $this->query->getString(),
            $urlPathPattern
        );
        return $this->getBaseUrl() . $urlPath;
    }

    public function id_search_person() : string
    {
        $urlPathPattern = $this->config["parameters"]["rdf_api_adapter.urls"]
        [strtoupper($this->searchType->getCurrectSearchType())];
        $urlPath = preg_replace(
            '/\?id/', $this->query->getString(),
            $urlPathPattern
        );
        return $this->getBaseUrl() . $urlPath;
    }

    public function bib_resources_by_author() : string
    {
        $urlPathPattern = $this->config["parameters"]["rdf_api_adapter.urls"]
        [strtoupper($this->searchType->getCurrectSearchType())];
        $urlPath = preg_replace(
            '/\?id/', $this->query->getString(),
            $urlPathPattern
        );
        return $this->getBaseUrl() . $urlPath;
    }

    public function ids_search_gnd() : string
    {
        $urlPathPattern = $this->config["parameters"]["rdf_api_adapter.urls"]
        [strtoupper($this->searchType->getCurrectSearchType())];
        $urlPath = preg_replace(
            '/\?ids/', $this->query->getString(),
            $urlPathPattern
        );
        return $this->getBaseUrl() . $urlPath;
    }

    public function ids_search_person() : string
    {
        $urlPathPattern = $this->config["parameters"]["rdf_api_adapter.urls"]
        [strtoupper($this->searchType->getCurrectSearchType())];
        $urlPath = preg_replace(
            '/\?ids/', $this->query->getString(),
            $urlPathPattern
        );
        return $this->getBaseUrl() . $urlPath;
    }

    public function person_by_genre() : string
    {
        $urlPathPattern = $this->config["parameters"]["rdf_api_adapter.urls"]
        [strtoupper($this->searchType->getCurrectSearchType())];
        $urlPath = preg_replace(
            '/\?id/', $this->query->getString(),
            $urlPathPattern
        );
        return $this->getBaseUrl() . $urlPath;
    }

    public function person_by_movement() : string
    {
        $urlPathPattern = $this->config["parameters"]["rdf_api_adapter.urls"]
        [strtoupper($this->searchType->getCurrectSearchType())];
        $urlPath = preg_replace(
            '/\?id/', $this->query->getString(),
            $urlPathPattern
        );
        return $this->getBaseUrl() . $urlPath;
    }


}