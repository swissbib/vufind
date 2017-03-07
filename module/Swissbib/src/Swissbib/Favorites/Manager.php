<?php
/**
 * Manager
 *
 * PHP version 5
 *
 * Copyright (C) project swissbib, University Library Basel, Switzerland
 * http://www.swissbib.org  / http://www.swissbib.ch / http://www.ub.unibas.ch
 *
 * Date: 1/2/13
 * Time: 4:09 PM
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
 * @package  Favorites
 * @author   Guenter Hipler  <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.swissbib.org
 */
namespace Swissbib\Favorites;

use Zend\Config\Config;
use Zend\Session\Storage\StorageInterface as SessionStorageInterface;
use VuFind\Auth\Manager as AuthManager;

/**
 * Manage user favorites
 * Depending on login status, save in session or database
 *
 * @category Swissbib_VuFind2
 * @package  Favorites
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
class Manager
{
    /**
     * Instution favorites key
     *
     * @var string
     */
    protected $SESSION_DATA = 'institution-favorites';

    /**
     * Instution favorites downloaded key
     *
     * @var string
     */
    protected $SESSION_DOWNLOADED = 'institution-favorites-downloaded';

    /**
     * Session
     *
     * @var SessionStorageInterface
     */
    protected $session;

    /**
     * Group Mapping
     *
     * @var Config
     */
    protected $groupMapping;

    /**
     * AuthManager
     *
     * @var AuthManager
     */
    protected $authManager;

    /**
     * Initialize
     *
     * @param SessionStorageInterface $session      SessionStorageInterface
     * @param Config                  $groupMapping Config
     * @param AuthManager             $authManager  AuthManager
     */
    public function __construct(
        SessionStorageInterface $session,
        Config $groupMapping,
        AuthManager $authManager
    ) {
        $this->session        = $session;
        $this->groupMapping    = $groupMapping;
        $this->authManager    = $authManager;
    }

    /**
     * Get user institutions
     *
     * @return String[]
     *
     * @todo Do login check
     */
    public function getUserInstitutions()
    {
        return $this->authManager->isLoggedIn() ?
            $this->getFromDatabase() : $this->getFromSession();
    }

    /**
     * Check whether download flag is set
     *
     * @return Boolean
     */
    public function hasInstitutionsDownloaded()
    {
        return isset($this->session[$this->SESSION_DOWNLOADED]);
    }

    /**
     * Set downloaded flag in session
     *
     * @return void
     */
    public function setInstitutionsDownloaded()
    {
        $this->session[$this->SESSION_DOWNLOADED] = true;
    }

    /**
     * Save user institutions
     *
     * @param String[] $institutionCodes institution codes
     *
     * @return void
     */
    public function saveUserInstitutions(array $institutionCodes)
    {
        $this->authManager->isLoggedIn() !== false ?
            $this->saveInDatabase($institutionCodes) :
            $this->saveInSession($institutionCodes);
    }

    /**
     * Get listing data for user institutions
     *
     * @return Array[]
     */
    public function getUserInstitutionsListingData()
    {
        $institutions = $this->getUserInstitutions();
        $listing    = [];

        foreach ($institutions as $institutionCode) {
            $groupCode    = isset($this->groupMapping[$institutionCode]) ?
                $this->groupMapping[$institutionCode] : 'unknown';

            $listing[$groupCode][] = $institutionCode;
        }

        return $listing;
    }

    /**
     * Save institutions in session
     *
     * @param String[] $institutions institutions
     *
     * @return void
     */
    protected function saveInSession(array $institutions)
    {
        $this->session[$this->SESSION_DATA] = $institutions;
    }

    /**
     * Save institutions as user setting in database
     *
     * @param String[] $institutionCodes institution codes
     *
     * @return void
     */
    protected function saveInDatabase(array $institutionCodes)
    {
        $user = $this->authManager->isLoggedIn();

        $user->favorite_institutions = implode(',', $institutionCodes);
        $user->save();
    }

    /**
     * Get user institutions from session
     *
     * @return String[]
     */
    protected function getFromSession()
    {
        if (!isset($this->session[$this->SESSION_DATA])) {
            $this->session[$this->SESSION_DATA] = [];
        }

        return $this->session[$this->SESSION_DATA];
    }

    /**
     * Get user institutions from database
     *
     * @return String[]
     */
    protected function getFromDatabase()
    {
        $favoriteList    = $this->authManager->isLoggedIn()->favorite_institutions;

        return $favoriteList ? explode(',', $favoriteList) : [];
    }
}
