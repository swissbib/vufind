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
namespace ElasticSearch\VuFind\RecordDriver;

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

    protected $searchYaml = 'elasticsearch_adapter.yml';

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
        //preg_replace('[0-9]','',$data['_index']);

        if (is_array($data) && array_key_exists("_source", $data)
            && array_key_exists("@type", $data["_source"])
        ) {
            $recordType = $this->_mapType2RecordDriver(
                $data["_source"]["@type"]
            );
        } elseif (is_array($data) && array_key_exists("_source", $data)
            && array_key_exists("type", $data["_source"])
        ) {
            //for subjects the type is in type field instead of @type
            $recordType =  $this->_mapType2RecordDriver($data["_source"]["type"]);
        } else {
            $recordType = self::DEFAULT_RECORD;
        }

        // Build the object:
        $driver = $this->get($recordType);
        $driver->setRawData($data);
        return $driver;
    }

    /**
     * Maps the type to the record driver
     *
     * @param string $sourceType the source type
     *
     * @return string
     */
    private function _mapType2RecordDriver($sourceType)
    {
        $specReader = $this->creationContext->get('VuFind\SearchSpecsReader');
        $yaml = $specReader->get($this->searchYaml);
        $index_2_driver_mapping
            = $yaml['parameters']['mappings']['index_2_driver_mapping'];

        if (!is_array($sourceType)) {
            $sourceType = [$sourceType];
        }

        $recordType = self::DEFAULT_RECORD;

        $callable = function ($st) use ($index_2_driver_mapping, &$recordType) {
            if (self::DEFAULT_RECORD != $recordType) {
                return;
            }

            foreach ($index_2_driver_mapping as $key => $definedTypes) {
                foreach ($definedTypes as $definedType) {
                    if ($st == $definedType || strpos($st, $definedType) !== false) {
                        $test = strpos($st, $definedType);
                        $recordType = $key;
                        break 2;
                    }
                }
            }
        };

        array_map($callable, $sourceType);

        return $recordType;
    }
}
