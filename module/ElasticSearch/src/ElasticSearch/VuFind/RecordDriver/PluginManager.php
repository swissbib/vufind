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
        if (isset($data['_index'])) {
            //$key = 'ES' . ucwords(preg_replace('[0-9]','',$data['_index']));
            $key = 'ES' . $this->mapIndexName( ucwords(str_replace([0,1,2,3,4,5,6,7,8,9],'',$data['_index'])));
            $recordType = $this->has($key) ? $key : self::DEFAULT_RECORD;
        } else {
            $recordType = self::DEFAULT_RECORD;
        }
        // Build the object:
        $driver = $this->get($recordType);
        $driver->setRawData($data);
        return $driver;
    }

    private function mapIndexName($name) {
        $map = ['Bibr' => 'BibliographicResource'];
        return array_key_exists($name,$map) ? $map[$name]: $name;
    }



}
