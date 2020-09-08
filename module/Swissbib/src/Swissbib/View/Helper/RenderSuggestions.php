<?php
/**
 * FacetItem
 *
 * PHP version 7
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
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category Swissbib_VuFind
 * @package  View_Helper
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
namespace Swissbib\View\Helper;

use VuFind\Search\UrlQueryHelper;
use VuFindSearch\Query\Query;
use Laminas\View\Helper\AbstractHelper;

/**
 * Render suggestions
 * Based on renderSpellingSuggestions from VuFind
 *
 * @category Swissbib_VuFind
 * @package  View_Helper
 * @author   Lionel Walter <lionel.walter@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
class RenderSuggestions extends AbstractHelper
{
    /**
     * Render suggestions
     *
     * @param \VuFind\Search\Base\Results     $results Results object
     * @param \Laminas\View\Renderer\PhpRenderer $view    View renderer object
     *
     * @return string
     */
    public function __invoke($results, $view)
    {
        $spellingSuggestions = $results->getSpellingSuggestions();
        if (empty($spellingSuggestions)) {
            return '';
        }

        $html = '';

        $suggested = [];
        foreach ($spellingSuggestions as $term => $details) {
            foreach ($details['suggestions'] as $word => $data) {
                $suggested[$term] = $word;
                break;//only the first suggestion
            }
        }

        /**
         * Initial Query
         *
         * @var Query $initialQuery
         */
        $initialQuery = $results->getParams()->getQuery();
        $queryText = $initialQuery->getNormalizedString();

        /**
         * Url Query Helper
         *
         * @var UrlQueryHelper $queryUrl
         */
        $queryUrl = $results->getUrlQuery();

        foreach ($suggested as $term => $suggestion) {
            $queryText = str_replace($term, $suggestion, $queryText);
            $queryUrl = $queryUrl->replaceTerm($term, $suggestion, true);
        }

        $href=$queryUrl->getParams();

        $html .= '<a href="' . $href . '">' . $view->escapeHtml($queryText)
            . '</a>';

        return $html;
    }
}
