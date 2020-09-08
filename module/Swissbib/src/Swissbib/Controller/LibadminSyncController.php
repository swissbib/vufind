<?php
/**
 * Swissbib LibadminSyncController
 *
 * PHP version 7
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
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category Swissbib_VuFind
 * @package  Controller
 * @author   Guenter Hipler  <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.swissbib.org
 */
namespace Swissbib\Controller;

use Swissbib\Libadmin\Importer;
use Laminas\Console\Request as ConsoleRequest;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\ServiceManager\Exception\ServiceNotCreatedException;
use Laminas\ServiceManager\ServiceManager;

/**
 * Synchronize VuFind with LibAdmin
 * Import data into local files
 *
 * @category Swissbib_VuFind
 * @package  Controller
 * @author   Guenter Hipler  <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org
 */
class LibadminSyncController extends AbstractActionController
{
    /**
     * Service manager
     *
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * Constructor
     *
     * @param ServiceLocatorInterface $sm Service locator
     */
    public function __construct(ServiceManager $sm)
    {
        $this->serviceLocator = $sm;
    }

    /**
     * Synchronize with libadmin system
     *
     * @throws \RuntimeException
     *
     * @return string | boolean
     */
    public function syncAction()
    {
        $request = $this->getRequest();

        if (!$request instanceof ConsoleRequest) {
            throw new \RuntimeException(
                'You can only use this action from a console!'
            );
        }

        $verbose = $request->getParam('verbose', false)
            || $request->getParam('v', false);
        $showResult = $request->getParam('result', false)
            || $request->getParam('r', false);
        $dryRun = $request->getParam('dry', false)
            || $request->getParam('d', false);

        /**
         * Libadmin Importer
         *
         * @var Importer $importer
         */
        try {
            $importer = $this->serviceLocator
                ->get('Swissbib\Libadmin\Importer');
            $result   = $importer->import($dryRun);
            $hasErrors = $result->hasErrors();
        } catch (ServiceNotCreatedException $e) {
            // handle service exception
            echo "- Fatal error\n";
            echo "- Stopped with exception: " . get_class($e) . "\n";
            echo "===============================================================\n";
            echo $e->getMessage() . "\n";
            echo $e->getPrevious()->getMessage() . "\n";

            return false;
        }

        // Show all messages?
        if ($verbose || $hasErrors) {
            foreach ($result->getFormattedMessages() as $message) {
                echo '- ' . $message . "\n";
            }
        }

        // No messages printed, but result required?
        if (!$verbose && $showResult) {
            echo $result->isSuccess() ? 1 : 0;
        }

        return '';
    }

    /**
     * Sync map portal
     *
     * @return bool | string
     */
    public function syncMapPortalAction()
    {
        $request = $this->getRequest();

        if (!$request instanceof ConsoleRequest) {
            throw new \RuntimeException(
                'You can only use this action from a console!'
            );
        }

        $verbose = $request->getParam('verbose', false)
            || $request->getParam('v', false);
        $showResult = $request->getParam('result', false)
            || $request->getParam('r', false);
        //$dryRun     = $request->getParam('dry', false)
        //  || $request->getParam('d', false);
        $path = $request->getParam('path', 'mapportal/green.json');

        /**
         * Libadmin importer
         *
         * @var Importer $importer
         */
        try {
            $importer = $this->serviceLocator
                ->get('Swissbib\Libadmin\Importer');
            $result   = $importer->importMapPortalData($path);
            $hasErrors = $result->hasErrors();
        } catch (ServiceNotCreatedException $e) {
            // handle service exception
            echo "- Fatal error\n";
            echo "- Stopped with exception: " . get_class($e) . "\n";
            echo "===============================================================\n";
            echo $e->getMessage() . "\n";
            echo $e->getPrevious()->getMessage() . "\n";

            return false;
        }

        // Show all messages?
        if ($verbose || $hasErrors) {
            foreach ($result->getFormattedMessages() as $message) {
                echo '- ' . $message . "\n";
            }
        }

        // No messages printed, but result required?
        if (!$verbose && $showResult) {
            echo $result->isSuccess() ? 1 : 0;
        }

        // Show all messages?

        return '';
    }

    /**
     * Sync Geo Json
     *
     * @return bool | string
     */
    public function syncGeoJsonAction()
    {
        $request = $this->getRequest();

        if (!$request instanceof ConsoleRequest) {
            throw new \RuntimeException(
                'You can only use this action from a console!'
            );
        }

        $verbose = $request->getParam('verbose', false)
            || $request->getParam('v', false);
        $showResult = $request->getParam('result', false)
            || $request->getParam('r', false);
        //$dryRun     = $request->getParam('dry', false)
        //  || $request->getParam('d', false);

        /**
         * Libadmin importer
         *
         * @var Importer $importer
         */
        try {
            $importer = $this->serviceLocator
                ->get('Swissbib\Libadmin\Importer');
            $result   = $importer->importGeoJsonData();
            $hasErrors = $result->hasErrors();
        } catch (ServiceNotCreatedException $e) {
            // handle service exception
            echo "- Fatal error\n";
            echo "- Stopped with exception: " . get_class($e) . "\n";
            echo "===============================================================\n";
            echo $e->getMessage() . "\n";
            echo $e->getPrevious()->getMessage() . "\n";

            return false;
        }

        // Show all messages?
        if ($verbose || $hasErrors) {
            foreach ($result->getFormattedMessages() as $message) {
                echo '- ' . $message . "\n";
            }
        }

        // No messages printed, but result required?
        if (!$verbose && $showResult) {
            echo $result->isSuccess() ? 1 : 0;
        }

        // Show all messages?

        return '';
    }
}
