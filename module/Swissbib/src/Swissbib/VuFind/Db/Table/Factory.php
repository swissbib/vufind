<?php
/**
 * Factory for DB tables.
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
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @category Swissbib_VuFind2
 * @package  VuFind_Db_Table
 * @author   Matthias Edel <matthias.edel@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://vufind.org Main Site
 */
namespace Swissbib\VuFind\Db\Table;
use Zend\ServiceManager\ServiceManager;

/**
 * Factory for DB tables.
 *
 * @category VuFind
 * @package  Db_Table
 * @author   Matthias Edel <matthias.edel@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://vufind.org/wiki/development Wiki
 *
 * @codeCoverageIgnore
 */
class Factory
{
    /**
     * Return row prototype object (null if unavailable)
     *
     * @param ServiceManager $sm   Service manager
     * @param string         $name Name of row prototype to retrieve
     *
     * @return object
     */
    public static function getRowPrototype(ServiceManager $sm, $name)
    {
        if ($name) {
            $rowManager = $sm->getServiceLocator()->get('VuFind\DbRowPluginManager');

            return $rowManager->has($name) ? $rowManager->get($name) : null;
        }
        return null;
    }

    /**
     * Construct a generic table object.
     *
     * @param string         $name    Name of table to construct (fully qualified
     *                                class name, or else a class name within
     *                                the current namespace)
     * @param ServiceManager $sm      Service manager
     * @param string         $rowName Name of custom row prototype object to
     *                                retrieve (null for none).
     * @param array          $args    Extra constructor arguments for table object
     *
     * @return object
     */
    public static function getGenericTable($name, ServiceManager $sm,
        $rowName = null, $args = []
    ) {
        // Prepend the current namespace unless we receive a FQCN:
        $class = (strpos($name, '\\') === false)
            ? __NAMESPACE__ . '\\' . $name : $name;
        if (!class_exists($class)) {
            throw new \Exception('Cannot construct ' . $class);
        }
        $adapter = $sm->getServiceLocator()->get('VuFind\DbAdapter');
        $config = $sm->getServiceLocator()->get('config');
        return new $class(
            $adapter, $sm, $config, static::getRowPrototype($sm, $rowName)
        );
    }

    /**
     * Default factory behavior.
     *
     * @param string $name Method name being called
     * @param array  $args Method arguments
     *
     * @return object
     */
    public static function __callStatic($name, $args)
    {
        // Strip "get" off method name, and use the remainder as the table name;
        // grab the first argument to pass through as the service manager.
        $dbName = substr($name, 3);
        return static::getGenericTable(
            $dbName, array_shift($args), strtolower($dbName)
        );
    }

    /**
     * Construct the NationalLicenceUser table.
     *
     * @param ServiceManager $sm Service manager.
     *
     * @return NationalLicenceUser
     */
    public static function getNationalLicenceUser(ServiceManager $sm)
    {
        $sessionManager = $sm->getServiceLocator()->get('VuFind\SessionManager');
        $session = new \Zend\Session\Container('List', $sessionManager);
        return static::getGenericTable(
            'NationalLicenceUser', $sm, 'nationallicence', [$session]
        );
    }

    /**
     * Construct the PuraUser table.
     *
     * @param ServiceManager $sm Service manager.
     *
     * @return PuraUser
     */
    public static function getPuraUser(ServiceManager $sm)
    {
        $sessionManager = $sm->getServiceLocator()->get('VuFind\SessionManager');
        $session = new \Zend\Session\Container('List', $sessionManager);
        return static::getGenericTable(
            'PuraUser', $sm, 'pura', [$session]
        );
    }
}
