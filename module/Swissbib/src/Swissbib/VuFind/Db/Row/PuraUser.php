<?php
/**
 * Row Definition for pura user.
 * PHP version 7
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
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category VuFind
 * @package  VuFind_Db_Row
 * @author   Lionel Walter <lionel.walter@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://vufind.org Main Site
 */
namespace Swissbib\VuFind\Db\Row;

use VuFind\Db\Row\RowGateway;
use VuFind\Db\Table\User;

/**
 * Class NationalLicenceUser.
 *
 * @category VuFind
 * @package  VuFind_Db_Row
 * @author   Lionel Walter <lionel.walter@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://vufind.org Main Site
 */
class PuraUser extends RowGateway
{
    /**
     * User.
     *
     * @var \VuFind\Db\Row\User $relUser
     */
    protected $relUser;

    /**
     * Constructor.
     *
     * @param \Zend\Db\Adapter\Adapter $adapter Database adapter
     */
    public function __construct($adapter)
    {
        parent::__construct('id', 'pura_user', $adapter);
    }

    /**
     * Get the expiration date of the temporary access to the
     * National Licence content.
     *
     * @return \DateTime
     */
    public function getExpirationDate()
    {
        return new \DateTime($this->date_expiration);
    }

    /**
     * Get vufind user id.
     *
     * @return string
     */
    public function getVufindUserId()
    {
        return $this->user_id;
    }

    /**
     * Get pura barcode
     *
     * @return string
     */
    public function getBarcode()
    {
        return $this->barcode;
    }

    /**
     * Get the library code (the library for which this user
     * has registered for pura)
     *
     * @return string
     */
    public function getLibraryCode()
    {
        return $this->library_code;
    }

    /**
     * Check if user has access
     *
     * @return bool
     */
    public function hasAccess()
    {
        return $this->has_access;
    }

    /**
     * Set related user.
     *
     * @param \VuFind\Db\Row\User $relUser Related user
     *
     * @return void
     */
    public function setRelUser($relUser)
    {
        $this->relUser = $relUser;
    }

    /**
     * Set user id related to the User db table.
     *
     * @param int $id User id  Id
     *
     * @return void
     */
    public function setUserId($id)
    {
        $this->user_id = $id;
    }

    /**
     * Set the last_account_extension_request field.
     *
     * @param \DateTime $date Account extension date expiration
     *
     * @return void
     */
    public function setLastAccountExtensionRequest($date)
    {
        $this->last_account_extension_request = $date->format('Y-m-d H:i:s');
    }
}
