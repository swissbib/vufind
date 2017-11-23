<?php
/**
 * Service to manage Private Users remote access.
 *
 * PHP version 5
 * Copyright (C) project swissbib, University Library Basel, Switzerland
 * http://www.swissbib.org  / http://www.swissbib.ch / http://www.ub.unibas.ch
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License version 2,
 * as published by the Free Software Foundation.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @category Swissbib_VuFind2
 * @package  Services
 * @author   Lionel Walter <lionel.walter@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
namespace Swissbib\Services;

use Zend\ServiceManager\ServiceLocatorInterface;
use Swissbib\VuFind\Db\Row\PuraUser;

/**
 * Class Pura.
 *
 * @category Swissbib_VuFind2
 * @package  Service
 * @author   Lionel Walter <lionel.walter@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
class Pura
{
    /**
     * ServiceLocator.
     *
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * Config.
     *
     * @var array $config
     */
    protected $config;

    /**
     * List of all publishers whith their
     * contracts with libraries as well as
     * url and name and similar information
     *
     * @var array $publishers
     */
    protected $publishers;

    /**
     * NationalLicence constructor.
     *
     * @param object $config Config
     * @param array $publishers List of Publishers
     */
    public function __construct($config, array $publishers)
    {
        $this->config = $config;
        $this->publishers = $publishers;
    }

    /**
     * Get the list of publishers which have a contract with this library
     *
     * @param $libraryCode (the library code, for example Z01)
     * @return array PublishersWithContracts with that library
     */
    public function getPublishersForALibrary($libraryCode)
    {
        $publishersWithContracts=[];
        foreach ($this->publishers as $publisher) {
            if ($this->hasContract($libraryCode,$publisher))
            {
                array_push($publishersWithContracts,$publisher);
            }
        }
        return $publishersWithContracts;
    }

    /**
     * Get a NationalLicenceUser or creates a new one if is not existing in the
     * database.
     *
     * @param string $userNumber id if the pura-user table
     *
     * @return PuraUser $user
     * @throws \Exception
     */
    public function getPuraUser(
        $userNumber
    ) {
        /**
         * Pura user table.
         *
         * @var \Swissbib\VuFind\Db\Table\PuraUser $userTable
         */
        $userTable = $this->getTable(
            '\\Swissbib\\VuFind\\Db\\Table\\PuraUser'
        );
        $user = $userTable->getUserByPersistentId($persistentId);

        return $user;
    }

    /**
     * @param $libraryCode the library code, for example Z01
     * @param $publisher, an array of properties for a specific publisher, for example cambridge
     * @return bool
     */
    protected function hasContract($libraryCode, $publisher)
    {
        if (in_array($libraryCode,$publisher["libraries_with_contract"]))
        {
            return true;
        }
        else
        {
            return false;
        }
    }
}
