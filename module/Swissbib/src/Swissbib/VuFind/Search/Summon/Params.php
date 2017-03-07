<?php
/**
 * Summon Search Params
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
namespace Swissbib\VuFind\Search\Summon;

use VuFind\Search\Summon\Params as VFSummonParams;
use SerialsSolutions_Summon_Query as SummonQuery,
    VuFind\Solr\Utils as SolrUtils,
    VuFindSearch\ParamBag;

/**
 * Summon Search Params
 *
 * @category Swissbib_VuFind2
 * @package  VuFind_Search_SolrClassification
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.swissbib.org  Main Page
 */
class Params extends VFSummonParams
{
    use \Swissbib\VuFind\Search\Helper\PersonalSettingsHelper;

    /**
     * DateRange
     *
     * @var array
     */
    protected $dateRange = [
        'isActive' => false
    ];

    /**
     * Pull the page size parameter or set to default
     *
     * @param \Zend\StdLib\Parameters $request Parameter object representing user
     * request.
     *
     * @return void 
     */
    protected function initLimit($request)
    {

        $auth = $this->serviceLocator->get('VuFind\AuthManager');
        $defLimit = $this->getOptions()->getDefaultLimit();
        $limitOptions = $this->getOptions()->getLimitOptions();
        $view = $this->getView();
        $this->handleLimit($auth, $request, $defLimit, $limitOptions, $view);
    }

    /**
     * Get the value for which type of sorting to use
     *
     * @param \Zend\StdLib\Parameters $request Parameter object representing user
     * request.
     *
     * @return string
     */
    protected function initSort($request)
    {
        $auth = $this->serviceLocator->get('VuFind\AuthManager');
        $defaultSort = $this->getOptions()->getDefaultSortByHandler();

        $this->setSort(
            $this->handleSort(
                $auth, $request, $defaultSort, $this->getSearchClassId()
            )
        );
    }

    /**
     * GH: we need this method to call initLimit (which is protected in base class
     * and shouldn't be changed only because
     * of hacks relaed to silly personal settings (although is possible in the
     * current PHP version)
     *
     * @param \Zend\StdLib\Parameters $request Request
     *
     * @return void
     */
    public function initLimitAdvancedSearch($request)
    {
        $this->initLimit($request);
    }

    /**
     * GetSearchClassId
     *
     * @return string
     */
    public function getSearchClassId()
    {
        //$class = explode('\\', get_class($this));
        //return $class[2];
        //we can't use the basic VuFind mechanism return class[2] because our
        // namespace is build as
        //Swissbib/Vufind/Search/[specialized Search target]
        //therefor it has o be $class[3]
        //My guess: the whole Design related to search types will be refactored by
        // VuFind in the upcoming time (More intensive use of EventManager)
        //so return the name of the target makes it more explicit for a type only
        // responsible for Summon results

        return 'Summon';
    }

    /**
     * Set up filters based on VuFind settings.
     *
     * @param ParamBag $params Parameter collection to update
     *
     * @return void
     */
    public function createBackendFilterParameters(ParamBag $params)
    {
        // flag our non-Standard checkbox filters:
        $foundIncludeNewspapers = false;        // includeNewspapers
        $foundIncludeWithoutFulltext = false;   // includeWithoutFulltext
        $filterList = $this->getFilterList();
        // Which filters should be applied to our query?
        if (!empty($filterList)) {
            // Loop through all filters and add appropriate values to request:
            foreach ($filterList as $filterArray) {
                foreach ($filterArray as $filt) {
                    $safeValue = SummonQuery::escapeParam($filt['value']);
                    if ($filt['field'] == 'holdingsOnly') {
                        // Special case "holdings only" is a separate parameter from
                        // other facets.
                        $params->set(
                            'holdings',
                            strtolower(trim($safeValue)) == 'true'
                        );
                    } else if ($filt['field'] == 'excludeNewspapers') {
                        // support a checkbox for excluding newspapers:
                        // this is now the default behaviour.
                    } else if ($filt['field'] == 'includeNewspapers') {
                        // explicitly include newspaper articles
                        $foundIncludeNewspapers = true;
                    } else if ($range = SolrUtils::parseRange($filt['value'])) {
                        // Special case -- range query (translate [x TO y] syntax):
                        $from = SummonQuery::escapeParam($range['from']);
                        $to = SummonQuery::escapeParam($range['to']);
                        $params->add(
                            'rangeFilters',
                            "PublicationDate,{$from}:{$to}"
                        );
                    } else if ($filt['field'] == 'includeWithoutFulltext') {
                        $foundIncludeWithoutFulltext = true;
                    } else {
                        // Standard case:
                        $params->add('filters', "{$filt['field']},{$safeValue}");
                    }
                }
            }
        }
        // special cases (apply also when filter list is empty)
        // newspaper articles
        if (! $foundIncludeNewspapers) {
            // this actually means: do *not* show newspaper articles
            $params->add('filters', "ContentType,Newspaper Article,true");
        }
        // combined facet "with holdings/with fulltext"
        if (!$foundIncludeWithoutFulltext) {
            $params->set('holdings', true);
            $params->add('filters',  'IsFullText,true');

        } else {
            $params->set('holdings', false);
        }
    }

    /**
     * GetTypeLabel
     *
     * @return string
     */
    public function getTypeLabel()
    {
        return $this->getServiceLocator()->get('Swissbib\TypeLabelMappingHelper')
            ->getLabel($this);
    }

    /**
     * BuildDateRangeFilter
     *
     * @param string $field field to use for filtering.
     * @param string $from  year for start of range.
     * @param string $to    year for end of range.
     *
     * @return string       filter query.
     *
     * @override
     */
    protected function buildDateRangeFilter($field, $from, $to)
    {
        $this->dateRange['from']        = (int) $from;
        $this->dateRange['to']          = (int) $to;
        $this->dateRange['isActive']    = true;

        return parent::buildDateRangeFilter($field, $from, $to);
    }

    /**
     * InitHomePageFacets
     *
     * @return void
     */
    public function initHomePageFacets()
    {
        // Load Advanced settings if HomePage settings are missing (legacy support):
        if (!$this->initFacetList('HomePage', 'HomePage_Settings', 'Summon')) {
            $this->initAdvancedFacets();
        }
    }
}
