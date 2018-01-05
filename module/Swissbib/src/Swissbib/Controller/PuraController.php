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

use Zend\View\Model\ViewModel;
use Swissbib\Services\Pura;
use VuFind\Db\Row\User;
use Zend\Barcode\Barcode;

/**
 * Class NationalLicencesController.
 *
 * @category Swissbib_VuFind2
 * @package  Controller
 * @author   Simone Cogno <scogno@snowflake.ch>
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
     * @param Pura $puraService Pura.
     */
    public function __construct(Pura $puraService)
    {
        $this->puraService = $puraService;
    }

    /**
     * Show the list of libraries which offer Pura Service
     *
     * @return mixed|ViewModel
     * @throws \Exception
     */
    public function indexAction()
    {
        $institutionCodes = ["Z01", "RE01001"];
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
        $libraryCode = $this->params()->fromRoute('libraryCode');

        $active = $this->params()->fromRoute('active');

        if (!isset($libraryCode)) {
            $libraryCode = "Z01";
        }

        $publishers = $this->puraService->getPublishersForALibrary($libraryCode);
        $institution = $this->puraService->getInstitutionInfo($libraryCode);

        // Get user information from the shibboleth attributes

        $uniqueId
            = isset($_SERVER['uniqueID']) ? $_SERVER['uniqueID'] : null;
        $persistentId
            = isset($_SERVER['persistent-id']) ? $_SERVER['persistent-id'] : null;

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
                    $persistentId
                );
        } catch (\Exception $e) {
            $this->flashMessenger()->addErrorMessage(
                $this->translate($e->getMessage())
            );
        }


        $vuFindUser = $this->puraService->getVuFindUser($puraUser->id);

        $email = $vuFindUser->email;
        $firstName = $vuFindUser->firstname;
        $lastName = $vuFindUser->lastname;
        $token = $puraUser->getBarcode();

        $view = new ViewModel(
            [
                'publishers' => $publishers,
                'user' => $puraUser,
                'institution' => $institution,
                'email' => $email,
                'firstname' => $firstName,
                'lastname' => $lastName,
                'token' => $token,
                'active' => $active
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
    public function barcodeAction()
    {
        $token = $this->params()->fromRoute('token');

        // Only the text to draw is required
        $barcodeOptions = [
            'text' => $token,
            'factor' => '2',
        ];

        // No required options
        $rendererOptions = [];

        // send the headers and the image
        Barcode::factory(
            'code39', 'image', $barcodeOptions, $rendererOptions
        )->render();
    }
}
