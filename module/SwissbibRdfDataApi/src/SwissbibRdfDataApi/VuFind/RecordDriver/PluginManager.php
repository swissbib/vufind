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
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category VuFind
 * @package  ElasticSearch\VuFind\RecordDriver
 * @author   Christoph Boehm <cbo@outermedia.de>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.vufind.org  Main Page
 */
namespace SwissbibRdfDataApi\VuFind\RecordDriver;

use Interop\Container\ContainerInterface;
use VuFind\RecordDriver\AbstractBase;

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

    static $conceptRegex = ['/person/','/bibliographicResource/','/gnd/'];


    /**
     * PluginManager constructor.
     *
     * @param ContainerInterface $serviceLocator Service Locator
     * @param array              $v3config       v3config
     */
    public function __construct(
        ContainerInterface $serviceLocator,
        array $v3config = []
    ) {
        // Record drivers are not meant to be shared -- every time we retrieve one,
        // we are building a brand new object.
        $this->sharedByDefault = false;
        parent::__construct($serviceLocator, $v3config);
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

        $this->checkRecordType();

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

    /**
     * Convenience method to retrieve a populated Solr record driver.
     *
     * @param array $data Raw ElasticSearch data
     *
     * @return AbstractBase
     */
    public function getRdfDataApiSearchRecord($data)
    {
        $recordType = 'API' . ucwords($this->checkRecordType($data->{'@context'}));

        //if (isset($data['_type'])) {
        //    $key = 'API' . ucwords($data['_type']);
        //    $recordType = $this->has($key) ? $key : self::DEFAULT_RECORD;
        //} else {
        //    $recordType = self::DEFAULT_RECORD;
        //}
        // Build the object:
        $driver = $this->get($recordType);
        $driver->setRawData($data);
        return $driver;
    }


    private function checkRecordType(string $contextURI) {

        $match = null;
        foreach(self::$conceptRegex as $regex) {
            preg_match($regex, $contextURI,$matches);
            if (count($matches)) {$match = $matches[0]; break;}
        }

        //we check the context from lobid which is called
        //http://lobid.org/gnd/context.jsonld
        //But our driver is called subject so we have to re-map it -
        // might be changed in the future
        if (isset($match) && $match === "gnd") {$match = "subject";}
        return isset($match) ? $match : self::DEFAULT_RECORD;

    }


}
