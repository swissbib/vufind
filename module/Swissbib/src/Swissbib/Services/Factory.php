<?php
/**
 * Factory for services.
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
 * @package  Services
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
namespace Swissbib\Services;

use Zend\ServiceManager\ServiceManager;
use Swissbib\VuFind\Recommend\FavoriteFacets;
/**
 * Factory for Services.
 *
 * @category Swissbib_VuFind2
 * @package  Services
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
class Factory
{
    /**
     * Constructs a type for redirecting resources using the appropriate protocol
     * (most often used for http resources in https environments).
     *
     * @param ServiceManager $sm Service manager.
     *
     * @return RedirectProtocolWrapper
     */
    public static function getProtocolWrapper(ServiceManager $sm)
    {
        $config = $sm->get('VuFind\Config')->get('config');

        return new RedirectProtocolWrapper($config);
    }

    /**
     * Constructs Theme - a type used to load Theme specific configuration
     *
     * @param ServiceManager $sm ServiceManager
     *
     * @return Theme
     */
    public static function getThemeConfigs(ServiceManager $sm)
    {
        //Factory Method doesn't make sense but was introduced by Snowflake
        //perhaps we can use it later to enhance the Theme type
        //once the Responsive Design project has finished
        // (and no enhancement is necessary) we could throw it away
        //and simplify the mechanism with invokables
        return new Theme();
    }

    /**
     * Creates a service to configure the requests against SOLR to receive
     * highlighting snippets in fulltext
     *
     * @param ServiceManager $sm ServiceManager
     *
     * @return \Swissbib\Highlight\SolrConfigurator
     */
    public static function getSOLRHighlightingConfigurator(ServiceManager $sm)
    {
        $config = $sm->get('Vufind\Config')->get('config')->Highlight;
        $eventsManager = $sm->get('SharedEventManager');
        $memory = $sm->get('VuFind\Search\Memory');

        return new \Swissbib\Highlight\SolrConfigurator(
            $eventsManager, $config, $memory
        );
    }

    /**
     * Creates a Swissbib specific logger type
     *
     * @param ServiceManager $sm ServiceManager
     *
     * @return \Swissbib\Log\Logger
     */
    public static function getSwissbibLogger(ServiceManager $sm)
    {
        $logger = new  \Swissbib\Log\Logger();
        $logger->addWriter(
            'stream', 1, [
                'stream' => 'log/swissbib.log'
            ]
        );

        return $logger;
    }

    /**
     * Factory for FavoriteFacets module.
     *
     * @param ServiceManager $sm Service manager.
     *
     * @return FavoriteFacets
     */
    public static function getFavoriteFacets(ServiceManager $sm)
    {
        /*
        the VuFind mechanism isn't flexible enough. They changed the mechanism
        displaying "Merklisten" (favorite lists in VF terminology)
        because they should be present on all the pages after users have logged in.
        This is not compatible with our current UI.
        VF core is using only tags as mainfacets
        $this->mainFacets = ($tagSetting && $tagSetting !== 'disabled')
            ? array('tags' => 'Your Tags') : array();
        we need tags and lists for our current UI ....
        solve this in RD design project
        */

        return new FavoriteFacets(
            $sm->getServiceLocator()->get('VuFind\Config')
        );
    }

    /**
     * Get Export
     *
     * @param ServiceManager $sm ServiceManager
     *
     * @return \Swissbib\Export
     */
    public static function getExport(ServiceManager $sm)
    {
        return new \Swissbib\Export(
            $sm->get('VuFind\Config')->get('config'),
            $sm->get('VuFind\Config')->get('export')
        );
    }

    /**
     * Construct the Search\Options Plugin Manager.
     *
     * @param ServiceManager $sm Service manager.
     *
     * @return \VuFind\Search\Options\PluginManager
     */
    public static function getSearchOptionsPluginManager(ServiceManager $sm)
    {
        return static::getGenericPluginManager($sm, 'VuFind\Search\Options');
    }

    /**
     * Generic plugin manager factory (support method).
     *
     * @param ServiceManager $sm Service manager.
     * @param string         $ns VuFind namespace containing plugin manager
     *
     * @return object
     */
    public static function getGenericPluginManager(ServiceManager $sm, $ns)
    {
        $className = 'Swissbib\\' . $ns . '\PluginManager';
        $configKey = strtolower(str_replace('\\', '_', $ns));
        $config = $sm->get('Config');

        return new $className(
            new \Zend\ServiceManager\Config(
                //we need the swissbib specific configurations
                $config['swissbib']['plugin_managers'][$configKey]
            )
        );
    }

    /**
     * Construct the Search\Params Plugin Manager.
     *
     * @param ServiceManager $sm Service manager.
     *
     * @return \VuFind\Search\Params\PluginManager
     */
    public static function getSearchParamsPluginManager(ServiceManager $sm)
    {
        return static::getGenericPluginManager($sm, 'VuFind\Search\Params');
    }

    /**
     * Construct the Search\Results Plugin Manager.
     *
     * @param ServiceManager $sm Service manager.
     *
     * @return \VuFind\Search\Results\PluginManager
     */
    public static function getSearchResultsPluginManager(ServiceManager $sm)
    {
        return static::getGenericPluginManager($sm, 'VuFind\Search\Results');
    }

    /**
     * Construct the Service\NationalLicence service.
     *
     * @param ServiceManager $sm Service manager
     *
     * @return NationalLicence
     */
    public static function getNationalLicenceService(ServiceManager $sm)
    {
        return new NationalLicence(
            $sm->get('Swissbib\SwitchApiService'),
            $sm->get('Swissbib\EmailService'),
            $sm->get('VuFind\Config')->get('NationalLicences')
        );
    }

    /**
     * Get SwitchApi service.
     *
     * @param ServiceManager $sm Service manager
     *
     * @return SwitchApi
     */
    public static function getSwitchApiService(ServiceManager $sm)
    {
        return new SwitchApi($sm->get('VuFind\Config'));
    }

    /**
     * Get Email service.
     *
     * @param ServiceManager $sm service manager
     *                            
     * @return Email
     */
    public static function getEmailService(ServiceManager $sm)
    {
        return new Email($sm->get('VuFind\Config'));
    }
}
