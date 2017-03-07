<?php
/**
 * Controller for manage console commands.
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
 * @package  Controller
 * @author   Simone Cogno <scogno@snowflake.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
namespace Swissbib\Controller;

use Swissbib\Services\NationalLicence;

/**
 * Class ConsoleController.
 *
 * @category Swissbib_VuFind2
 * @package  Controller
 * @author   Simone Cogno <scogno@snowflake.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
class ConsoleController extends BaseController
{
    /**
     * Send the National Licence user list export in .csv format via e-mail.
     *
     * @return void
     * @throws \Exception
     */
    public function sendNationalLicenceUsersExportAction()
    {
        /**
         * National Licence service.
         *
         * @var NationalLicence $nationalLicenceService
         */
        $nationalLicenceService = $this->getServiceLocator()
            ->get('Swissbib\NationalLicenceService');
        $nationalLicenceService->sendExportEmail();
    }

    /**
     * Script command for update the national licence users with their
     * new attributes.
     *
     * @return void
     * @throws \Exception
     */
    public function updateNationalLicenceUserInfoAction()
    {
        echo "Update national licence users info cron job started.\r\n";
        //These lines allow to retrieve the route urls from the controller command
        //http://stackoverflow.com/questions/27295895/how-can-i-
        //create-a-url-in-a-console-controller-in-zf2
        $event = $this->getEvent();
        $http = $this->getServiceLocator()->get('HttpRouter');
        $router = $event->setRouter($http);
        $request = new \Zend\Http\Request();
        $request->setUri('');
        $router = $event->getRouter();
        $routeMatch = $router->match($request);

        /**
         * National licence service.
         *
         * @var NationalLicence $nationalLicenceService
         */
        $nationalLicenceService = $this->getServiceLocator()
            ->get('Swissbib\NationalLicenceService');
        $nationalLicenceService->checkAndUpdateNationalLicenceUserInfo();
    }
}
