<?php
/**
 * Controller of the National Licence page on the Swissbib account.
 *
 * PHP version 7
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
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category Swissbib_VuFind
 * @package  Controller
 * @author   Simone Cogno <scogno@snowflake.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
namespace Swissbib\Controller;

use Laminas\Mvc\MvcEvent;
use Laminas\ServiceManager\ServiceManager;
use Laminas\View\Model\ViewModel;
use Swissbib\Services\NationalLicence;
use Swissbib\VuFind\Db\Row\NationalLicenceUser;

/**
 * Class NationalLicencesController.
 *
 * @category Swissbib_VuFind
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
     * @param ServiceManager $sm Service Manager.
     */
    public function __construct(ServiceManager $sm)
    {
        $this->nationalLicenceService = $sm->get('Swissbib\NationalLicenceService');
        $this->nationalLicenceService->setServiceLocator($sm);
        parent::__construct($sm);
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
            = $_SERVER['uniqueID'] ?? null;
        $givenName
            = $_SERVER['givenName'] ?? null;
        $surname
            = $_SERVER['surname'] ?? null;
        $persistentId
            = $_SERVER['persistent-id'] ?? null;
        $homePostalAddress
            = $_SERVER['homePostalAddress'] ?? null;
        $mobile
            = $_SERVER['mobile'] ?? null;
        $homeOrganizationType
            = $_SERVER['home_organization_type'] ?? null;
        $homeOrganization
            = $_SERVER['homeOrganization'] ?? null;
        $affiliation
            = $_SERVER['affiliation'] ?? null;
        $swissLibraryPersonResidence
            = $_SERVER['swissLibraryPersonResidence'] ?? null;
        $swissEduIDUsage1y
            = $_SERVER['swissEduIDUsage1y'] ?? null;
        $swissEduIdAssuranceLevel
            = $_SERVER['swissEduIdAssuranceLevel'] ?? null;

        /**
         * National licence user.
         *
         * @var NationalLicenceUser $user
         */
        $user = null;

        if ($homeOrganization == "eduid.ch"
            or $homeOrganization == "test.eduid.ch"
        ) {
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
                $this->flashMessenger()->addErrorMessage(
                    $this->translate('snl.error')
                );
                $this->flashMessenger()->addErrorMessage(
                    $this->translate($e->getMessage())
                );
                $view = new ViewModel(
                    [
                        'error' => true
                    ]
                );
                return $view;
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
                = $this->nationalLicenceService
                ->isTemporaryAccessCurrentlyValid($user);
            $hasAcceptedTermsAndConditions
                = $user->hasAcceptedTermsAndConditions();
            $hasVerifiedHomePostalAddress
                = $this->nationalLicenceService->hasVerifiedSwissAddress();
            $hasPermanentAccess                 = $user->hasRequestPermanentAccess();

            $NLUrl = "/Search/Results?filter%5B%5D=union%3A%22NATIONALLICENCE%22";

            if ($hasPermanentAccess) {
                $this->flashMessenger()->addSuccessMessage(
                    [
                        'msg' =>
                            $this->translate('snl.nationalLicenceCompliant') .
                            ' <a href="' . $NLUrl . '">' .
                            $this->translate('snl.nationalLicencesContent') .
                            "</a>.",
                        'html' => true
                    ]
                );
            } elseif ($temporaryAccessValid) {
                $message
                    =  $this->translate(
                        'snl.yourTemporaryAccessWasCreatedSuccessfully'
                    ) .
                    ' <a href="' . $NLUrl . '">' .
                    $this->translate('snl.nationalLicencesContent') .
                    "</a>. " .
                    $this->translate(
                        'snl.theAddressNotVerifiedYet.verify'
                    ) .
                    " " .
                    $this->translate(
                        'snl.forPermanent'
                    );
                $this->flashMessenger()->addSuccessMessage(
                    [
                        'msg' => $message,
                        'html' => true
                    ]
                );
            } else {
                if (!$temporaryAccessValid && !$hasPermanentAccess) {
                    $this->activateTemporaryAccess();
                }
            }

            if (!$hasPermanentAccess && $hasVerifiedHomePostalAddress) {
                $this->activatePermanentAccess();
            }

            $view = new ViewModel(
                [
                    'nonEduId' =>
                        false,
                    'swissLibraryPersonResidence' =>
                        $swissLibraryPersonResidence,
                    'homePostalAddress' =>
                        $homePostalAddress,
                    'mobile' =>
                        $mobile,
                    'user' =>
                        $user,
                    'isSwissPhoneNumber' =>
                        $isSwissPhoneNumber,
                    'isHomePostalAddressInSwitzerland' =>
                        $isHomePostalAddressInSwitzerland,
                    'isNationalLicenceCompliant' =>
                        $isNationalLicenceCompliant,
                    'temporaryAccessValid' =>
                        $temporaryAccessValid,
                    'hasAcceptedTermsAndConditions' =>
                        $hasAcceptedTermsAndConditions,
                    'hasPermanentAccess' =>
                        $hasPermanentAccess,
                    'hasVerifiedHomePostalAddress' =>
                        $hasVerifiedHomePostalAddress,
                    'error' => false,
                ]
            );
            return $view;
        } else {
            $view = new ViewModel(
                [
                    'nonEduId' => true,
                    'error' => false,
                ]
            );
            return $view;
        }
    }

    /**
     * Method called before every action. It checks if the user is authenticated
     * and it redirects it to the login page otherwise.
     *
     * @param MvcEvent $e MvcEvent.
     *
     * @return mixed|\Laminas\Http\Response
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
            $this->flashMessenger()->addErrorMessage(
                $this->translate($e->getMessage())
            );
        }
        return $this->redirect()->toRoute('national-licences');
    }

    /**
     * Send request for the temporary access.
     *
     * @return void
     */
    public function activateTemporaryAccess()
    {
        $accessCreatedSuccessfully = false;
        try {
            $accessCreatedSuccessfully
                = $this->nationalLicenceService->createTemporaryAccessForUser();
        } catch (\Exception $e) {
            $this->flashMessenger()->addInfoMessage(
                $this->translate($e->getMessage())
            );

            return;
        }
        if (!$accessCreatedSuccessfully) {
            $this->flashMessenger()->addErrorMessage(
                $this->translate(
                    'snl.wasNotPossibleToCreateTemporaryAccessError'
                )
            );

            return;
        }

        return $this->redirect()->toRoute('national-licences');
    }

    /**
     * Set the permanent access for the current user. Internally this will also
     * adds the user to the National Licence Program using the Switch API.
     *
     * @return void
     */
    public function activatePermanentAccess()
    {
        $accessCreatedSuccessfully = false;
        try {
            $accessCreatedSuccessfully
                = $this->nationalLicenceService->createPermanentAccessForUser();
        } catch (\Exception $e) {
            $this->flashMessenger()->addInfoMessage(
                $this->translate($e->getMessage())
            );

            return;
        }
        if (!$accessCreatedSuccessfully) {
            $this->flashMessenger()->addErrorMessage(
                $this->translate(
                    'snl.requestPermanentAccessNotSuccessful'
                )
            );

            return;
        }
        $this->flashMessenger()->addSuccessMessage(
            $this->translate(
                'snl.yourRequestPermanentAccessSuccessful'
            )
        );
        return $this->redirect()->toRoute('national-licences');
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
}
