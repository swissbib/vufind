<?php
/**
 * Swissbib MyResearchNationalLicensesController
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
 * @package  Controller
 * @author   Guenter Hipler  <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.swissbib.org
 */
namespace Swissbib\Controller;

use Swissbib\Services\NationalLicence;
use VuFind\Exception\Auth as AuthException;
use Swissbib\VuFind\Db\Row\NationalLicenceUser;

/**
 * Swissbib MyResearchNationalLicensesController
 *
 * @category Swissbib_VuFind2
 * @package  Controller
 * @author   Guenter Hipler  <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org
 */
class MyResearchNationalLicensesController extends MyResearchController
{
    /**
     * National licence.
     *
     * @var NationalLicence
     */
    protected $nationalLicenceService;

    /**
     * Constructor.
     * MyResearchNationalLicensesController constructor.
     *
     * @param NationalLicence $nationalLicenceService NationalLicence.
     */
    public function __construct(NationalLicence $nationalLicenceService)
    {
        $this->nationalLicenceService = $nationalLicenceService;
    }

    /**
     * Handler used after login with swissEduId
     *
     * @return mixed
     */
    public function nlsignpostAction()
    {
        try {

            if (!$this->getAuthManager()->isLoggedIn()) {
                $this->getAuthManager()->login($this->getRequest());
            }
        } catch (AuthException $e) {
            $this->processAuthenticationException($e);
        }

        // we expect to call this method only as target method
        // at the end of a Shibboleth (particularly swissEduID)
        //login process
        //so: if user is not correctly logged in,
        // he/she is led to the regular login page
        if (!$this->getAuthManager()->isLoggedIn()) {
            $this->setFollowupUrlToReferer();
            return $this->forwardTo('MyResearch', 'Login');
        }

        if ($this->isAuthenticatedWithSwissEduId()) {
            //check attributes and / or start registration process
            // only if user is authenticated with swiss EduId
            $user = $this->initializeServiceInstance();

            //could we do that instead $user = $this->nationalLicenceService
            //   ->getOrCreateNationalLicenceUserIfNotExists(
            //     $_SERVER['persistent-id']);

            $hasAccessToNationalLicenceContent = $this->nationalLicenceService
                ->hasAccessToNationalLicenceContent($user);

            if (!$hasAccessToNationalLicenceContent) {
                return $this->forwardTo('national-licences', 'index');

            } else {
                $tURL = $this->getDocumentProviderURL();
                $this->redirect()->toUrl($tURL);
            }

        } else {
            //if the user is not logged in with swissEduId he/she is sent to the
            // document but we can't guarentee correct access - user should be
            // notified about this in advance on the surface
            $tURL = $this->getDocumentProviderURL();
            $this->redirect()->toUrl($tURL);

        }

    }

    /**
     * Initialized the service instance
     *
     * @return NationalLicenceUser
     */
    protected function initializeServiceInstance()
    {
        // Get user information from the shibboleth attributes
        $uniqueId
            = isset($_SERVER['uniqueID']) ? $_SERVER['uniqueID'] : null;
        $persistentId
            = isset($_SERVER['persistent-id']) ? $_SERVER['persistent-id'] : null;
        $givenName
            = isset($_SERVER['givenName']) ? $_SERVER['givenName'] : null;
        $surname
            = isset($_SERVER['surname']) ? $_SERVER['surname'] : null;
        $persistentId
            = isset($_SERVER['persistent-id']) ? $_SERVER['persistent-id'] : null;
        $homePostalAddress
            = isset($_SERVER['homePostalAddress']) ? $_SERVER['homePostalAddress'] :
            null;
        $mobile
            = isset($_SERVER['mobile']) ? $_SERVER['mobile'] : null;
        $homeOrganizationType
            = isset($_SERVER['home_organization_type']) ?
                $_SERVER['home_organization_type'] : null;
        $affiliation
            = isset($_SERVER['affiliation']) ? $_SERVER['affiliation'] : null;
        $swissLibraryPersonResidence
            = isset($_SERVER['swissLibraryPersonResidence']) ?
            $_SERVER['swissLibraryPersonResidence'] : null;
        $swissEduIDUsage1y
            = isset($_SERVER['swissEduIDUsage1y']) ?
            $_SERVER['swissEduIDUsage1y'] : null;
        $swissEduIdAssuranceLevel
            = isset($_SERVER['swissEduIdAssuranceLevel']) ?
            $_SERVER['swissEduIdAssuranceLevel'] : null;

        /**
         * National licence user.
         *
         * @var NationalLicenceUser $user
         */
        $user = null;
        try {
            // Create a national licence user liked the the current logged user
            $user = $this->nationalLicenceService
                ->getOrCreateNationalLicenceUserIfNotExists(
                    $persistentId,
                    [
                        'edu_id' => $uniqueId,
                        'persistent_id' => $persistentId,
                        'home_organization_type' => $homeOrganizationType,
                        'mobile' => $mobile,
                        'home_postal_address' => $homePostalAddress,
                        'affiliation' => $affiliation,
                        'swiss_library_person_residence' =>
                            $swissLibraryPersonResidence,
                        'active_last_12_month' => $swissEduIDUsage1y === 'TRUE',
                        'assurance_level' => $swissEduIdAssuranceLevel,
                        'display_name' => $givenName . " " . $surname
                    ]
                );
        } catch (\Exception $e) {
            $this->flashMessenger()->addMessage(
                $this->translate($e->getMessage(), 'error')
            );
        }

        return $user;

    }

    /**
     * Checks if current user is authenticated with swiss edu id
     *
     * @return boolean
     */
    protected function isAuthenticatedWithSwissEduId()
    {

        //doesn't work :
        //$idbName = $this->config->NationaLicensesWorkflow->swissEduIdIDP;

        $idbName = "eduid\.ch\/idp";

        $persistentId = isset($_SERVER['persistent-id']) ?
            $_SERVER['persistent-id'] : "";
        return (isset($idbName) && !empty($_SERVER['persistent-id'])) ?
            count(preg_grep("/$idbName/", [$persistentId])) > 0 : false;

    }

    /**
     * Gets the document provider URL
     *
     * @return mixed
     */
    protected function getDocumentProviderURL()
    {
        $publisher = $this->getRequest()->getQuery()->get("publisher");
        return $publisher;
    }

}
