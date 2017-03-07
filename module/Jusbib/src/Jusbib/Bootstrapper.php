<?php
/**
 * Jusbib Bootstrapper
 *
 * PHP version 5
 *
 * Copyright (C) project swissbib, University Library Basel, Switzerland
 * http://www.swissbib.org  / http://www.swissbib.ch / http://www.ub.unibas.ch
 *
 * This program is
 * free software; you can redistribute it and/or modify
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
 * @category Jusbib_VuFind2
 * @package  Jusbib
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
namespace Jusbib;

use Zend\Mvc\MvcEvent;

/**
 * Jusbib Bootstrapper
 *
 * @category Jusbib_VuFind2
 * @package  Jusbib
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
class Bootstrapper
{
    /**
     * Bootstrap Event
     *
     * @var MvcEvent
     */
    protected $event;

    /**
     * Constructor
     *
     * @param MvcEvent $event Bootstrap Event
     */
    public function __construct(MvcEvent $event)
    {
        $this->event  = $event;
    }

    /**
     * Automatically discovers and evokes all class
     * methods with names starting with 'init'
     *
     * @return void
     */
    public function bootstrap()
    {
        $methods = get_class_methods($this);

        foreach ($methods as $method) {
            if (substr($method, 0, 4) == 'init') {
                $this->$method();
            }
        }
    }

    /**
     * Set up plugin managers.
     *
     * @return void
     */
    protected function initPluginManagers()
    {
        $app            = $this->event->getApplication();
        $serviceManager = $app->getServiceManager();
        $config         = $app->getConfig();

        // Use naming conventions to set up a bunch of services based on namespace:
        $namespaces = [
            'VuFind\Search\Results','VuFind\Search\Options', 'VuFind\Search\Params'
        ];

        foreach ($namespaces as $namespace) {
            $plainNamespace    = str_replace('\\', '', $namespace);
            $shortNamespace    = str_replace('VuFind', '', $plainNamespace);
            $configKey        = strtolower(str_replace('\\', '_', $namespace));
            $serviceName    = 'Jusbib\\' . $shortNamespace . 'PluginManager';
            $serviceConfig    = $config['jusbib']['plugin_managers'][$configKey];
            $className        = 'Jusbib\\' . $namespace . '\PluginManager';

            $pluginManagerFactoryService = function ($sm) use
                ($className, $serviceConfig) {

                return new $className(
                    new \Zend\ServiceManager\Config($serviceConfig)
                );
            };

            $serviceManager->setFactory($serviceName, $pluginManagerFactoryService);
        }
    }
}
