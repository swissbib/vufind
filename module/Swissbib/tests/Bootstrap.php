<?php
/**
 * Bootstrap
 *
 * PHP version 5
 *
 * Copyright (C) project swissbib, University Library Basel, Switzerland
 * http://www.swissbib.org  / http://www.swissbib.ch / http://www.ub.unibas.ch
 *
 * Date: 1/2/13
 * Time: 4:09 PM
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
 * @package  SwissbibTest
 * @author   Guenter Hipler  <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.swissbib.org
 */
namespace SwissbibTest;

use Zend\Mvc\Service\ServiceManagerConfig;
use Zend\ServiceManager\ServiceManager;
use Zend\Stdlib\ArrayUtils;
use RuntimeException;

define('APPLICATION_PATH', realpath(dirname(__DIR__) . '/../..'));
define('SWISSBIB_TESTS_PATH', __DIR__);
chdir(APPLICATION_PATH);

/**
 * Bootstrap
 *
 * @category Swissbib_VuFind2
 * @package  SwissbibTest
 * @author   Guenter Hipler  <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org
 */
class Bootstrap
{
    /**
     * ServiceManager
     *
     * @var ServiceManager
     */
    protected static $serviceManager;

    /**
     * Config
     *
     * @var Config
     */
    protected static $config;

    /**
     * Bootstrap
     *
     * @var
     */
    protected static $bootstrap;

    /**
     * Init
     *
     * @return void
     */
    public static function init()
    {
        // Load the user-defined test configuration file, if it exists;
        // otherwise, load
        if (is_readable(SWISSBIB_TESTS_PATH . '/TestConfig.php')) {
            $testConfig = include SWISSBIB_TESTS_PATH . '/TestConfig.php';
        } else {
            $testConfig = include SWISSBIB_TESTS_PATH . '/TestConfig.php.dist';
        }

        $zf2ModulePaths = [];

        if (isset($testConfig['module_listener_options']['module_paths'])) {
            $modulePaths = $testConfig['module_listener_options']['module_paths'];
            foreach ($modulePaths as $modulePath) {
                if (($path = static::findParentPath($modulePath))) {
                    $zf2ModulePaths[] = $path;
                }
            }
        }

        $zf2ModulePaths = implode(PATH_SEPARATOR, $zf2ModulePaths) . PATH_SEPARATOR;
        $zf2ModulePaths .= getenv('ZF2_MODULES_TEST_PATHS') ?: (defined('ZF2_MODULES_TEST_PATHS') ? ZF2_MODULES_TEST_PATHS : '');

        static::initAutoloader();

        // use ModuleManager to load this module and it's dependencies
        $baseConfig = [
            'module_listener_options' => [
                'module_paths' => explode(PATH_SEPARATOR, $zf2ModulePaths),
            ],
        ];

        self::initEnvironment();

        $config = ArrayUtils::merge($baseConfig, $testConfig);

        $serviceManager = new ServiceManager(new ServiceManagerConfig());
        $serviceManager->setService('ApplicationConfig', $config);
        $serviceManager->get('ModuleManager')->loadModules();

        static::$serviceManager = $serviceManager;
        static::$config         = $config;
    }

    /**
     * InitVuFind
     *
     * @return void
     */
    public static function initEnvironment()
    {
        define('APPLICATION_ENV', 'development');
        define('SWISSBIB_TEST_FIXTURES', realpath(SWISSBIB_TESTS_PATH . '/fixtures'));
    }

    /**
     * GetServiceManager
     *
     * @return ServiceManager
     */
    public static function getServiceManager()
    {
        return static::$serviceManager;
    }

    /**
     * GetConfig
     *
     * @return Config
     */
    public static function getConfig()
    {
        return static::$config;
    }

    /**
     * InitAutoloader
     *
     * @return void
     */
    protected static function initAutoloader()
    {
        $vendorPath = static::findParentPath('vendor');

        if (is_readable($vendorPath . '/autoload.php')) {
            include $vendorPath . '/autoload.php';

            $loader = new \Composer\Autoload\ClassLoader();
            $loader->add('VuFindTest', APPLICATION_PATH . '/module/VuFind/tests/unit-tests/src');
            $loader->add('VuFindTest', APPLICATION_PATH . '/module/VuFind/src');
            $loader->add('SwissbibTest', SWISSBIB_TESTS_PATH . '/');
            $loader->add('VuFindTest', APPLICATION_PATH . '/module/VuFind/src/VuFindTest');

            $loader->register();
        } else {
            throw new RuntimeException('Unable initialize autoloading.');
        }
    }

    /**
     * FindParentPath
     *
     * @param String $path Path
     *
     * @return bool|string
     */
    protected static function findParentPath($path)
    {
        $dir         = SWISSBIB_TESTS_PATH;
        $previousDir = '.';
        while (!is_dir($dir . '/' . $path)) {
            $dir = dirname($dir);

            if ($previousDir === $dir) {
                return false;
            }

            $previousDir = $dir;
        }

        return $dir . '/' . $path;
    }
}

Bootstrap::init();
