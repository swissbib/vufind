<?php
/**
 * SwissCollections: FieldGroupFormatterRegistryFactory.php
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
use SwissCollections\Formatter\FieldFormatter\Inline;
use SwissCollections\Formatter\FieldFormatter\Line;
use SwissCollections\Formatter\FieldFormatterRegistry;
use SwissCollections\Formatter\FieldGroupFormatter\DefaultFormatter;
use SwissCollections\Formatter\FieldGroupFormatter\SucheinstiegePerson;
use SwissCollections\Formatter\FieldGroupFormatterRegistry;
use SwissCollections\Formatter\SubfieldFormatter\Brackets;
use SwissCollections\Formatter\SubfieldFormatter\Date;
use SwissCollections\Formatter\SubfieldFormatter\Simple;
use SwissCollections\Formatter\SubfieldFormatter\SimpleLine;
use SwissCollections\Formatter\SubfieldFormatter\Translate;
use SwissCollections\Formatter\SubfieldFormatterRegistry;

class FieldGroupFormatterRegistryFactory implements FactoryInterface
{
    /**
     * Create an object
     *
     * @param ContainerInterface $container     Service manager
     * @param string             $requestedName Service being created
     * @param null|array         $options       Extra options (optional)
     *
     * @return FieldGroupFormatterRegistry
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

        /**
         * @var FieldGroupFormatterRegistry $registry
         */
        $registry = new $requestedName();

        // TODO use data from $options to register all subfield formatters
        $registry->register(DefaultFormatter::$NAME, new DefaultFormatter());
        $registry->register("sucheinstiege-person", new SucheinstiegePerson());

        return $registry;
    }
}