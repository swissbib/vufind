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
use Zend\Config\Config;
use Libadmin\Institution\InstitutionLoader;

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
     * Group Mapping
     *
     * @var Config
     */
    protected $groupMapping;

    /**
     * Groups
     *
     * @var Config
     */
    protected $groups;

    /**
     * Email service.
     *
     * @var Email $emailService
     */
    protected $emailService;

    /**
     * Pura constructor.
     *
     * @param object                  $config         Config
     * @param array                   $publishers     List of Publishers
     * @param Config                  $groupMapping   Map the institution code to a group (network)
     * @param Config                  $groups         The indices of the groups in the libadmin array
     * @param Email                   $emailService   The email service
     * @param ServiceLocatorInterface $serviceLocator Service locator.
     */
    public function __construct(
        $config,
        array $publishers,
        Config $groupMapping,
        Config $groups,
        Email $emailService,
        ServiceLocatorInterface $serviceLocator
    ) {
        $this->config = $config;
        $this->publishers = $publishers;
        $this->groupMapping = $groupMapping;
        $this->groups = $groups;
        $this->emailService = $emailService;
        $this->serviceLocator = $serviceLocator;
    }

    /**
     * Get the list of publishers which have a contract with this library
     *
     * @param string $libraryCode The library code, for example Z01
     *
     * @return array  PublishersWithContracts with that library
     */
    public function getPublishersForALibrary($libraryCode)
    {
        $publishersWithContracts = [];
        foreach ($this->publishers as $publisher) {
            if ($this->hasContract($libraryCode, $publisher)) {
                array_push($publishersWithContracts, $publisher);
            }
        }
        return $publishersWithContracts;
    }

    /**
     * Get a PuraUser
     *
     * @param string $userNumber id in the pura-user table
     *
     * @return PuraUser $user
     * @throws \Exception if the user doesn't exist
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
            'pura'
        );
        $user = $userTable->getUserById($userNumber);

        return $user;
    }

    /**
     * Get the VufindUser related to the PuraUser
     *
     * @param string $puraUserId id in the pura-user table
     *
     * @return PuraUser $user
     * @throws \Exception
     */
    public function getVuFindUser(
        $puraUserId
    ) {
        /**
         * Pura user table.
         *
         * @var \Swissbib\VuFind\Db\Table\PuraUser $userTable
         */
        $puraUserTable = $this->getTable(
            'pura'
        );
        $user = $puraUserTable->getUserById($puraUserId);

        $vuFindUserId = $user->getVufindUserId();

        $vuFindUserTable = $this->getTable('user');

        $result = $vuFindUserTable->select(['id' => $vuFindUserId])
            ->current();

        return $result;
    }

    /**
     * Retrieve serviceManager instance.
     *
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    /**
     * Set serviceManager instance.
     *
     * @param ServiceLocatorInterface $serviceLocator ServiceLocatorInterface
     *
     * @return void
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    /**
     * Get Institution Information
     *
     * @param string $libraryCode the library code (for example Z01)
     *
     * @return array Institution information
     * @throws \Exception
     */
    public function getInstitutionInfo($libraryCode)
    {
        $institutionLoader = new InstitutionLoader();

        $institutions = $institutionLoader->getGroupedInstitutions();

        $groupCode    = isset($this->groupMapping[$libraryCode]) ?
            $this->groupMapping[$libraryCode] : 'unknown';

        if ($groupCode == 'unknown') {
            throw new \Exception(
                'The institution with code ' .
                $libraryCode .
                ' is not in a libadmin group.'
            );
        }

        $groupKey = array_search($groupCode, $this->groups->toArray());

        if ($groupKey == false) {
            throw new \Exception(
                'The institution with code ' .
                $libraryCode .
                ' does not exist.'
            );
        }

        $libraryCodes = array_column(
            $institutions[$groupKey]["institutions"],
            "bib_code"
        );

        $institutionKey = array_search($libraryCode, $libraryCodes);

        if ($institutionKey == false) {
            throw new \Exception(
                'The institution with code ' .
                $libraryCode .
                ' was not found.'
            );
        }

        $institution = $institutions[$groupKey]["institutions"][$institutionKey];

        return $institution;
    }

    /**
     * Return true if a library has a contract with this publisher
     *
     * @param string $libraryCode the library code, for example Z01
     * @param array  $publisher   array of properties for a specific publisher
     *
     * @return bool
     */
    protected function hasContract($libraryCode, $publisher)
    {
        if (in_array($libraryCode, $publisher["libraries_with_contract"])) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get a database table object.
     *
     * @param string $table Name of table to retrieve
     *
     * @return \VuFind\Db\Table\Gateway
     */
    protected function getTable($table)
    {
        return $this->serviceLocator
            ->get('VuFind\DbTablePluginManager')
            ->get($table);
    }

    /**
     * Return a unique id
     *
     * @return string
     */
    public function createUniqueId()
    {
        /* To make sure it is unique, maybe we could check if the id is already
         * present and generate another one if needed
         */
        $bytes = openssl_random_pseudo_bytes(7);
        $hex   = bin2hex($bytes);

        return strtoupper($hex);
    }

    /**
     * Get a PuraUser or creates a new one if is not existing in the
     * database.
     *
     * @param string $eduId        Edu-id number like 321983219839218@eduid.ch
     * @param string $persistentId persistent id (needed to link with user table)
     * @param string $libraryCode  the library code of the user library
     *
     * @return PuraUser $user
     * @throws \Exception
     */
    public function getOrCreatePuraUserIfNotExists(
        $eduId,
        $persistentId,
        $libraryCode
    ) {
        /**
         * Pura user table.
         *
         * @var \Swissbib\VuFind\Db\Table\PuraUser $puraUserTable
         */
        $puraUserTable = $this->getTable(
            'pura'
        );
        $puraUser = $puraUserTable->getPuraUserByEduIdAndLibrary(
            $eduId, $libraryCode
        );

        $barcode = $this->createUniqueId();

        if (empty($puraUser)) {
            $puraUser = $puraUserTable->createPuraUserRow(
                $eduId,
                $persistentId,
                $barcode,
                $libraryCode
            );
        }

        return $puraUser;
    }

    /**
     * Check the current validity of Pura users
     * (check expiration date for example, send emails)
     *
     * @return void
     */
    public function checkValidityPuraUsers()
    {
        $users = $this->getListPuraUsers();

        //Foreach users
        /**
         * Pura user.
         *
         * @var PuraUser $user
         */
        foreach ($users as $user) {
            //we send an email to user with expired expiration date
            if (!($this->isAccountExtensionEmailHasAlreadyBeenSent($user))
                && new \DateTime() > $user->getExpirationDate()
            ) {
                $this->emailService->sendPuraAccountExtensionEmail(
                    $this->getVuFindUser($user->id)
                );
                $user->setLastAccountExtensionRequest(new \DateTime());
                $user->save();
                echo "Email sent to " .
                    $this->getVuFindUser($user->id)->email . "\n";
            }
        }
    }

    /**
     * Get list of all pura users
     *
     * @return \Zend\Db\ResultSet\ResultSet
     * @throws \Exception
     */
    public function getListPuraUsers()
    {
        /**
         * Pura user table.
         *
         * @var \Swissbib\VuFind\Db\Table\PuraUser $userTable
         */
        $userTable = $this->getTable(
            'pura'
        );
        return $userTable->getList();
    }

    /**
     * Return true if the account extension email has already been sent
     *
     * @param PuraUser $user user
     *
     * @return bool
     */
    protected function isAccountExtensionEmailHasAlreadyBeenSent($user)
    {
        $dateRequest = $user->last_account_extension_request;
        if (empty($dateRequest)) {
            return false;
        }
        return true;
    }
}
