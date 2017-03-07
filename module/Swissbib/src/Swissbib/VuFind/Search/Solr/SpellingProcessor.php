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

use VuFindSearch\Backend\Solr\Response\Json\Spellcheck;
use VuFindSearch\Query\AbstractQuery;
use Zend\Config\Config;

/**
 * Extended version of the VuFind Solr Spelling Processor (based on
 * advanced Spellers like DirectIndexSpelling and .... )
 *
 * @category Swissbib_VuFind2
 * @package  VuFind_Search_Solr
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.vufind.org  Main Page
 */
class SpellingProcessor
{
    /**
     * SpellingResults
     *
     * @var SpellingResults
     */
    protected $spellingResults;

    /**
     * Spelling limit
     *
     * @var int
     */
    protected $spellingLimit = 3;

    /**
     * TermSpellingLimits
     *
     * @var int
     */
    protected $termSpellingLimits = 3;

    /**
     * Spell check words with numbers in them?
     *
     * @var bool
     */
    protected $spellSkipNumeric = true;

    /**
     * Offer expansions on terms as well as basic replacements?
     *
     * @var bool
     */
    protected $expand = true;

    /**
     * Show the full modified search phrase on screen rather then just the suggested
     * word?
     *
     * @var bool
     */
    protected $phrase = false;

    /**
     * Constructor
     *
     * @param SpellingResults $spellingResults Spelling configuration (optional)
     */
    public function __construct(SpellingResults $spellingResults)
    {
        $this->spellingResults = $spellingResults;
    }

    /**
     * Are we skipping numeric words?
     *
     * @return bool
     */
    public function shouldSkipNumericSpelling()
    {
        return $this->spellSkipNumeric;
    }

    /**
     * Get the spelling limit.
     *
     * @return int
     */
    public function getSpellingLimit()
    {
        return $this->spellingLimit;
    }

    /**
     * Input Tokenizer - Specifically for spelling purposes
     *
     * Because of its focus on spelling, these tokens are unsuitable
     * for actual searching. They are stripping important search data
     * such as joins and groups, simply because they don't need to be
     * spellchecked.
     *
     * @param string $input Query to tokenize
     *
     * @return array        Tokenized array
     */
    public function tokenize($input)
    {
        //at the moment not used by swissbib (maybe the blacklist - not used terms
        // like and / or / not .. but should be handled by the search engine

        return [];
    }

    /**
     * Get raw spelling suggestions for a query.
     *
     * @param Spellcheck    $spellcheck Complete spellcheck information
     * @param AbstractQuery $query      Query for which info should be retrieved
     *
     * @return array
     * @throws \Exception
     */
    public function getSuggestions(Spellcheck $spellcheck, AbstractQuery $query)
    {

        if (!$this->spellingResults->hasSuggestions()) {
            $this->spellingResults->setSpellingQuery($query);
            $i = 1;
            foreach ($spellcheck as $term => $info) {
                if ($term == "collation") {
                    if (is_array($info)) {
                        $this->spellingResults->addCollocationSOLRStructure($info);
                    }

                } elseif (++$i && $i <= $this->getSpellingLimit()
                    && array_key_exists("suggestion", $info)
                ) {
                    //no so called collation suggestions are based on the
                    // single term part of the spelling query
                    $numberTermSuggestions = 1;
                    foreach ($info['suggestion'] as $termSuggestion) {
                        $numberTermSuggestions++;
                        if ($numberTermSuggestions > $this->termSpellingLimits) {
                            break;
                        }
                        $this->spellingResults->addTerm(
                            $term, $termSuggestion['word'], $termSuggestion['freq']
                        );
                    }

                }

            }
        }

        return $this->spellingResults;
    }

    /**
     * Support method for getSuggestions()
     *
     * @param AbstractQuery $query Query for which info should be retrieved
     * @param array         $info  Spelling suggestion information
     *
     * @return array
     * @throws \Exception
     */
    protected function formatAndFilterSuggestions($query, $info)
    {
        // Validate response format
        if (isset($info['suggestion'][0]) && !is_array($info['suggestion'][0])) {
            throw new \Exception(
                'Unexpected suggestion format; spellcheck.extendedResults'
                . ' must be set to true.'
            );
        }
        $limit = $this->getSpellingLimit();
        $suggestions = [];
        foreach ($info['suggestion'] as $suggestion) {
            if (count($suggestions) >= $limit) {
                break;
            }
            $word = $suggestion['word'];
            if (!$this->shouldSkipTerm($query, $word, true)) {
                $suggestions[$word] = $suggestion['freq'];
            }
        }
        return $suggestions;
    }

    /**
     * Should we skip the specified term?
     *
     * @param AbstractQuery $query         Query for which info should be retrieved
     * @param string        $term          Term to check
     * @param bool          $queryContains Should we skip the term if it is found
     * in the query (true), or should we skip the term if it is NOT found in the
     * query (false)?
     *
     * @return bool
     */
    protected function shouldSkipTerm($query, $term, $queryContains)
    {
        // If term is numeric and we're in "skip numeric" mode, we should skip it:
        if ($this->shouldSkipNumericSpelling() && is_numeric($term)) {
            return true;
        }
        // We should also skip terms already contained within the query:
        return $queryContains == $query->containsTerm($term);
    }

    /**
     * Process spelling suggestions.
     *
     * @param array  $suggestions Raw suggestions from getSuggestions()
     * @param string $query       Spelling query
     * @param Params $params      Params helper object
     *
     * @return array
     */
    public function processSuggestions($suggestions, $query, Params $params)
    {
        $returnArray = [];
        foreach ($suggestions as $term => $details) {
            // Find out if our suggestion is part of a token
            $inToken = false;
            $targetTerm = "";
            foreach ($this->tokenize($query) as $token) {
                // TODO - Do we need stricter matching here, similar to that in
                // \VuFindSearch\Query\Query::replaceTerm()?
                if (stripos($token, $term) !== false) {
                    $inToken = true;
                    // We need to replace the whole token
                    $targetTerm = $token;
                    // Go and replace this token
                    $returnArray = $this->doSingleReplace(
                        $term, $targetTerm, $inToken, $details, $returnArray, $params
                    );
                }
            }
            // If no tokens were found, just look for the suggestion 'as is'
            if ($targetTerm == "") {
                $targetTerm = $term;
                $returnArray = $this->doSingleReplace(
                    $term, $targetTerm, $inToken, $details, $returnArray, $params
                );
            }
        }

        return $returnArray;
    }

    /**
     * Process one instance of a spelling replacement and modify the return
     *   data structure with the details of what was done.
     *
     * @param string $term        The actually term we're replacing
     * @param string $targetTerm  The term above, or the token it is inside
     * @param bool   $inToken     Flag for whether the token or term is used
     * @param array  $details     The spelling suggestions
     * @param array  $returnArray Return data structure so far
     * @param Params $params      Params helper object
     *
     * @return array              $returnArray modified
     */
    protected function doSingleReplace($term, $targetTerm, $inToken, $details,
        $returnArray, Params $params
    ) {
        $returnArray[$targetTerm]['freq'] = $details['freq'];
        foreach ($details['suggestions'] as $word => $freq) {
            // If the suggested word is part of a token
            if ($inToken) {
                // We need to make sure we replace the whole token
                $replacement = str_replace($term, $word, $targetTerm);
            } else {
                $replacement = $word;
            }
            //  Do we need to show the whole, modified query?
            if ($this->phrase) {
                $label = $params->getDisplayQueryWithReplacedTerm(
                    $targetTerm, $replacement
                );
            } else {
                $label = $replacement;
            }
            // Basic spelling suggestion data
            $returnArray[$targetTerm]['suggestions'][$label] = [
                'freq' => $freq,
                'new_term' => $replacement
            ];

            // Only generate expansions if enabled in config
            if ($this->expand) {
                // Parentheses differ for shingles
                if (strstr($targetTerm, " ") !== false) {
                    $replacement = "(($targetTerm) OR ($replacement))";
                } else {
                    $replacement = "($targetTerm OR $replacement)";
                }
                $returnArray[$targetTerm]['suggestions'][$label]['expand_term']
                    = $replacement;
            }
        }

        return $returnArray;
    }
}
