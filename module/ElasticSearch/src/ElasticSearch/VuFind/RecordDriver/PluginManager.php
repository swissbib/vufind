<?php
/**
 * Record driver plugin factory
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

use VuFind\RecordDriver\AbstractBase;
use Zend\ServiceManager\ConfigInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class PluginManager
 *
 * @category VuFind
 * @package  ElasticSearch\VuFind\RecordDriver
 * @author   Christoph Boehm <cbo@outermedia.de>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.vufind.org  Main Page
 */
class PluginManager extends \VuFind\ServiceManager\AbstractPluginManager
{
    const DEFAULT_RECORD = 'ElasticSearch';

    /**
     * Constructor
     *
     * @param ServiceLocatorInterface $serviceLocator The Service Locator
     * @param ConfigInterface         $configuration  Configuration settings
     *                                                (optional)
     */
    public function __construct(
        ServiceLocatorInterface $serviceLocator,
        ConfigInterface $configuration = null
    ) {
        $this->serviceLocator = $serviceLocator;
        // Record drivers are not meant to be shared -- every time we retrieve one,
        // we are building a brand new object.
        $this->setShareByDefault(false);

        parent::__construct($configuration);
    }

    /**
     * Return the name of the base class or interface that plug-ins must conform
     * to.
     *
     * @return string
     */
    protected function getExpectedInterface()
    {
        return 'VuFind\RecordDriver\AbstractBase';
    }

    /**
     * Convenience method to retrieve a populated Solr record driver.
     *
     * @param array $data Raw ElasticSearch data
     *
     * @return AbstractBase
     */
    public function getElasticSearchRecord($data)
    {
        if (isset($data['_type'])) {
            $key = 'ES' . ucwords($data['_type']);
            $recordType = $this->has($key) ? $key : self::DEFAULT_RECORD;
        } else {
            $recordType = self::DEFAULT_RECORD;
        }
        // Build the object:
        $driver = $this->get($recordType);
        $driver->setRawData($data);
        return $driver;
    }
}
