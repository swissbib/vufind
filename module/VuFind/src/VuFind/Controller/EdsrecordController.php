<?php
/**
 * EDS Record Controller
 *
 * PHP version 7
 *
 * Copyright (C) Villanova University 2010.
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
 * @category VuFind
 * @package  Controller
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://vufind.org Main Site
 */
namespace VuFind\Controller;

use VuFind\Exception\Forbidden as ForbiddenException;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * EDS Record Controller
 *
 * @category VuFind
 * @package  Controller
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://vufind.org Main Site
 */
class EdsrecordController extends AbstractRecord
{
    /**
     * Constructor
     *
     * @param ServiceLocatorInterface $sm Service locator
     */
    public function __construct(ServiceLocatorInterface $sm)
    {
        // Override some defaults:
        $this->searchClassId = 'EDS';
        $this->fallbackDefaultTab = 'Description';

        // Call standard record controller initialization:
        parent::__construct($sm);
    }

    /**
     * PDF display action.
     *
     * @return mixed
     */
    public function pdfAction()
    {
        $driver = $this->loadRecord();
        //if the user is a guest, redirect them to the login screen.
        $auth = $this->getAuthorizationService();
        if (!$auth->isGranted('access.EDSExtendedResults')) {
            if (!$this->getUser()) {
                return $this->forceLogin();
            }
            throw new ForbiddenException('Access denied.');
        }
        return $this->redirect()->toUrl($driver->getPdfLink());
    }

    /**
     * Is the result scroller active?
     *
     * @return bool
     */
    protected function resultScrollerActive()
    {
        $config = $this->serviceLocator->get(\VuFind\Config\PluginManager::class)
            ->get('EDS');
        return isset($config->Record->next_prev_navigation)
            && $config->Record->next_prev_navigation;
    }
}
