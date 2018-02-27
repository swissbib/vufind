<?php
/**
 * Table Definition for pura_user.
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
 * @author   Lionel Walter <lionel.walter@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://vufind.org Main Site
 */
namespace Swissbib\VuFind\Db\Table;

use VuFind\Db\Table\Gateway;
use VuFind\Db\Table\User;
use Zend\Db\Sql\Select;
use Zend\Db\Adapter\Adapter;
use VuFind\Db\Table\PluginManager;

/**
 * Class NationalLicenceUser.
 *
 * @category VuFind
 * @package  VuFind_Db_Table
 * @author   Simone Cogno <scogno@snowflake.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://vufind.org Main Site
 */
class PuraUser extends Gateway
{
    /**
     * Constructor.
     *
     * @param Adapter       $adapter Database adapter
     * @param PluginManager $tm      Table manager
     * @param array         $cfg     Zend Framework configuration
     * @param Row           $row     row object
     */
    public function __construct(Adapter $adapter, PluginManager $tm, $cfg, $row)
    {
        parent::__construct(
            $adapter, $tm, $cfg,
            $row, "pura_user"
        );
    }

    /**
     * Get user by id.
     *
     * @param int $id Id
     *
     * @return \Swissbib\VuFind\Db\Row\PuraUser
     * @throws \Exception
     */
    public function getUserById($id)
    {
        $result = $this->select(['id' => $id])
            ->current();
        if ($result == false) {
            throw new \Exception('No Pura User for the id ' . $id);
        }

        return $result;
    }

    /**
     * Get user by edu-id number and library.
     * Returns null if no user has this combination eduid number / libraryCode
     *
     * @param string $eduId       edu-id number
     * @param string $libraryCode the library code
     *
     * @return \Swissbib\VuFind\Db\Row\PuraUser
     * @throws \Exception
     */
    public function getPuraUserByEduIdAndLibrary($eduId, $libraryCode)
    {
        if (empty($eduId)) {
            throw new \Exception('Cannot fetch user with empty edu_id number');
        }
        /**
         * Pura user.
         *
         * @var \Swissbib\VuFind\Db\Row\PuraUser $puraUser
         */
        $puraUser = $this->select(
            [
                'edu_id' => $eduId,
                'library_code' => $libraryCode
            ]
        )->current();
        if (empty($puraUser)) {
            return null;
        }

        return $puraUser;
    }

    /**
     * Create a new Pura user row.
     *
     * @param string $eduId        edu-id number
     * @param string $persistentId persistent id
     * @param string $barcode      pura barcode
     * @param string $libraryCode  library code
     *
     * @return \Swissbib\VuFind\Db\Row\PuraUser $user
     * @throws \Exception
     */
    public function createPuraUserRow(
        $eduId,
        $persistentId,
        $barcode,
        $libraryCode
    ) {
        if (empty($eduId)) {
            throw new \Exception(
                'The edu-id is mandatory for creating a Pura User'
            );
        }

        if (strstr($eduId, "eduid.ch") == false) {
            throw new \Exception(
                'pura.nonEduId'
            );
        }

        /**
         * User.
         *
         * @var \Swissbib\VuFind\Db\Row\PuraUser $puraUser
         */
        $puraUser = $this->createRow();
        $puraUser->edu_id = $eduId;
        $puraUser->barcode = $barcode;
        $puraUser->library_code = $libraryCode;

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
        // If there is already a user registered in the system in the user table,
        // we link it to the pura_user table.
        if ($user) {
            // Link table User to PuraUser
                $puraUser->setUserId($user->id);
        }
        $savedUser = $puraUser->save();
        if (empty($savedUser)) {
            throw new \Exception('Impossible to create the Pura user.');
        }

        return $puraUser;
    }

    /**
     * Get list of all Pura users
     *
     * @return array
     * @throws \Exception
     */
    public function getList()
    {
        $puraUsers = $this->select(
            function (Select $select) {
                $select->where->greaterThan('id', 0);
            }
        );

        return $puraUsers;
    }
}
