<?php
/**
 * Theme
 *
 * PHP version 5
 *
 * Copyright (C) project swissbib, University Library Basel, Switzerland
 * http://www.swissbib.org  / http://www.swissbib.ch / http://www.ub.unibas.ch
 *
 * Date: 2/13/14
 * Time: 4:49 PM
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
 * @author   Guenter Hipler  <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.swissbib.org
 */
namespace Swissbib\Services;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class Theme
 *
 * @category Swissbib_VuFind2
 * @package  Services
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
class Theme implements ServiceLocatorAwareInterface
{
    /**
     * ServiceLocatorInterface
     *
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * Set serviceManager instance
     *
     * @param ServiceLocatorInterface $serviceLocator ServiceLocatorInterface
     *
     * @return void
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    /**
     * Retrieve serviceManager instance
     *
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    /**
     * Get active theme
     *
     * @return String
     */
    protected function getTheme()
    {
        return $this->getServiceLocator()->get('Vufind\Config')->get('config')
            ->Site->theme;
    }

    /**
     * Get all configuration for theme tabs
     *
     * @return Array[]
     */
    public function getThemeTabsConfig()
    {
        $theme = $this->getTheme();
        $tabs = [];
        $moduleConfig = $this->getServiceLocator()->get('Config');
        $tabsConfig = $moduleConfig['swissbib']['resultTabs'];
        $allTabs = $tabsConfig['tabs'];
        $themeTabs = isset($tabsConfig['themes'][$theme]) ?
            $tabsConfig['themes'][$theme] : [];

        foreach ($themeTabs as $themeTab) {
            if (isset($allTabs[$themeTab])) {
                $tabs[$themeTab] = $allTabs[$themeTab];
            }
        }

        return $tabs;
    }
}
