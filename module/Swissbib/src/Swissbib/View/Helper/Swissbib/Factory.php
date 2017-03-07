<?php
/**
 * Factory for view helpers related to the Swissbib theme.
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
 * @package  View_Helper_Swissbib
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
namespace Swissbib\View\Helper\Swissbib;

use Swissbib\View\Helper\FormatRelatedEntries;
use Swissbib\View\Helper\NationalLicences;
use Swissbib\VuFind\View\Helper\Root\Piwik;
use Zend\ServiceManager\ServiceManager;

use Swissbib\VuFind\View\Helper\Root\Auth;
use Swissbib\VuFind\View\Helper\Root\SearchTabs;
use Swissbib\VuFind\Search\Helper\SearchTabsHelper;
use Swissbib\View\Helper\LayoutClass;
use Swissbib\View\Helper\IncludeTemplate;
use Swissbib\View\Helper\TranslateFacets;

/**
 * Factory for swissbib specific view helpers related to the Swissbib Theme.
 * these theme related static factory functions were refactored from Closures
 * which were part of the configuration. Because configuration can now be cached we
 * have to write factory methods
 *
 * @category Swissbib_VuFind2
 * @package  View_Helper_Swissbib
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @author   Markus Mächler <markus.maechler@bithost.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
class Factory
{
    /**
     * GetRecordHelper
     *
     * @param ServiceManager $sm ServiceManager
     *
     * @return \Swissbib\View\Helper\Record
     */
    public static function getRecordHelper(ServiceManager $sm)
    {
        return new \Swissbib\View\Helper\Record(
            $sm->getServiceLocator()->get('VuFind\Config')->get('config')
        );
    }

    /**
     * GetCitation
     *
     * @param ServiceManager $sm ServiceManager
     *
     * @return \Swissbib\VuFind\View\Helper\Root\Citation
     */
    public static function getCitation(ServiceManager $sm)
    {
        return new \Swissbib\VuFind\View\Helper\Root\Citation(
            $sm->getServiceLocator()->get('VuFind\DateConverter')
        );
    }

    /**
     * GetRecordLink
     *
     * @param ServiceManager $sm ServiceManager
     *
     * @return \Swissbib\View\Helper\RecordLink
     */
    public static function getRecordLink(ServiceManager $sm)
    {
        return new \Swissbib\View\Helper\RecordLink(
            $sm->getServiceLocator()->get('VuFind\RecordRouter')
        );
    }

    /**
     * GetExtendedLastSearchLink
     *
     * @param ServiceManager $sm ServiceManager
     *
     * @return \Swissbib\View\Helper\GetExtendedLastSearchLink
     */
    public static function getExtendedLastSearchLink(ServiceManager $sm)
    {
        return new \Swissbib\View\Helper\GetExtendedLastSearchLink(
            $sm->getServiceLocator()->get('VuFind\Search\Memory')
        );
    }

    /**
     * Construct the Auth helper as an extension of the VuFind Core Auth helper
     *
     * @param ServiceManager $sm Service manager.
     *
     * @return Auth
     */
    public static function getAuth(ServiceManager $sm)
    {
        $config = isset(
            $sm->getServiceLocator()->get('VuFind\Config')->get('config')
                ->Authentication->noAjaxLogin
        ) ? $sm->getServiceLocator()->get('VuFind\Config')->get('config')
            ->Authentication->noAjaxLogin->toArray() : [];

        return new Auth(
            $sm->getServiceLocator()->get('VuFind\AuthManager'),
            $config
        );
    }

    /**
     * GetFacetTranslator
     *
     * @param ServiceManager $sm ServiceManager
     *
     * @return TranslateFacets
     */
    public static function getFacetTranslator(ServiceManager $sm)
    {
        $config =  $sm->getServiceLocator()->get('VuFind\Config')->get('facets')
            ->Advanced_Settings->translated_facets->toArray();
        return new TranslateFacets($config);
    }

    /**
     * GetLayoutClass
     *
     * @param ServiceManager $sm ServiceManager
     *
     * @return LayoutClass
     */
    public static function getLayoutClass(ServiceManager $sm)
    {
        $config = $sm->getServiceLocator()->get('VuFind\Config')->get('config');
        $left = !isset($config->Site->sidebarOnLeft)
            ? false : $config->Site->sidebarOnLeft;

        return new LayoutClass($left);
    }

    /**
     * GetSearchTabs
     *
     * @param ServiceManager $sm ServiceManager
     *
     * @return SearchTabs
     */
    public static function getSearchTabs(ServiceManager $sm)
    {
        return new SearchTabs(
            $sm->getServiceLocator()->get('VuFind\SearchResultsPluginManager'),
            $sm->get('url'),
            $sm->getServiceLocator()->get('Swissbib\SearchTabsHelper')
        );
    }

    /**
     * Construct the SearchTabs helper.
     *
     * @param ServiceManager $sm Service manager.
     *
     * @return SearchTabsHelper
     */
    public static function getSearchTabsHelper(ServiceManager $sm)
    {
        $config = $sm->get('VuFind\Config')->get('config');
        $tabConfig = [];
        if (isset($config->SearchTabs)) {
            $tabConfig['SearchTabs'] = $config->SearchTabs->toArray();
        }
        if (isset($config->AdvancedSearchTabs)) {
            $tabConfig['AdvancedSearchTabs']
                = $config->AdvancedSearchTabs->toArray();
        }
        $filterConfig = isset($config->SearchTabsFilters)
            ? $config->SearchTabsFilters->toArray() : [];
        return new SearchTabsHelper(
            $sm->get('VuFind\SearchResultsPluginManager'),
            $tabConfig, $filterConfig,
            $sm->get('Application')->getRequest()
        );
    }

    /**
     * GetIncludeTemplate
     *
     * @param ServiceManager $sm Service manager.
     *
     * @return IncludeTemplate
     */
    public static function getIncludeTemplate(ServiceManager $sm)
    {
        return new IncludeTemplate();
    }

    /**
     * GetFormatRelatedEntries
     *
     * @param ServiceManager $sm Service manager.
     *
     * @return FormatRelatedEntries
     */
    public static function getFormatRelatedEntries(ServiceManager $sm)
    {
        return new FormatRelatedEntries(
            $sm->getServiceLocator()->get('VuFind\Translator')
        );
    }

    /**
     * Construct the Piwik helper.
     *
     * @param ServiceManager $sm Service manager.
     *
     * @return Piwik
     */
    public static function getPiwik(ServiceManager $sm)
    {
        $config = $sm->getServiceLocator()->get('VuFind\Config')->get('config');
        $url = isset($config->Piwik->url) ? $config->Piwik->url : false;
        $siteId = isset($config->Piwik->site_id) ? $config->Piwik->site_id : 1;
        $customVars = isset($config->Piwik->custom_variables)
            ? $config->Piwik->custom_variables
            : false;
        $request = $sm->getServiceLocator()->get('Request');
        $router = $sm->getServiceLocator()->get('Router');
        return new Piwik($url, $siteId, $customVars, $router, $request);
    }

    /**
     * Construct NationalLicence Helper
     *
     * @param ServiceManager $sm Service manager.
     *
     * @return NationalLicences
     */
    public static function getNationalLicences(ServiceManager $sm)
    {
        return new NationalLicences($sm);
    }
}
