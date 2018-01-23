<?php
/**
 * Factory for controllers.
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
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
namespace Swissbib\Controller;

use Zend\ServiceManager\ServiceManager;

/**
 * Factory for controllers.
 *
 * @category Swissbib_VuFind2
 * @package  Controller
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
class Factory
{
    /**
     * Construct a generic controller.
     *
     * @param string         $name Name of table to construct (fully qualified
     *                             class name, or else a class name within the
     *                             current namespace)
     * @param ServiceManager $sm   Service manager
     *
     * @return object
     */
    public static function getGenericController($name, ServiceManager $sm)
    {
        // Prepend the current namespace unless we receive a FQCN:
        $class = (strpos($name, '\\') === false) ? __NAMESPACE__ . '\\' . $name
            : $name;
        if (!class_exists($class)) {
            throw new \Exception('Cannot construct ' . $class);
        }
        return new $class($sm->getServiceLocator());
    }

    /**
     * Construct a generic controller.
     *
     * @param string $name Method name being called
     * @param array  $args Method arguments
     *
     * @return object
     */
    public static function __callStatic($name, $args)
    {
        // Strip "get" from method name to get name of class; pass first argument
        // on assumption that it should be the ServiceManager object.
        return static::getGenericController(
            substr($name, 3), isset($args[0]) ? $args[0] : null
        );
    }

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
            $sm->getServiceLocator(),
            $sm->getServiceLocator()->get('VuFind\Config')->get('config')
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
        return new NationalLicencesController(
            $sm->getServiceLocator()
        );
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
        $sl = $sm->getServiceLocator();

        return new MyResearchNationalLicensesController(
            $sl->get('Swissbib\NationalLicenceService')
        );
    }

    /**
     * Get Knowledge Card Controller
     *
     * @param \Zend\ServiceManager\ServiceManager $sm Service manager
     *
     * @return \Swissbib\Controller\KnowledgeCardController
     */
    public static function getKnowledgeCardController(ServiceManager $sm)
    {
        return new KnowledgeCardController($sm->getServiceLocator());
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
        $serviceLocator = $sm->getServiceLocator();
        return new PersonDetailPageController($serviceLocator);
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
        $serviceLocator = $sm->getServiceLocator();
        return new SubjectDetailPageController($serviceLocator);
    }
}
