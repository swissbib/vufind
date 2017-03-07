<?php
/**
 * Bootstraper
 *
 * PHP version 5
 *
 * Copyright (C) project swissbib, University Library Basel, Switzerland
 * http://www.swissbib.org  / http://www.swissbib.ch / http://www.ub.unibas.ch
 *
 * Date: 9/12/13
 * Time: 11:46 AM
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
 * @package  Swissbib
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.swissbib.org
 */
namespace Swissbib;

use Zend\Config\Config;
use Zend\Console\Console;
use Zend\EventManager\Event;
use Zend\Mvc\MvcEvent;
use Zend\Console\Request as ConsoleRequest;
use Zend\I18n\Translator\Translator as TranslatorImpl;
use Zend\ServiceManager\ServiceManager;

use VuFind\Auth\Manager;

use Swissbib\Filter\TemplateFilenameFilter;

/**
 * Bootstraper
 *
 * @category Swissbib_VuFind2
 * @package  Swissbib
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.swissbib.org  Main Page
 */
class Bootstrapper
{
    /**
     * Config
     *
     * @var Config
     */
    protected $config;

    /**
     * Bootstrap Event
     *
     * @var MvcEvent
     */
    protected $event;

    /**
     * Events
     *
     * @var \Zend\EventManager\EventManagerInterface
     */
    protected $events;

    /**
     * Application
     *
     * @var \Zend\Mvc\ApplicationInterface
     */
    protected $application;

    /**
     * ServiceManager
     *
     * @var \Zend\ServiceManager\ServiceLocatorInterface
     */
    protected $serviceManager;

    /**
     * Constructor
     *
     * @param MvcEvent $event Bootstrap Event
     */
    public function __construct(MvcEvent $event)
    {
        $this->application = $event->getApplication();
        $this->serviceManager = $this->application->getServiceManager();
        $this->config = $this->serviceManager->get('VuFind\Config')->get('config');
        $this->event = $event;
        $this->events = $this->application->getEventManager();
    }

    /**
     * Bootstrap
     * Automatically discovers and evokes all class methods
     * with names starting with 'init'
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
     * Add template path filter to filter chain
     *
     * @return void
     */
    protected function initFilterChain()
    {
        if (APPLICATION_ENV == 'development'
            && !$this->event->getRequest() instanceof ConsoleRequest
        ) {
            $sm = $this->event->getApplication()->getServiceManager();

            $widgetFilter = new TemplateFilenameFilter();
            $widgetFilter->setServiceLocator($sm);

            $view = $sm->get('ViewRenderer');

            $view->getFilterChain()->attach($widgetFilter, 50);
        }
    }

    /**
     * Initialize locale change
     * Save changed locale in user
     *
     * @return void
     */
    protected function initLocaleChange()
    {
        /**
         * ServiceLocator
         *
         * @var ServiceManager $serviceLocator
         */
        $serviceLocator = $this->serviceManager;
        /**
         * AuthManager
         *
         * @var Manager $authManager
         */
        $authManager = $serviceLocator->get('VuFind\AuthManager');

        if ($authManager->isLoggedIn()) {
            $user = $authManager->isLoggedIn();

            $callback = function ($event) use ($user) {
                $request = $event->getRequest();

                if (($locale = $request->getPost()->get('mylang', false))
                    || ($locale = $request->getQuery()->get('lng', false))
                ) {
                    $user->language = $locale;
                    $user->save();
                }
            };

            $this->events->attach('dispatch', $callback, 1000);
        }
    }

    /**
     * Initialize translation from user settings
     * Executes later than vufind language init (vufind has priority 9000)
     *
     * @return void
     */
    protected function initUserLocale()
    {
        /**
         * ServiceManager
         *
         * @var ServiceManager $serviceLocator
         */
        $serviceLocator = $this->serviceManager;

        /**
         * AuthManager
         *
         * @var Manager $authManager
         */
        $authManager    = $serviceLocator->get('VuFind\AuthManager');

        /**
         * Config
         *
         * @var Config $config
         */
        $config = $this->config;

        if ($authManager->isLoggedIn()) {
            $locale = $authManager->isLoggedIn()->language;

            if ($locale) {
                /**
                 * Translator
                 *
                 * @var TranslatorImpl $translator
                 */
                $translator = $this->serviceManager->get('VuFind\Translator');
                $viewModel = $serviceLocator->get('viewmanager')->getViewModel();

                $callback = function ($event) use ($locale, $translator,
                    $viewModel, $config
                ) {
                    $request = $event->getRequest();
                    $post = $request->getPost();
                    $query = $request->getQuery();

                    if (($languageChange = $post->get('mylang', false))
                        || ($languageChange = $query->get('lng', false))
                    ) {
                        if (in_array(
                            $languageChange,
                            array_keys($config->Languages->toArray())
                        )) {
                            $locale = $languageChange;
                        }
                    }

                    $translator->setLocale($locale);
                    $viewModel->setVariable('userLang', $locale);
                };

                $this->events->attach('dispatch', $callback, 8000);
            }
        }
    }

    /**
     * Set headers no-cache in case it is configured
     * we need this functionality especially after the deployment of new versions
     * with significant CSS changes
     * then we want to suppress the browser caching for a limited period of time
     *
     * @return void
     */
    protected function initNoCache()
    {
        // call to get headers not supported in cli mode:
        if (Console::isConsole()) {
            return;
        }
        $config = & $this->config;

        if (isset($config->Site->header_no_cache) &&  $config->Site->header_no_cache
        ) {
            $callback = function ($event) {
                $response = $event->getApplication()->getResponse();
                //for expires use date in the past
                $response->getHeaders()->addHeaders(
                    [
                    'Cache-Control' => 'no-cache, no-store, must-revalidate',
                    'Pragma' => 'no-cache',
                    'Expires' => 'Thu, 1 Jan 2015 00:00:00 GMT'
                    ]
                );

            };

            $this->events->attach('dispatch', $callback, -500);
        }
    }

    /**
     * Add log listener for missing institution translations
     *
     * @return void
     */
    protected function initMissingTranslationObserver()
    {
        if (APPLICATION_ENV != 'development') {
            return;
        }

        /**
         * ServiceManager
         *
         * @var ServiceManager $serviceLocator
         */
        $serviceLocator    = $this->event->getApplication()->getServiceManager();

        /**
         * Logger
         *
         * @var \Swissbib\Log\Logger $logger
         */
        $logger    = $serviceLocator->get('Swissbib\Logger');

        /**
         * Translator
         *
         * @var TranslatorImpl $translator
         */
        $translator = $serviceLocator->get('VuFind\Translator');

        /**
         * Logging untranslated institutions
         *
         * @param Event $event
         *
         * @return void
         */
        $callback = function ($event) use ($logger) {
            if ($event->getParam('text_domain') === 'institution') {
                $logger->logUntranslatedInstitution($event->getParam('message'));
            }
        };

        $translator->enableEventManager();
        $translator->getEventManager()->attach('missingTranslation', $callback);
    }

    /**
     * Add translation for Form Validation
     *
     * @return void
     */
    protected function initZendValidatorTranslations()
    {
        $callback = function ($event) {
            /**
             * Translator
             *
             * @var TranslatorImpl $translator
             */
            $translator = $event->getApplication()->getServiceManager()
                ->get('VuFind\Translator');

            $translator->addTranslationFile(
                'phparray',
                'vendor/zendframework/zendframework/resources/languages/' .
                $translator->getLocale() . '/Zend_Validate.php',
                'default',
                $translator->getLocale()
            );
        };

        $this->events->attach('dispatch', $callback, 8996);
    }

    /**
     * Enables class loading for local composer dependencies
     *
     * @return void
     */
    protected function initLocalComposerDependencies()
    {
        $autoloadFilePath = APPLICATION_PATH . '/local/vendor/autoload.php';

        if (file_exists($autoloadFilePath)) {
            include $autoloadFilePath;
        }
    }
}
