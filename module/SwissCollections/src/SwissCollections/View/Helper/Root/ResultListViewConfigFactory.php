<?php
/**
 * SwissCollections: ResultListViewConfigFactory.php
 *
 * PHP version 7
 *
 * Copyright (C) project swissbib, University Library Basel, Switzerland
 * http://www.swisscollections.org  / http://www.swisscollections.ch / http://www.ub.unibas.ch
 *
 * Date: 1/12/20
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
 * @category SwissCollections_VuFind
 * @package  SwissCollections\View\Helper\Root
 * @author   Christoph Böhm <cbo@outermedia.de>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.swisscollections.org Project Wiki
 */

namespace SwissCollections\View\Helper\Root;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Exception\ServiceNotCreatedException;
use Laminas\ServiceManager\Exception\ServiceNotFoundException;
use Laminas\ServiceManager\Factory\FactoryInterface;

class ResultListViewConfigFactory implements FactoryInterface
{
    /**
     * Create an object
     *
     * @param ContainerInterface $container     Service manager
     * @param string             $requestedName Service being created
     * @param null|array         $options       Extra options (optional)
     *
     * @return array
     *
     * @throws ServiceNotFoundException if unable to resolve the service.
     * @throws ServiceNotCreatedException if an exception is raised when
     * creating a service.
     * @throws ContainerException|\Exception if any other error occurs
     */
    public function __invoke(
        ContainerInterface $container, $requestedName,
        array $options = null
    ) {
        if (!empty($options)) {
            throw new \Exception('Unexpected options sent to factory.');
        }
        // TODO put yaml name to config!?
        $config = $container->get(\VuFind\Config\PluginManager::class)->get(
            'config'
        );
        // echo "<!-- RLVCF: " . print_r($config, true) . " -->";
        // TODO needs own subclass, because of caching or
        // use $yamlReader->get('detail-view-field-structure',true, true) instead of ->get('...')
        $yamlReader = $container->get(\VuFind\Config\YamlReader::class);
        $resultListViewFieldInfo = $yamlReader->get(
            'result-list-entry-fields.yaml', true, true
        );
        $helper = new $requestedName($resultListViewFieldInfo);
        return $helper;
    }
}