<?php
/**
 * SortAndPrepareFacetList
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
 * @package  View_Helper
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
namespace Swissbib\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Vufind\Search\Base\Results;

/**
 * Improved version of VuFind\View\Helper\Root\SortFacetList
 * Add url and sort, but keep all data
 *
 * @category Swissbib_VuFind2
 * @package  View_Helper
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
class SortAndPrepareFacetList extends AbstractHelper
{
    /**
     * Sort and extend facet list
     *
     * @param Results $results     VuFind\Search\Solr\Results
     * @param String  $field       Facet group ID, e.g. 'navSubidsbb'
     * @param Array   $list        Contained items of the facet group
     * @param String  $searchRoute E.g. 'search-results'
     * @param Array   $routeParams RouteParams
     *
     * @return Array
     */
    public function __invoke(Results $results, $field, array $list, $searchRoute,
        array $routeParams = []
    ) {
        $facets = [];
        // Avoid limit on URL
        $urlHelper = $this->getView()->plugin('url');
        $baseRoute = $urlHelper($searchRoute, $routeParams);

        foreach ($list as $facetItem) {
            $facetItem['url'] = $baseRoute . $results->getUrlQuery()->addFacet(
                $field, $facetItem['value']
            );
            $facets[$facetItem['displayText']] = $facetItem;
        }

        return array_values($facets);
    }
}
