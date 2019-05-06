<?php
/**
 * Factory.php
 *
 * PHP Version 7
 *
 * Copyright (C) swissbib 2018
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License version 2,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.    See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category VuFind
 * @package  ElasticSearch\VuFind\RecordDriver
 * @author   Christoph Boehm <cbo@outermedia.de>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.vufind.org  Main Page
 */
namespace SwissbibRdfDataApi\VuFind\RecordDriver;

use Zend\ServiceManager\ServiceManager;

/**
 * Class Factory
 *
 * @category VuFind
 * @package  ElasticSearch\VuFind\RecordDriver
 * @author   Christoph Boehm <cbo@outermedia.de>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.vufind.org  Main Page
 */
class Factory
{
    /**
     * Creates an ElasticSearchRecord
     *
     * @param \Zend\ServiceManager\ServiceManager $sm The Service Manager
     *
     * @return \ElasticSearch\VuFind\RecordDriver\ElasticSearch
     */
    public static function getElasticSearchRecord(ServiceManager $sm)
    {
        $driver = new RdfDataApi(
            //          $sm->getServiceLocator()->get('VuFind\Config')->get('config'),
            //          null,
            //          $sm->getServiceLocator()->get('VuFind\Config')->get('searches')
        );
        //        $driver->attachSearchService($sm->getServiceLocator()->get('VuFind\Search'));
        return $driver;
    }

    /**
     * Creates a PersonRecord
     *
     * @param \Zend\ServiceManager\ServiceManager $sm The Service Manager
     *
     * @return \ElasticSearch\VuFind\RecordDriver\ESPerson
     */
    public static function getESPersonRecord(ServiceManager $sm)
    {
        $driver = new APIPerson(
            //          $sm->getServiceLocator()->get('VuFind\Config')->get('config'),
            //          null,
            //          $sm->getServiceLocator()->get('VuFind\Config')->get('searches')
        );
        //        $driver->attachSearchService($sm->getServiceLocator()->get('VuFind\Search'));
        return $driver;
    }

    /**
     * Creates a BibliographicResourceRecord
     *
     * @param \Zend\ServiceManager\ServiceManager $sm The Service Manager
     *
     * @return \ElasticSearch\VuFind\RecordDriver\ESBibliographicResource
     */
    public static function getESBibliographicResourceRecord(ServiceManager $sm)
    {
        return new APIBibliographicResource();
    }

    /**
     * Creates a SubjectRecord
     *
     * @param \Zend\ServiceManager\ServiceManager $sm The Service Manager
     *
     * @return \ElasticSearch\VuFind\RecordDriver\ESSubject
     */
    public static function getESSubjectRecord(ServiceManager $sm)
    {
        return new APISubject();
    }

    /**
     * Creates a OrganisationRecord
     *
     * @param \Zend\ServiceManager\ServiceManager $sm The Service Manager
     *
     * @return \ElasticSearch\VuFind\RecordDriver\ESOrganisation
     */
    public static function getESOrganisationRecord(ServiceManager $sm)
    {
        return new APIOrganisation();
    }
}
