<?php
/**
 * ZF2 module definition for the VuFind application
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
 * @package  Module
 * @author   Guenter Hipler  <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.swissbib.org
 */
namespace Swissbib;

use Laminas\Console\Adapter\AdapterInterface as Console;
use Laminas\ModuleManager\Feature\AutoloaderProviderInterface as Autoloadable;
use Laminas\ModuleManager\Feature\ConfigProviderInterface as Configurable;
use Laminas\ModuleManager\Feature\ConsoleUsageProviderInterface as Consolable;
use Laminas\ModuleManager\Feature\InitProviderInterface as Initializable;
use Laminas\ModuleManager\ModuleManagerInterface;
use Laminas\Mvc\MvcEvent;

/**
 * ZF2 module definition for the VuFind application
 *
 * @category Swissbib_VuFind
 * @package  Module
 * @author   Guenter Hipler  <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org
 */
class Module implements Autoloadable, Configurable, Initializable, Consolable
{
    /**
     * Returns module Config
     *
     * @return Array|mixed|\Traversable
     */
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    /**
     * OnBootstrap Event
     *
     * @param MvcEvent $event BootstrapEvent
     *
     * @return void
     */
    public function onBootstrap(MvcEvent $event)
    {
        $b = new Bootstrapper($event);
        $b->bootstrap();
    }

    /**
     * Returns autoload config
     *
     * @return Array
     */
    public function getAutoloaderConfig()
    {
        return [
            'Laminas\Loader\StandardAutoloader' => [
                'namespaces' => [
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ],
            ],
        ];
    }

    /**
     * Return service configuration.
     *
     * @return array
     */
    public function getServiceConfig()
    {
        return [
            'factories' => [
                'VuFindTheme\ResourceContainer' =>
                    'Swissbib\VuFind\Factory::getResourceContainer',
            ],
        ];
    }

    /**
     * Explains console usage
     *
     * @param Console $console Console
     *
     * @return array
     */
    public function getConsoleUsage(Console $console)
    {
        return [];
    }

    /**
     * Initializes Module
     *
     * @param ModuleManagerInterface $m ModuleManager
     *
     * @return void
     */
    public function init(ModuleManagerInterface $m)
    {
        //note: only for testing
        //$m->getEventManager()
        //    ->attach(
        //        ModuleEvent::EVENT_LOAD_MODULES_POST,
        //        array($this,'postInSwissbib'),
        //        10000
        //);
    }
}
