<?php
/**
 * Factory for view helpers related to the Swissbib theme.
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
 * @package  View_Helper_Swissbib
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
namespace Swissbib\View\Helper\Swissbib;

use Swissbib\View\Helper\AutoSuggestConfig;
use Swissbib\View\Helper\FacetListSorter;
use Swissbib\View\Helper\FormatRelatedEntries;
use Swissbib\View\Helper\IncludeTemplate;

use Swissbib\View\Helper\NationalLicences;
use Swissbib\View\Helper\TranslateFacets;
use Swissbib\VuFind\Search\Helper\SearchTabsHelper;
use Swissbib\VuFind\View\Helper\Root\Auth;
use Swissbib\VuFind\View\Helper\Root\SearchTabs;
use Zend\ServiceManager\ServiceManager;

/**
 * Factory for swissbib specific view helpers related to the Swissbib Theme.
 * these theme related static factory functions were refactored from Closures
 * which were part of the configuration. Because configuration can now be cached we
 * have to write factory methods
 *
 * @category Swissbib_VuFind
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
            $sm->get('VuFind\Config\PluginManager')->get('config')
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
            $sm->get('VuFind\Search\Memory')
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
            $sm->get('VuFind\Config\PluginManager')->get('config')
                ->Authentication->noAjaxLogin
        ) ? $sm->get('VuFind\Config\PluginManager')->get('config')
            ->Authentication->noAjaxLogin->toArray() : [];

        return new Auth(
            $sm->get('VuFind\Auth\Manager'),
            $sm->get('VuFind\ILSAuthenticator'),
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
        $config =  $sm->get('VuFind\Config\PluginManager')->get('facets')
            ->Advanced_Settings->translated_facets->toArray();
        return new TranslateFacets($config);
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
        $helpers = $sm->get('ViewHelperManager');
        return new SearchTabs(
            $sm->get('VuFind\Search\Results\PluginManager'),
            $helpers->get('url'),
            $sm->get('VuFind\Search\SearchTabsHelper')
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
        $config = $sm->get('VuFind\Config\PluginManager')->get('config');
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
            $sm->get('VuFind\Search\Results\PluginManager'),
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
            $sm->get('Zend\Mvc\I18n\Translator')
        );
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

    /**
     * Construct AutoSuggestConfig Helper
     *
     * @param ServiceManager $sm Service manager.
     *
     * @return AutoSuggestConfig
     */
    public static function getAutoSuggestConfig(ServiceManager $sm)
    {
        return new AutoSuggestConfig($sm);
    }

    /**
     * Construct facet2ListSorter
     *
     * @param ServiceManager $sm Service manager.
     *
     * @return array
     */
    public static function getFacetListSorter(ServiceManager $sm)
    {
        return new FacetListSorter($sm);
    }

}
