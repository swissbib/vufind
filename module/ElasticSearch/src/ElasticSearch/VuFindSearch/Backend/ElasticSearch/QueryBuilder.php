<?php
/**
 * QueryBuilder.php
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
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA    02111-1307    USA
 *
 * @category VuFind
 * @package  ElasticSearch\VuFindSearch\Backend\ElasticSearch
 * @author   Christoph Boehm <cbo@outermedia.de>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.vufind.org  Main Page
 */
namespace ElasticSearch\VuFindSearch\Backend\ElasticSearch;

use VuFindSearch\Backend\Solr\QueryBuilderInterface;
use VuFindSearch\ParamBag;
use VuFindSearch\Query\AbstractQuery;

/**
 * Class QueryBuilder
 *
 * @category VuFind
 * @package  ElasticSearch\VuFindSearch\Backend\ElasticSearch
 * @author   Christoph Boehm <cbo@outermedia.de>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.vufind.org  Main Page
 */
class QueryBuilder implements QueryBuilderInterface
{
    /**
     * The templates
     *
     * @var array
     */
    private $_templates;

    /**
     * QueryBuilder constructor.
     *
     * @param array $templates The templates
     */
    public function __construct(array $templates)
    {

        $this->_templates = $templates;
    }

    /**
     * Build SOLR query based on VuFind query object.
     *
     * @param AbstractQuery $query Query object
     *
     * @return ParamBag
     */
    public function build(AbstractQuery $query)
    {
        $paramBag = new ParamBag();
        $paramBag->set("templates", $this->_templates);
        // TODO Very basic approach
        $queryString = $query->getString();
        $paramBag->set('q', $queryString);
        $paramBag->set('type', $query->getHandler());
        return $paramBag;
    }

    /**
     * Control whether or not the QueryBuilder should create an hl.q parameter
     * when the main query includes clauses that should not be factored into
     * highlighting. (Turned off by default).
     *
     * @param bool $enable Should highlighting query generation be enabled?
     *
     * @return void
     */
    public function setCreateHighlightingQuery($enable)
    {
        // TODO: Implement setCreateHighlightingQuery() method.
    }

    /**
     * Control whether or not the QueryBuilder should create a spellcheck.q
     * parameter. (Turned off by default).
     *
     * @param bool $enable Should spelling query generation be enabled?
     *
     * @return void
     */
    public function setCreateSpellingQuery($enable)
    {
        // TODO: Implement setCreateSpellingQuery() method.
    }
}

