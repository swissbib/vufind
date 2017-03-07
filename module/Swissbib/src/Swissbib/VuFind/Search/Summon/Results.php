<?php
/**
 * Summon Search Results
 *
 * PHP version 5
 *
 * Copyright (C) project swissbib, University Library Basel, Switzerland
 * http://www.swissbib.org  / http://www.swissbib.ch / http://www.ub.unibas.ch
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
 * @package  VuFind_Search_Summon
 * @author   Oliver Schihin <oliver.schihin@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.swissbib.org
 * @link     http://www.vufind.org  Main Page
 */
namespace Swissbib\Vufind\Search\Summon;

use SerialsSolutions_Summon_Query as SummonQuery,
    VuFind\Exception\RecordMissing as RecordMissingException,
    VuFind\Search\Base\Results as BaseResults,
    VuFind\Solr\Utils as SolrUtils,
    VuFindSearch\ParamBag;

use VuFind\Search\Summon\Results as VFSummonResults;

/**
 * Summon Search Results
 *
 * @category Swissbib_VuFind2
 * @package  VuFind_Search_SolrClassification
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.swissbib.org  Main Page
 */
class Results extends VFSummonResults
{
    /**
     * Results target
     *
     * @var String
     */
    protected $target = 'summon';

    /**
     * Turn the list of spelling suggestions into an array of urls
     *   for on-screen use to implement the suggestions.
     *
     * @return array Spelling suggestion data arrays
     */
    public function getSpellingSuggestions()
    {
        $retVal = [];
        foreach ($this->getRawSuggestions() as $term => $details) {
            foreach ($details['suggestions'] as $word) {
                // Strip escaped characters in the search term (for example, "\:")
                $term = stripcslashes($term);
                $word = stripcslashes($word);
                // strip enclosing parentheses
                $from = [ '/^\(/', '/\)$/'];
                $to = ['',''];
                $term = preg_replace($from, $to, $term);
                $word = preg_replace($from, $to, $word);
                $retVal[$term]['suggestions'][$word] = ['new_term' => $word];
            }
        }
        return $retVal;
    }

    /**
     * GetTarget
     *
     * @return String $target
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * GetMyLibrariesFacets
     *
     * @return array
     */
    public function getMyLibrariesFacets()
    {
        return [];
    }

}
