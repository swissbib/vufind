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
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA    02111-1307    USA
 *
 * @category VuFind
 * @package  ElasticSearch\VuFind\RecordDriver
 * @author   Christoph Boehm <cbo@outermedia.de>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.vufind.org  Main Page
 */
namespace ElasticSearch\VuFind\RecordDriver;

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
        $driver = new ElasticSearch(
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
        $driver = new ESPerson(
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
        return new ESBibliographicResource();
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
        return new ESSubject();
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
        return new ESOrganisation();
    }
}
