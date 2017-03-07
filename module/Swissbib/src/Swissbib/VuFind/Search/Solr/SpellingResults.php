<?php
/**
 * Solr spelling processor.
 *
 * PHP version 5
 *
 * Copyright (C) project swissbib, 2015.
 *
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
 * @package  VuFind_Search_Solr
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.swissbib.org  Main Page
 */
namespace Swissbib\VuFind\Search\Solr;

use VuFindSearch\Query\AbstractQuery;

/**
 * Spelling results is the container type to collect spelling suggestions provided
 * by the SOLR SearchEngine
 * Actually we distinguish collocation suggestions (multiple terms in sequence or
 * simple single term suggestions
 * term suggestions are variants of a token part of the original query
 *
 * @category Swissbib_VuFind2
 * @package  VuFind_Search_Solr
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.swissbib.org  Main Page
 */
class SpellingResults
{
    /**
     * Holds the collocation values and number of documents
     *
     * @var array
     */
    protected $collocations = [];

    /**
     * Holds the suggested term values related to their key with number
     * of documents to be expected
     *
     * @var array
     */
    protected $terms = [];

    /**
     * SpellingQuery
     *
     * @var AbstractQuery
     */
    protected $spellingQuery;

    /**
     * AddCollocation
     * method expects collocation and frequency explicitly
     * (processing has to be done by the client)
     *
     * @param String $collocation Collocation
     * @param String $frequency   Frequency
     *
     * @return void
     */
    public function addCollocation($collocation, $frequency)
    {
        $this->collocations[] = [$collocation, $frequency];
    }

    /**
     * AddCollocationSOLRStructure
     *
     * @param array $solrInfoStructure SolrInfoStructure
     *
     * @return void
     */
    public function addCollocationSOLRStructure(array $solrInfoStructure)
    {
        $collationSuggestion = [];
        //if term indicates a collation the info variable (again an array)
        // consists of three parts
        //[0][0]    => "collationQuery" string as key
        //[0][1]    => the collation query provided by SOLR istself
        //[1][0]    =>  "hits"  string as key
        //[1][1]    => number of hits for the collation query suggested by SOLR
        //[2][..]   => a list of so called "misspellingAndCorrections" for each
        // of the tokens part of the collation query
        //I don't use this at the moment
        foreach ($solrInfoStructure as $infoValues) {
            if ($infoValues[0] == "collationQuery") {
                $collationSuggestion["query"] = $infoValues[1];
            } elseif ($infoValues[0] == "hits") {
                $collationSuggestion["hits"] = $infoValues[1];
            }
        }

        if (array_key_exists("query", $collationSuggestion)
            && array_key_exists("hits", $collationSuggestion)
        ) {
            $this->collocations[] = $collationSuggestion;
        }
    }

    /**
     * AddTerm
     *
     * @param String $key       Key
     * @param String $value     Value
     * @param String $frequency Frequency
     *
     * @return void
     */
    public function addTerm($key, $value, $frequency)
    {
        $this->terms[$key][] = ['query' => $value, 'hits' => $frequency];
    }

    /**
     * HasSuggestions
     *
     * @return bool
     */
    public function hasSuggestions()
    {
        return count($this->collocations) > 0 || count($this->terms) > 0;
    }

    /**
     * HasCollocations
     *
     * @return bool
     */
    public function hasCollocations()
    {
        return count($this->collocations) > 0;
    }

    /**
     * HasTerms
     *
     * @return bool
     */
    public function hasTerms()
    {
        return count($this->terms) > 0;
    }

    /**
     * SetSpellingQuery
     *
     * @param AbstractQuery $query Query
     *
     * @return void
     */
    public function setSpellingQuery(AbstractQuery $query)
    {
        $this->spellingQuery = $query;
    }

    /**
     * GetSpellingQuery
     *
     * @return AbstractQuery
     */
    public function getSpellingQuery()
    {
        return $this->spellingQuery;
    }

    /**
     * GetCollocationSuggestions
     *
     * @return array
     */
    public function getCollocationSuggestions()
    {
        return $this->collocations;
    }

    /**
     * GetTermSuggestions
     *
     * @return array
     */
    public function getTermSuggestions()
    {
        return $this->terms;
    }
}
