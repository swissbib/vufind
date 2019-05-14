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

use SwissbibRdfDataApi\VuFind\Service\RdfDataApiAdapter\Search\RdfApiSearch;
use ElasticsearchAdapter\Search\TemplateSearch;
use InvalidArgumentException;
use SwissbibRdfDataApi\VuFind\Service\RdfDataApiAdapter\Params\Params;
use SwissbibRdfDataApi\VuFind\Service\RdfDataApiAdapter\Search\SearchTypeEnum;
use VuFindSearch\ParamBag;
use VuFindSearch\Query\AbstractQuery;

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
     * @var \SwissbibRdfDataApi\VuFind\Service\RdfDataApiAdapter\Params\Params;
     */
    protected $params;

    protected $searchType;

    /**
     * @param array $templates
     * @param Params $params
     */
    public function __construct()
    {
        //$this->params = $params;
        //$this->searchType = $searchType;
    }

    /**
     * @param string $template
     *
     * @return RdfApiSearch
     *
     * @throws InvalidArgumentException if template is not found
     */
    public function buildSearch(
        AbstractQuery $query,
        $offset,
        $limit,
        ParamBag $params = null

    ) : RdfApiSearch
    {
        //if (!isset($this->templates[$template])) {
        //    throw new InvalidArgumentException('No template with name "' . $template . '" found.');
        //}

        //$templateSearch = new TemplateSearch($this->templates[$template], $this->params);

        //$templateSearch->prepare();
        $rdfApiSearch =  new RdfApiSearch();


        return $rdfApiSearch;
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