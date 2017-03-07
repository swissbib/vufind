<?php
/**
 * Table Definition for national_licence_user.
 * PHP version 5
 * Copyright (C) Villanova University 2010.
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
 * @category VuFind
 * @package  VuFind_Db_Table
 * @author   Simone Cogno <scogno@snowflake.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://vufind.org Main Site
 */
namespace Swissbib\VuFind\Db\Table;

use VuFind\Db\Table\Gateway;
use VuFind\Db\Table\User;
use Zend\Db\Sql\Select;

/**
 * Class NationalLicenceUser.
 *
 * @category VuFind
 * @package  VuFind_Db_Table
 * @author   Simone Cogno <scogno@snowflake.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://vufind.org Main Site
 */
class NationalLicenceUser extends Gateway
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct(
            'national_licence_user',
            'Swissbib\VuFind\Db\Row\NationalLicenceUser'
        );
    }

    /**
     * Get user by id.
     *
     * @param int $id Id
     *
     * @return \Swissbib\VuFind\Db\Row\NationalLicenceUser
     */
    public function getUserById($id)
    {
        return $this->select(['id' => $id])
            ->current();
    }

    /**
     * Create a new National licence user row.
     *
     * @param string $persistentId Edu-id persistent id
     * @param array  $fieldsValue  Fieldd value
     *
     * @return \Swissbib\VuFind\Db\Row\NationalLicenceUser $user
     * @throws \Exception
     */
    public function createNationalLicenceUserRow(
        $persistentId,
        array $fieldsValue = []
    ) {
        if (empty($persistentId)) {
            throw new \Exception(
                'The persistent-id is mandatory for creating a National Licence User'
            );
        }

        /**
         * User.
         *
         * @var \Swissbib\VuFind\Db\Row\NationalLicenceUser $nationalUser
         */
        $nationalUser = $this->createRow();

        $nationalUser->setPersistentId($persistentId);

        foreach ($fieldsValue as $key => $value) {
            $nationalUser->$key = $value;
        }
        /**
         * User table.
         *
         * @var User $userTable
         */
        $userTable = $this->getDbTable('user');

        /**
         * User.
         *
         * @var \VuFind\Db\Row\User $user
         */
        $user = $userTable->getByUsername($persistentId);
        // If there is already a user registered in the system in the use table,
        // we link it to the
        // national_licence_user table.
        if ($user) {
            // Link table User to NationalLicenceUser
            $nationalUser->setUserId($user->id);
        }
        $savedUser = $nationalUser->save();
        if (empty($savedUser)) {
            throw new \Exception('Impossible to create the National Licence user.');
        }

        return $nationalUser;
    }

    /**
     * Update user fields.
     *
     * @param int   $persistentId         User persistent id
     * @param array $fieldsValues         Array of fields => value to update
     * @param array $fieldsValuesRelation Array of fields of the relation table=>
     *                                    value to update
     *
     * @return \Swissbib\VuFind\Db\Row\NationalLicenceUser
     * @throws \Exception
     */
    public function updateRowByPersistentId(
        $persistentId,
        array $fieldsValues,
        array $fieldsValuesRelation = null
    ) {
        //Check and convert in the right format
        if (isset($fieldsValues['active_last_12_month'])) {
            $swissEduIdUsagely = $fieldsValues['active_last_12_month'];
            if (!is_bool($swissEduIdUsagely)) {
                if (is_string($swissEduIdUsagely)) {
                    $fieldsValues['active_last_12_month']
                        = $fieldsValues['active_last_12_month'] === 'TRUE';
                } else {
                    throw new \Exception(
                        "Impossible to read the " .
                        "swissEduIdUsagely attributes. Format is incorrect."
                    );
                }
            }
        }

        $nationalLicenceUser = $this->getUserByPersistentId($persistentId);
        foreach ($fieldsValues as $key => $value) {
            if ($nationalLicenceUser->$key !== $value) {
                $nationalLicenceUser->$key = $value;
            }
        }
        if (!empty($fieldsValuesRelation)) {
            $user = $nationalLicenceUser->getRelUser();
            foreach ($fieldsValuesRelation as $key => $value) {
                if ($user->$key !== $value) {
                    $user->$key = $value;
                }
            }
            $user->save();
        }
        $nationalLicenceUser->save();

        return $nationalLicenceUser;
    }

    /**
     * Get user by persistent_id.
     *
     * @param string $persistentId Persistent id
     *
     * @return \Swissbib\VuFind\Db\Row\NationalLicenceUser
     * @throws \Exception
     */
    public function getUserByPersistentId($persistentId)
    {
        if (empty($persistentId)) {
            throw new \Exception('Cannot fetch user with empty persistent_id');
        }
        /**
         * National licence user.
         *
         * @var \Swissbib\VuFind\Db\Row\NationalLicenceUser $nationalLicenceUser
         */
        $nationalLicenceUser = $this->select(['persistent_id' => $persistentId])
            ->current();
        if (empty($nationalLicenceUser)) {
            return null;
        }
        /**
         * User table.
         *
         * @var User $userTable
         */
        $userTable = $this->getDbTable('user');
        $relUser = $userTable->getByUsername(
            $nationalLicenceUser->getPersistentId()
        );
        $nationalLicenceUser->setRelUser($relUser);

        return $nationalLicenceUser;
    }

    /**
     * Get list of all National licence users with relative VuFind users.
     *
     * @return array
     * @throws \Exception
     */
    public function getList()
    {
        /**
         * User table.
         *
         * @var User $userTable
         */
        $userTable = $this->getDbTable('user');

        $nationalLicenceUsers = $this->select(
            function (Select $select) {
                $select->where->greaterThan('id', 0);
            }
        );
        $arr_resultSet = [];
        /**
         * National licence user.
         *
         * @var \Swissbib\VuFind\Db\Row\NationalLicenceUser $nationalLicenceUser
         */
        foreach ($nationalLicenceUsers as $nationalLicenceUser) {
            /**
             * User.
             *
             * @var \VuFind\Db\Row\User $user
             */
            $user = $userTable->getByUsername(
                $nationalLicenceUser->getPersistentId()
            );
            $nationalLicenceUser->setRelUser($user);
            $arr_resultSet[] = $nationalLicenceUser;
        }

        return $arr_resultSet;
    }

    /**
     * Get number of temporary access of the last x months.
     *
     * @param object $months Number of the last months
     *
     * @return int
     */
    public function getLastTemporaryRequest($months)
    {
        $date = new \DateTime();
        $date->modify("-$months month");
        $numberOfTemporaryRequests = $this->select(
            function (Select $select) use ($date) {
                $select->where->greaterThan(
                    'request_temporary_access_created',
                    $date->format('Y-m-d H:i:s')
                );
            }
        );

        return count($numberOfTemporaryRequests);
    }

    /**
     * Get number of last permanent access requests.
     *
     * @param object $months months
     *
     * @return int
     */
    public function getNumberOfLastPermanentRequest($months)
    {
        $date = new \DateTime();
        $date->modify("-$months month");
        $numberOfTemporaryRequests = $this->select(
            function (Select $select) use ($date) {
                $select->where->greaterThan(
                    'request_permanent_access_created',
                    $date->format('Y-m-d H:i:s')
                );
            }
        );

        return count($numberOfTemporaryRequests);
    }

    /**
     * Gets last blocked user
     *
     * @param object $months months
     *
     * @return array
     * @throws \Exception
     */
    public function getLastBlockedUser($months)
    {
        $date = new \DateTime();
        $date->modify("-$months month");
        $lastBlockedUsers = $this->select(
            function (Select $select) use ($date) {
                $select->where
                    ->greaterThan('blocked_created', $date->format('Y-m-d H:i:s'))
                    ->equalTo('blocked', true);
            }
        );

        /**
         * User table.
         *
         * @var User $userTable
         */
        $userTable = $this->getDbTable('user');

        $arr_resultSet = [];
        /**
         * National licence user.
         *
         * @var \Swissbib\VuFind\Db\Row\NationalLicenceUser $lastBlockedUser
         */
        foreach ($lastBlockedUsers as $lastBlockedUser) {
            /**
             * User.
             *
             * @var \VuFind\Db\Row\User $user
             */
            $user = $userTable->getByUsername($lastBlockedUser->getPersistentId());
            $lastBlockedUser->setRelUser($user);
            $arr_resultSet[] = $lastBlockedUser;
        }

        return $arr_resultSet;
    }
}
