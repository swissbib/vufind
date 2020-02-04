<?php
/**
 * Params
 *
 * PHP version 7
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
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category Swissbib_VuFind
 * @package  VuFind_Search_Solr
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.swissbib.org  Main Page
 */
namespace Swissbib\VuFind\Search\Solr;

use Swissbib\Favorites\Manager as SwissbibFavoritesManager;
use Swissbib\VuFind\Search\Helper\TypeLabelMappingHelper;
use VuFind\Auth\Manager as VuFindAuthManager;
use VuFind\Search\Solr\HierarchicalFacetHelper;
use VuFind\Search\Solr\Params as VuFindSolrParams;
use VuFindSearch\ParamBag;

/**
 * Class to extend the core VF2 SOLR functionality related to Parameters
 *
 * @category Swissbib_VuFind
 * @package  VuFind_Search_Solr
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.vufind.org  Main Page
 */
class Params extends VuFindSolrParams
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
     * VuFindAuthManager
     *
     * @var VuFindAuthManager
     */
    protected $authManager;

    /**
     * TypeLabelMappingHelper
     *
     * @var TypeLabelMappingHelper
     */
    protected $typeLabelMappingHelper;

    /**
     * SwissbibFavoritesManager
     *
     * @var SwissbibFavoritesManager
     */
    protected $favoritesManager;

    /**
     * Constructor
     *
     * @param \VuFind\Search\Base\Options  $options          Options to use
     * @param \VuFind\Config\PluginManager $configLoader     Config loader
     * @param VuFindAuthManager            $authManager      AuthManager
     * @param TypeLabelMappingHelper       $mappingHelper    HelperClass mappings
     * @param SwissbibFavoritesManager     $favoritesManager favoritesManager
     * @param HierarchicalFacetHelper      $facetHelper      facetHelper
     */
    public function __construct($options, \VuFind\Config\PluginManager $configLoader,
        VuFindAuthManager $authManager,
        TypeLabelMappingHelper $mappingHelper,
        SwissbibFavoritesManager $favoritesManager,
        HierarchicalFacetHelper $facetHelper = null
    ) {
        parent::__construct(
            $options, $configLoader,
            $facetHelper
        );
        $this->authManager = $authManager;
        $this->typeLabelMappingHelper = $mappingHelper;
        $this->favoritesManager = $favoritesManager;
    }

    /**
     * Override to prevent problems with namespace
     * See implementation of parent for details
     *
     * @return String
     */
    public function getSearchClassId()
    {
        return 'Solr';
    }

    /**
     * Pull the page size parameter or set to default
     *
     * @param \Zend\StdLib\Parameters $request Parameter object representing user
     *                                         request.
     *
     * @return void
     */
    protected function initLimit($request)
    {
        //$auth = $this->serviceLocator->get('VuFind\AuthManager');
        $defLimit = $this->getOptions()->getDefaultLimit();
        $limitOptions = $this->getOptions()->getLimitOptions();
        $view = $this->getView();

        $this->handleLimit(
            $this->authManager, $request, $defLimit, $limitOptions, $view
        );
    }

    /**
     * GH: we need this method to call initLimit (which is protected in base
     * class and shouldn't be changed only because
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
     * Get the value for which type of sorting to use
     *
     * @param \Zend\StdLib\Parameters $request Parameter object representing user
     *                                         request.
     *
     * @return string
     */
    protected function initSort($request)
    {
        //$auth = $this->serviceLocator->get('VuFind\AuthManager');
        $defaultSort = $this->getOptions()->getDefaultSortByHandler();
        $this->setSort(
            $this->handleSort(
                $this->authManager, $request, $defaultSort, $this->getSearchClassId()
            )
        );
    }

    /**
     * Overridden function - we need some more parameters.
     *
     * @return ParamBag
     */
    public function getBackendParameters()
    {
        $backendParams = parent::getBackendParameters();

        //with SOLR 4.3 AND is no longer the default parameter
        $backendParams->add("q.op", "AND");

        $backendParams = $this->addUserInstitutions($backendParams);

        return $backendParams;
    }

    /**
     * GetSpellcheckBackendParameters
     *
     * @return ParamBag
     */
    public function getSpellcheckBackendParameters()
    {
        $backendParams = parent::getBackendParameters();
        $backendParams->remove("spellcheck");

        //with SOLR 4.3 AND is no longer the default parameter
        $backendParams->add("q.op", "AND");

        //we need this homegrown param to control the behaviour of
        // InjectSwissbibSpellingListener
        //I don't see another possibilty yet
        $backendParams->add("swissbibspellcheck", "true");

        return $backendParams;
    }

    /**
     * GetTypeLabel
     *
     * @return string
     */
    public function getTypeLabel()
    {
        return $this->typeLabelMappingHelper
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
     */
    protected function buildDateRangeFilter($field, $from, $to)
    {
        $this->dateRange['from']        = (int)$from;
        $this->dateRange['to']          = (int)$to;
        $this->dateRange['isActive']    = true;

        return parent::buildDateRangeFilter($field, $from, $to);
    }

    /**
     * Add user institutions as facet queries to backend params
     *
     * @param ParamBag $backendParams ParamBag
     *
     * @return ParamBag
     */
    protected function addUserInstitutions(ParamBag $backendParams)
    {

        /**
         * FavoriteInstitutions array
         *
         * @var String[] $favoriteInstitutions
         */
        $favoriteInstitutions = $this->favoritesManager->getUserInstitutions();

        if (sizeof($favoriteInstitutions) > 0) {
            //facet parameter has to be true in case it's false
            $backendParams->set("facet", "true");

            foreach ($favoriteInstitutions as $institutionCode) {
                //GH 19.12.2014: use configuration for index name
                //more investigation for a better solution necessary
                $backendParams->add("facet.query", "mylibrary:" . $institutionCode);
                //$backendParams->add("bq","institution:".$institutionCode "^5000");
            }
        }

        return $backendParams;
    }

    /**
     * GetFacetLabel
     *
     * @param string $field   Facet field name.
     * @param string $value   Facet value.
     * @param string $default Default field name (null for default behavior).
     *
     * @return string Human-readable description of field.
     */
    public function getFacetLabel($field, $value = null, $default = null)
    {
        switch ($field) {
        case 'publishDate':
            return 'adv_search_year';
        default:
            return parent::getFacetLabel($field, $value, $default);
        }
    }
}
