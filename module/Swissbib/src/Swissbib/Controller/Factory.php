<?php
/**
 * Factory for controllers.
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
 * @package  Controller
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
namespace Swissbib\Controller;

use VuFind\Controller\AbstractBaseFactory;
use Zend\ServiceManager\ServiceManager;

/**
 * Factory for controllers.
 *
 * @category Swissbib_VuFind
 * @package  Controller
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
class Factory extends AbstractBaseFactory
{
    /**
     * Construct the RecordController.
     *
     * @param ServiceManager $sm Service manager.
     *
     * @return RecordController
     */
    public static function getRecordController(ServiceManager $sm)
    {
        return new RecordController(
            $sm,
            $sm->get('VuFind\Config\PluginManager')->get('config')
        );
    }

    /**
     * Construct the ConsoleController
     *
     * @param ServiceManager $sm Service manager.
     *
     * @return ConsoleController
     */
    public function getConsoleController(ServiceManager $sm)
    {
        return new ConsoleController($sm);
    }

    /**
     * Construct the NationalLicenceController by injecting the
     * NationalLicence service.
     *
     * @param ServiceManager $sm Service manager.
     *
     * @return NationalLicencesController
     */
    public function getNationalLicenceController(ServiceManager $sm)
    {
        return new NationalLicencesController($sm);
    }

    /**
     * Construct the PuraController by injecting the
     * Pura service.
     *
     * @param ServiceManager $sm Service manager.
     *
     * @return PuraController
     */
    public static function getPuraController(ServiceManager $sm)
    {
        return new PuraController($sm);
    }

    /**
     * Construct the MyResearchNationalLicensesController by injecting the
     * NationalLicence service.
     *
     * @param ServiceManager $sm Service manager.
     *
     * @return MyResearchNationalLicensesController
     */
    public function getMyResearchNationalLicenceController(ServiceManager $sm)
    {
        return new MyResearchNationalLicensesController($sm);
    }

    /**
     * Get Person Knowledge Card Controller
     *
     * @param \Zend\ServiceManager\ServiceManager $sm Service manager
     *
     * @return \Swissbib\Controller\PersonKnowledgeCardController
     */
    public static function getPersonKnowledgeCardController(ServiceManager $sm)
    {
        return new PersonKnowledgeCardController($sm);
    }

    /**
     * Get Subject Knowledge Card Controller
     *
     * @param \Zend\ServiceManager\ServiceManager $sm Service manager
     *
     * @return \Swissbib\Controller\SubjectKnowledgeCardController
     */
    public static function getSubjectKnowledgeCardController(ServiceManager $sm)
    {
        return new SubjectKnowledgeCardController($sm);
    }

    /**
     * Get Person Detail Page Controller
     *
     * @param \Zend\ServiceManager\ServiceManager $sm Service manager
     *
     * @return \Swissbib\Controller\PersonDetailPageController
     */
    public static function getPersonDetailPageController(ServiceManager $sm)
    {
        return new PersonDetailPageController($sm);
    }

    /**
     * Get Person Search Controller
     *
     * @param \Zend\ServiceManager\ServiceManager $sm Service manager
     *
     * @return \Swissbib\Controller\PersonSearchController
     */
    public static function getPersonSearchController(ServiceManager $sm)
    {
        return new PersonSearchController($sm);
    }

    /**
     * Get Subject Detail Page Controller
     *
     * @param \Zend\ServiceManager\ServiceManager $sm Service manager
     *
     * @return \Swissbib\Controller\PersonDetailPageController
     */
    public static function getSubjectDetailPageController(ServiceManager $sm)
    {
        return new SubjectDetailPageController($sm);
    }

    /**
     * Method annotation wasn't available
     *
     * @param ServiceManager $sm ServiceManaegr
     *
     * @return LibadminSyncController
     */
    public function getLibadminSyncController(ServiceManager $sm)
    {
        return new LibadminSyncController($sm);
    }

    /**
     * Get Tab40Import Controller
     *
     * @param ServiceManager $sm ServiceManager
     *
     * @return Tab40ImportController
     */
    public function getTab40ImportController(ServiceManager $sm)
    {
        return new Tab40ImportController($sm);
    }

    /**
     * Cover Controller Factory
     *
     * @param ServiceManager $sm ServiceManaegr
     *
     * @return CoverController
     */
    public static function getCoverController(ServiceManager $sm)
    {
        return new CoverController(
            $sm->get('Swissbib\Cover\Loader'),
            $sm->get('VuFind\Cover\CachingProxy'),
            $sm->get('VuFind\Session\Settings')
        );
    }

    /**
     * Get Institution Detail Page Controller
     *
     * @param \Zend\ServiceManager\ServiceManager $sm Service manager
     *
     * @return \Swissbib\Controller\InstitutionDetailPageController
     */
    public static function getInstitutionDetailPageController(ServiceManager $sm)
    {
        return new InstitutionDetailPageController($sm);
    }

    /**
     * Get Institution Search Controller
     *
     * @param \Zend\ServiceManager\ServiceManager $sm Service manager
     *
     * @return \Swissbib\Controller\InstitutionSearchController
     */
    public static function getInstitutionSearchController(ServiceManager $sm)
    {
        return new InstitutionSearchController($sm);
    }
}
