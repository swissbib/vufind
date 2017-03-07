<?php
/**
 * Controller of the National Licence page on the Swissbib account.
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
 * @package  Controller
 * @author   Simone Cogno <scogno@snowflake.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
namespace Swissbib\Controller;

use Swissbib\Services\NationalLicence;
use Swissbib\VuFind\Db\Row\NationalLicenceUser;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\ViewModel;

/**
 * Class NationalLicencesController.
 *
 * @category Swissbib_VuFind2
 * @package  Controller
 * @author   Simone Cogno <scogno@snowflake.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
class NationalLicencesController extends BaseController
{
    /**
     * National licence.
     *
     * @var NationalLicence
     */
    protected $nationalLicenceService;

    /**
     * Constructor.
     * NationalLicencesController constructor.
     *
     * @param NationalLicence $nationalLicenceService NationalLicence.
     */
    public function __construct(NationalLicence $nationalLicenceService)
    {
        $this->nationalLicenceService = $nationalLicenceService;
    }

    /**
     * Show the form to become compliant with the Swiss National Licences.
     *
     * @return mixed|ViewModel
     * @throws \Exception
     */
    public function indexAction()
    {
        // Get user information from the shibboleth attributes
        $uniqueId
            = isset($_SERVER['uniqueID']) ? $_SERVER['uniqueID'] : null;
        $givenName
            = isset($_SERVER['givenName']) ? $_SERVER['givenName'] : null;
        $surname
            = isset($_SERVER['surname']) ? $_SERVER['surname'] : null;
        $persistentId
            = isset($_SERVER['persistent-id']) ? $_SERVER['persistent-id'] : null;
        $homePostalAddress
            = isset($_SERVER['homePostalAddress']) ?
            $_SERVER['homePostalAddress'] : null;
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

        // Compute the checks
        $isHomePostalAddressInSwitzerland
            = $this->nationalLicenceService
                ->isAddressInSwitzerland($homePostalAddress);
        $isSwissPhoneNumber
            = $this->nationalLicenceService->isSwissPhoneNumber($mobile);
        $isNationalLicenceCompliant
            = $this->nationalLicenceService->isNationalLicenceCompliant();
        $temporaryAccessValid
            = $this->nationalLicenceService->isTemporaryAccessCurrentlyValid($user);
        $hasAcceptedTermsAndConditions      = $user->hasAcceptedTermsAndConditions();
        $hasVerifiedHomePostalAddress
            = $this->nationalLicenceService->hasVerifiedSwissAddress();
        $hasPermanentAccess                 = $user->hasRequestPermanentAccess();
        $hasAccessToNationalLicenceContent
            = $this->nationalLicenceService
                ->hasAccessToNationalLicenceContent($user);

        if ($hasAccessToNationalLicenceContent) {
            $this->flashMessenger()->addSuccessMessage(
                $this->translate('snl.nationalLicenceCompliant')
            );
        } else {
            $this->flashMessenger()->addInfoMessage(
                $this->translate('snl.youDontHaveAccessToNationalLicencesError')
            );
        }

        $view = new ViewModel(
            [
                'swissLibraryPersonResidence' => $swissLibraryPersonResidence,
                'homePostalAddress' => $homePostalAddress,
                'mobile' => $mobile,
                'user' => $user,
                'isSwissPhoneNumber' => $isSwissPhoneNumber,
                'isHomePostalAddressInSwitzerland' =>
                    $isHomePostalAddressInSwitzerland,
                'isNationalLicenceCompliant' => $isNationalLicenceCompliant,
                'temporaryAccessValid' => $temporaryAccessValid,
                'hasAcceptedTermsAndConditions' => $hasAcceptedTermsAndConditions,
                'hasPermanentAccess' => $hasPermanentAccess,
                'hasVerifiedHomePostalAddress' => $hasVerifiedHomePostalAddress,
            ]
        );

        return $view;
    }

    /**
     * Method called before every action. It checks if the user is authenticated
     * and it redirects it to the login page otherwise.
     *
     * @param MvcEvent $e MvcEvent.
     *
     * @return mixed|\Zend\Http\Response
     * @throws \Exception
     */
    public function onDispatch(MvcEvent $e)
    {
        $account = $this->getAuthManager();

        if (false === $account->isLoggedIn()) {
            $this->forceLogin(false);

            return $this->redirect()->toRoute('myresearch-home');
        } else {
            return parent::onDispatch($e);
        }
    }

    /**
     * Called when user click on the accept terms and conditions checkbox.
     * This information will be directly stored in the database.
     *
     * @return void
     */
    public function acceptTermsConditionsAction()
    {
        try {
            $this->nationalLicenceService->acceptTermsConditions();
        } catch (\Exception $e) {
            $this->flashMessenger()->addMessage(
                $this->translate($e->getMessage(), 'error')
            );
        }
        return $this->redirect()->toRoute('national-licences');
    }

    /**
     * Send request for the temporary access.
     *
     * @return void
     */
    public function activateTemporaryAccessAction()
    {
        $accessCreatedSuccessfully = false;
        try {
            $accessCreatedSuccessfully
                = $this->nationalLicenceService->createTemporaryAccessForUser();
        } catch (\Exception $e) {
            $this->flashMessenger()->addMessage(
                $this->translate($e->getMessage(), 'error')
            );
            $this->redirect()->toRoute('national-licences');

            return;
        }
        if (!$accessCreatedSuccessfully) {
            $this->flashMessenger()->addMessage(
                $this->translate(
                    'snl.wasNotPossibleToCreateTemporaryAccessError', 'error'
                )
            );
            $this->redirect()->toRoute('national-licences');

            return;
        }
        $this->flashMessenger()->addMessage(
            $this->translate(
                'snl.yourTemporaryAccessWasCreatedSuccessfully',
                'success'
            )
        );
        $this->redirect()->toRoute('national-licences');
    }

    /**
     * Set the permanent access for the current user. Internally this will also
     * adds the user to the National Licence Program using the Switch API.
     *
     * @return void
     */
    public function activatePermanentAccessAction()
    {
        try {
            $this->nationalLicenceService->setNationalLicenceCompliantFlag();
            $this->flashMessenger()->addMessage(
                $this->translate(
                    'snl.yourRequestPermanentAccessSuccessful', 'success'
                )
            );
        } catch (\Exception $e) {
            $this->flashMessenger()->addMessage(
                $this->translate($e->getMessage(), 'error')
            );
        }
        $this->redirect()->toRoute('national-licences');
    }

    /**
     * Signpost Action
     *
     * @return void
     */
    public function signpostlAction()
    {
        $test = $this->getServerUrl();
        $t = $this->getRequest()->getQuery("publisher");

        $this->redirect()->toRoute('national-licences');
    }

    /**
     * Method called when user want to extend his account. The link to access
     * to this function has to be send by e-mail.
     *
     * @return void
     */
    public function extendAccountAction()
    {
        try {
            $this->nationalLicenceService->extendAccountIfCompliant();
            if ($this->nationalLicenceService->isSetMessage()) {
                $message = $this->nationalLicenceService->getMessage();
                $this->flashMessenger()->addMessage(
                    $this->translate($message['text'], $message['type'])
                );
            }
        } catch (\Exception $e) {
            $this->flashMessenger()->addMessage(
                $this->translate($e->getMessage(), 'error')
            );
        }
        //redirect to home page
        $this->redirect()->toRoute('national-licences');
    }
}
