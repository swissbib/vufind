<?php
/**
 * ShibbolethMock
 *
 * PHP version 5
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
 * @package  VuFind_Auth
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
namespace Swissbib\VuFind\Auth;

use VuFind\Auth\AbstractBase;
use VuFind\Exception\Auth as AuthException;
use Zend\Crypt\Password\Bcrypt;

/**
 * Class ShibbolethMock
 * This driver should be used in environments where Shibboleth deployment
 * (Service Provider) is difficult or impossible
 *
 * Always the same user (stored in the VuFind database) is returned instead of
 * an authenticated user against an IDP
 * (name and password can be configured)
 * For swissbib a shibboleth based authentication works in combination with a
 * MultiBackend Catalog configuration
 * (actually not part of the original VuFind authentication)
 *
 * @category Swissbib_VuFind2
 * @package  VuFind_Auth
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
class ShibbolethMock extends AbstractBase
{
    /**
     * Catalog connection
     *
     * @var \VuFind\ILS\Connection
     */
    protected $catalog = null;

    /**
     * Set the ILS connection for this object.
     *
     * @param \VuFind\ILS\Connection $connection ILS connection to set
     */
    public function __construct(\VuFind\ILS\Connection $connection)
    {
        $this->setCatalog($connection);
    }

    /**
     * Attempt to authenticate the current user.  Throws exception if login fails.
     *
     * @param \Zend\Http\PhpEnvironment\Request $request Request object containing
     * account credentials.
     *
     * @throws AuthException
     * @return \VuFind\Db\Row\User Object representing logged-in user.
     */
    public function authenticate($request)
    {
        // Make sure the credentials are non-blank:
        $this->username = trim($request->getPost()->get('username'));
        $this->password = trim($request->getPost()->get('password'));
        if ($this->username == '' || $this->password == '') {
            throw new AuthException('authentication_error_blank');
        }

        // Validate the credentials:
        $user = $this->getUserTable()->getByUsername($this->username, false);
        if (!is_object($user) || !$this->checkPassword($this->password, $user)) {
            throw new AuthException('authentication_error_invalid');
        }

        // If we got this far, the login was successful:
        return $user;
    }

    /**
     * Set Catalog
     *
     * @param \VuFind\ILS\Connection $catalog Connection
     *
     * @return void
     */
    public function setCatalog(\VuFind\ILS\Connection $catalog)
    {
        $this->catalog = $catalog;
    }

    /**
     * Get Catalog
     *
     * @return \VuFind\ILS\Connection
     */
    public function getCatalog()
    {
        return $this->catalog;
    }

    /**
     * Check that the user's password matches the provided value.
     *
     * @param string $password Password to check.
     * @param object $userRow  The user row.  We pass this instead of the password
     *                         because we may need to check different values
     *                         depending on the password hashing configuration.
     *
     * @throws \VuFind\Exception\PasswordSecurity
     *
     * @return Boolean
     */
    protected function checkPassword($password, $userRow)
    {
        // Special case: hashing enabled:
        if ($this->passwordHashingEnabled()) {
            if ($userRow->password) {
                throw new \VuFind\Exception\PasswordSecurity(
                    'Unexpected unencrypted password found in database'
                );
            }

            $bcrypt = new Bcrypt();

            return $bcrypt->verify($password, $userRow->pass_hash);
        }

        // Default case: unencrypted passwords:
        return $password == $userRow->password;
    }

    /**
     * Is Password Hashing enabled
     *
     * @return Boolean
     */
    protected function passwordHashingEnabled()
    {
        $config = $this->getConfig();
        return isset($config->Authentication->hash_passwords)
            ? $config->Authentication->hash_passwords : false;
    }

    /**
     * Get login targets (ILS drivers/source ID's)
     *
     * @return array
     */
    public function getLoginTargets()
    {
        return is_callable($this->getCatalog(), 'getLoginDrivers') ?
            $this->getCatalog()->getLoginDrivers() : [];
    }
}
