<?php
/**
 * ZF2 module definition for the VuFind application
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
 * @package  Module
 * @author   Guenter Hipler  <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.swissbib.org
 */
namespace Swissbib;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface as Autoloadable;
use Zend\ModuleManager\Feature\ConfigProviderInterface as Configurable;
use Zend\ModuleManager\Feature\ConsoleUsageProviderInterface as Consolable;
use Zend\ModuleManager\Feature\InitProviderInterface as Initializable;
use Zend\ModuleManager\ModuleManagerInterface;
use Zend\Mvc\MvcEvent;
use Zend\Console\Adapter\AdapterInterface as Console;

/**
 * ZF2 module definition for the VuFind application
 *
 * @category Swissbib_VuFind2
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
        //we want to use the classmap mechanism if we are not in development mode
        if (strcmp(APPLICATION_ENV, 'development') != 0) {
            preg_match('/(.*?)module/', __DIR__, $matches);
            /*
            return [
                'Zend\Loader\ClassMapAutoloader' => [
                    __NAMESPACE__ => __DIR__ . '/src/autoload_classmap.php',
                    'VuFind' => $matches[0] . '/VuFind/src/autoload_classmap.php',
                    'VuFindSearch' => $matches[0] .
                        '/VuFindSearch/src/autoload_classmap.php',
                    'VuFindTheme' => $matches[0] .
                        '/VuFindTheme/src/autoload_classmap.php'
                    //'Zend' => $matches[1] .
                    //    'vendor/zendframework/zendframework/library/Zend/' .
                    //    'autoload_classmap.php'
                ],
                'Zend\Loader\StandardAutoloader' => [
                    'namespaces' => [
                        __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                    ],
                ],
            ];
        } else {
            */
            return [
                'Zend\Loader\StandardAutoloader' => [
                    'namespaces' => [
                        __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                    ],
                ],
            ];
        }
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
        return [
            '# Libadmin VuFind Synchronisation',
            '# Import library and group data from libadmin API and save as' .
                ' local files',
            'libadmin sync [--verbose|-v] [--dry|-d] [--result|-r]',
            [
                '--verbose|-v', 'Print informations about actions on console output'
            ],
            [
                '--dry|-d', 'Don\'t replace local files with new data ' .
                    '(check if new data is available/reachable)'
            ],
            [
                '--result|-r', 'Print out a single result info at the end.' .
                    ' This is included in the verbose flag'
            ],
            '# Tab40 Location Import',
            '# Extract label information from a tab40 file and convert to vufind' .
                ' language format',
            'tab40import <network> <locale> <source>',
            [
                'network',
                'Network key the file contains informatino about. Ex: idsbb'
            ],
            ['locale', 'Locale key: de, en, fr, etc'],
            ['source', 'Path to input file. Ex: ~/myalephdata/tab40.ger']
        ];
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
