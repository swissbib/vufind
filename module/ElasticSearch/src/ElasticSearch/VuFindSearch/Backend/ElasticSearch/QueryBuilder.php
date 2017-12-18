<?php
/**
 * Created by IntelliJ IDEA.
 * User: boehm
 * Date: 23.11.17
 * Time: 13:41
 */
namespace ElasticSearch\VuFindSearch\Backend\ElasticSearch;

use VuFindSearch\Backend\Solr\QueryBuilderInterface;
use VuFindSearch\ParamBag;
use VuFindSearch\Query\AbstractQuery;

class QueryBuilder implements QueryBuilderInterface
{
    /**
     * @var array
     */
    private $templates;

    /**
     * QueryBuilder constructor.
     */
    public function __construct(array $templates)
    {

        $this->templates = $templates;
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
        $paramBag->set("templates", $this->templates);
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
