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
     */
    public function __construct()
    {
        parent::__construct(
            'pura_user',
            'Swissbib\VuFind\Db\Row\PuraUser'
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
}
