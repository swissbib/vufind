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

use Laminas\Barcode\Barcode;
use Laminas\Mvc\MvcEvent;
use Laminas\ServiceManager\ServiceManager;
use Laminas\View\Model\ViewModel;
use Swissbib\Services\Pura;
use SwitchSharedAttributesAPIClient\PublishersList;

/**
 * Class NationalLicencesController.
 *
 * @category Swissbib_VuFind
 * @package  Controller
 * @author   Lionel Walter <lionel.walter@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
class PuraController extends BaseController
{
    /**
     * Pura.
     *
     * @var Pura
     */
    protected $puraService;

    /**
     * Constructor.
     * NationalLicencesController constructor.
     *
     * @param ServiceManager $sm Service Manager.
     */
    public function __construct(ServiceManager $sm)
    {
        $this->puraService = $sm->get('Swissbib\PuraService');
        $this->puraService->setServiceLocator($sm);
        parent::__construct($sm);
    }

    /**
     * Show the list of libraries which offer Pura Service
     *
     * @return mixed|ViewModel
     * @throws \Exception
     */
    public function indexAction()
    {
        //libraries for which Pura is activated
        $institutionCodes = ["Z01", "E65"];
        $institutions = [];

        foreach ($institutionCodes as $institutionCode) {
            $institution = $this->puraService->getInstitutionInfo($institutionCode);
            array_push($institutions, $institution);
        }

        $view = new ViewModel(
            [
                'institutions' => $institutions
            ]
        );

        return $view;
    }

    /**
     * Show the registration for a specific Pura Library
     *
     * @return mixed|ViewModel
     * @throws \Exception
     */
    public function libraryAction()
    {
        // Get user information from the shibboleth attributes
        $uniqueId
            = $_SERVER['uniqueID'] ?? null;
        $persistentId
            = $_SERVER['persistent-id'] ?? null;

        $libraryCode = $this->params()->fromRoute('libraryCode');

        if (!isset($libraryCode)) {
            $libraryCode = "Z01";
        }

        /**
         * Publishers List
         *
         * @var PublishersList $publishers Publishers List
         */
        $publishers = $this->puraService->getPublishers()
            ->getPublishersForALibrary($libraryCode);
        $institution = $this->puraService->getInstitutionInfo($libraryCode);
        $agbLink = $this->puraService->getAGBLink($libraryCode);
        $infoLink = $this->puraService->getInfoLink($libraryCode);

        if (strpos($uniqueId, "eduid.ch") == false) {
            $view = new ViewModel(
                [
                    'nonEduId' => true,
                    'publishers' => $publishers,
                    'institution' => $institution,
                ]
            );
            return $view;
        } else {
            /**
             * Pura user.
             *
             * @var PuraUser $user
             */
            $puraUser = null;
            try {
                // Create a national licence user liked the the current logged user
                $puraUser = $this->puraService
                    ->getOrCreatePuraUserIfNotExists(
                        $uniqueId,
                        $persistentId,
                        $libraryCode
                    );
                $vuFindUser = $this->puraService->getVuFindUser($puraUser->id);
            } catch (\Exception $e) {
                $this->flashMessenger()->addErrorMessage(
                    $this->translate($e->getMessage())
                );
            }

            $email = $vuFindUser->email;
            $firstName = $vuFindUser->firstname;
            $lastName = $vuFindUser->lastname;
            $token = $puraUser->getBarcode();

            $page = $this->params()->fromRoute('page');

            //decide if we render the barcode page or the list of resources
            //the url parameters have priority for example in the case of renewal
            $showRegistration = false;
            $showListResources = false;

            if ($page == 'registration') {
                $showRegistration = true;
            } elseif ($page == 'listResources') {
                $showListResources = true;
            } else {
                $hasAccess = $puraUser->hasAccess();
                if ($hasAccess) {
                    $showListResources = true;
                } else {
                    $showRegistration = true;
                }
            }

            $view = new ViewModel(
                [
                    'publishers' => $publishers,
                    'user' => $puraUser,
                    'institution' => $institution,
                    'email' => $email,
                    'firstname' => $firstName,
                    'lastname' => $lastName,
                    'token' => $token,
                    'showRegistration' => $showRegistration,
                    'showListResources' => $showListResources,
                    'agbLink' => $agbLink,
                    'infoLink' => $infoLink,
                    'libraryCode' => $libraryCode,
                ]
            );
            return $view;
        }
    }

    /**
     * Renders a barcode in an image, which corresponds to the token
     * The token must contain only numbers and upper case letters
     * otherwise an image containing an error text is returned
     * $factor is the size : 1 is small, 2 is larger
     *
     * @return bool
     * @throws \Laminas\Barcode\Exception\ExceptionInterface
     */
    public function barcodeAction()
    {
        $token = $this->params()->fromRoute('token');
        $size = $this->params()->fromRoute('size');

        if ($size == 'big') {
            $factor = 2;
        } else {
            $factor = 1;
        }

        // Only the text to draw is required
        $barcodeOptions = [
            'text' => $token,
            'factor' => $factor,
        ];

        // No required options
        $rendererOptions = [];

        // send the headers and the image
        Barcode::factory(
            'code39', 'image', $barcodeOptions, $rendererOptions
        )->render();

        // disable rendering
        return false;
    }

    /**
     * Method called before every action. It checks if the user is authenticated
     * and it redirects it to the login page otherwise.
     *
     * @param MvcEvent $e MvcEvent.
     *
     * @return mixed|\Laminas\Http\Response
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
}
