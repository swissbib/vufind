<?php
/**
 * Shibboleth
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

use VuFind\Auth\Shibboleth as VuFindShib;
use VuFind\Exception\Auth as AuthException;

/**
 * Shibboleth
 *
 * @category Swissbib_VuFind2
 * @package  VuFind_Auth
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
class Shibboleth extends VuFindShib
{
    /**
     * VuFind Standard is looking for Server variables delivered by Shibboleth
     * IMHO this is impossible because the variables are only available after
     * authentication
     *
     * @return bool
     */
    public function isExpired()
    {
        //return parent::isExpired();
        //todo: we still have to solve some issues:
        //-- can we solve the REDIRECT_ proefix issue in Shibboleth Server variables?
        // If yes, isExpired
        //could be implemented by looking up server variables

        //or another strategy might be to request the Shibbiloth sessionID
        //it all depends on the next evaluation (together with SWITCH)
        return false;
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
        // Check if username is set.
        $shib = $this->getConfig()->Shibboleth;
        $usernameAlternatives = explode("##", $shib->username);
        $username = "";
        foreach ($usernameAlternatives as $usernameAlternative) {
            $username = $request->getServer()->get($usernameAlternative);
            if (!empty($username)) {
                break;
            }
        }

        //$username = $request->getServer()->get($shib->username);
        if (empty($username)) {
            throw new AuthException('authentication_error_admin');
        }

        // Check if required attributes match up (so far not used in swissbib:
        foreach ($this->getRequiredAttributes() as $key => $value) {

            $valueAlternatives = explode("##", $value);
            $found = false;
            foreach ($valueAlternatives as $valuetest) {
                if (preg_match(
                    '/' . $valuetest . '/', $request->getServer()->get($key)
                )
                ) {
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                throw new AuthException('authentication_error_denied');
            }
        }

        // If we made it this far, we should log in the user!
        $user = $this->getUserTable()->getByUsername($username);

        // Variable to hold catalog password (handled separately from other
        // attributes since we need to use saveCredentials method to store it):
        $catPassword = null;

        // Has the user configured attributes to use for populating the user table?
        $attribsToCheck = [
            'cat_username', 'cat_password', 'email', 'lastname', 'firstname',
            'college', 'major', 'home_library'
        ];
        foreach ($attribsToCheck as $attribute) {
            if (isset($shib->$attribute)) {

                $tattrAlternatives = explode("##", $shib->$attribute);
                $attvalue = "";
                foreach ($tattrAlternatives as $aAlternative) {
                    $tvar = $request->getServer()->get($aAlternative);
                    if (!empty($tvar)) {
                        $attvalue = $request->getServer()->get($aAlternative);
                        break;
                    }
                }

                if ($attribute != 'cat_password' && !empty($attvalue)) {
                    $user->$attribute = $attvalue;
                } else {
                    $catPassword = $value;
                }
            }
        }

        // Save credentials if applicable:
        if (!empty($catPassword) && !empty($user->cat_username)) {
            $user->saveCredentials($user->cat_username, $catPassword);
        }

        // Save and return the user object:
        $user->save();

        return $user;
    }
}
