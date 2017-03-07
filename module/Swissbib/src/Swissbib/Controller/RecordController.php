<?php
/**
 * Swissbib RecordController
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

use VuFind\Exception\ILS;
use Zend\Form\Form;
use Zend\View\Model\ViewModel,
    VuFind\Controller\RecordController as VuFindRecordController,
    VuFind\Exception\RecordMissing as RecordMissingException,
    Zend\Session\Container as SessionContainer;

/**
 * Swissbib RecordController
 *
 * @category Swissbib_VuFind2
 * @package  Controller
 * @author   Guenter Hipler  <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org
 */
class RecordController extends VuFindRecordController
{
    /**
     * Record home action
     * Catch record not found exceptions and show error page
     *
     * @return ViewModel
     */
    public function homeAction()
    {
        try {
            //GH: this is kind of a hack but in this situation not avoidable
            //MarcFormatter and Processor are hard instantiated (not as a service)
            // so you get no chance to set references for these types
            //because MarcFormatter is now implementing ServiceManagerAwareInterface
            // it will get a reference to the ServiceManager to fetch the
            //new service RedirectProtocolWrapper
            //there is another caveat: MarcFormatter is used by the XSLT Template to
            // hook into a custom PHP function using a static function
            // (which doesn't work for PHP 5.4.24 and higher
            //another issue - should be solved by snowfake because it was
            // implemented by them)
            //some work for a redesign
            $this->getServiceLocator()->get("MarcFormatter");

            return parent::homeAction();
        } catch (RecordMissingException $e) {

            return $this->forwardTo('MissingRecord', 'Home');
        }
    }

    /**
     * Save action - Allows the save template to appear,
     *   passes containingLists & nonContainingLists
     *
     * @return mixed
     */
    public function saveAction()
    {
        // Process form submission:
        if ($this->params()->fromPost('submit')) {
            return $this->processSave();
        }

        // Retrieve user object and force login if necessary:
        if (!($user = $this->getUser())) {
            return $this->forceLogin();
        }

        // If we got so far, we should save the referer for later use by the
        // ProcessSave action (to get back to where we came from after saving).
        // We shouldn't save follow-up information if it points to the Save
        // screen or the "create list" screen, as this causes confusing workflows;
        // in these cases, we will simply default to pushing the user to record view.

        $shibFollowup = new SessionContainer('ShibbolethSaveFollowup');
        $tURL = $shibFollowup->url;

        //only for Shibboleth case otherwise use the ordinary HTTP_Referer
        //(standard VuFind)
        if (!empty($tURL)) {
            $referer = $tURL;
            //clear the temporary session because we don't need it anymore
            //(the user was successfully authenticated)
            $shibFollowup->getManager()->getStorage()
                ->clear('ShibbolethSaveFollowup');

        } else {
            $referer = $this->getRequest()->getServer()->get('HTTP_REFERER');
        }
        $followup = new SessionContainer($this->searchClassId . 'SaveFollowup');

        if (substr($referer, -5) != '/Save'
            && stripos($referer, 'MyResearch/EditList/NEW') === false
        ) {
            $followup->url = $referer;
        }

        // Retrieve the record driver:
        $driver = $this->loadRecord();

        // Find out if the item is already part of any lists; save list info/IDs
        $listIds = [];
        $resources = $user->getSavedData(
            $driver->getUniqueId(), null, $driver->getResourceSource()
        );
        foreach ($resources as $userResource) {
            $listIds[] = $userResource->list_id;
        }

        // Loop through all user lists and sort out containing/non-containing lists
        $containingLists = $nonContainingLists = [];
        foreach ($user->getLists() as $list) {
            // Assign list to appropriate array based on whether or not we found
            // it earlier in the list of lists containing the selected record.
            if (in_array($list->id, $listIds)) {
                $containingLists[] = [
                    'id' => $list->id, 'title' => $list->title
                ];
            } else {
                $nonContainingLists[] = [
                    'id' => $list->id, 'title' => $list->title
                ];
            }
        }

        $view = $this->createViewModel(
            [
                'containingLists' => $containingLists,
                'nonContainingLists' => $nonContainingLists
            ]
        );
        $view->setTemplate('record/save');
        return $view;
    }

    /**
     * Action for dealing with holds.
     *
     * @return mixed
     */
    public function holdAction()
    {
        $driver = $this->loadRecord();

        // Stop now if the user does not have valid catalog credentials available:
        if (!is_array($patron = $this->catalogLogin())) {
            return $patron;
        }

        // If we're not supposed to be here, give up now!
        $catalog = $this->getILS();
        $checkHolds = $catalog->checkFunction(
            'Holds',
            [
                'id' => $driver->getUniqueID(),
                'patron' => $patron
            ]
        );
        if (!$checkHolds) {
            return $this->forwardTo('Record', 'Home');
        }

        // Do we have valid information?
        // Sets $this->logonURL and $this->gatheredDetails
        $gatheredDetails = $this->holds()->validateRequest($checkHolds['HMACKeys']);
        if (!$gatheredDetails) {
            return $this->redirectToRecord();
        }

        // Block invalid requests:
        if (!$catalog->checkRequestIsValid(
            $driver->getUniqueID(), $gatheredDetails, $patron
        )) {
            return $this->blockedholdAction();
        }

        // Send various values to the view so we can build the form:
        $pickup = $catalog->getPickUpLocations($patron, $gatheredDetails);
        $requestGroups = $catalog->checkCapability('getRequestGroups')
            ? $catalog->getRequestGroups($driver->getUniqueID(), $patron)
            : [];
        $extraHoldFields = isset($checkHolds['extraHoldFields'])
            ? explode(":", $checkHolds['extraHoldFields']) : [];

        // Process form submissions if necessary:
        if (!is_null($this->params()->fromPost('placeHold'))) {
            // If the form contained a pickup location or request group, make sure
            // they are valid:
            $valid = $this->holds()->validateRequestGroupInput(
                $gatheredDetails, $extraHoldFields, $requestGroups
            );
            if (!$valid) {
                $this->flashMessenger()
                    ->addMessage('hold_invalid_request_group', 'error');
            } elseif (!$this->holds()->validatePickUpInput(
                $gatheredDetails['pickUpLocation'], $extraHoldFields, $pickup
            )) {
                $this->flashMessenger()
                    ->addMessage('hold_invalid_pickup', 'error');
            } else {
                // If we made it this far, we're ready to place the hold;
                // if successful, we will redirect and can stop here.

                // Add Patron Data to Submitted Data
                $holdDetails = $gatheredDetails + ['patron' => $patron];

                // Attempt to place the hold:
                $function = (string)$checkHolds['function'];
                $results = $catalog->$function($holdDetails);

                // Success: Go to Display Holds
                if (isset($results['success']) && $results['success'] == true) {
                    $this->flashMessenger()
                        ->addMessage('hold_place_success', 'success');
                    return $this->redirectToRecord();
                } else {
                    // Failure: use flash messenger to display messages, stay on
                    // the current form.
                    if (isset($results['status'])) {
                        $this->flashMessenger()
                            ->addMessage($results['status'], 'error');
                    }
                    if (isset($results['sysMessage'])) {
                        $this->flashMessenger()
                            ->addMessage($results['sysMessage'], 'error');
                    }
                }
            }
        }

        // Find and format the default required date:
        $defaultDateUNIX = $this->holds()->getDefaultRequiredDate($checkHolds);
        $defaultRequired = (!empty($requiredDate)) ?
            $requiredDate : $this->getServiceLocator()->get('VuFind\DateConverter')
                ->convertToDisplayDate("U", $defaultDateUNIX);
        //$defaultRequired = $this->getServiceLocator()->get('VuFind\DateConverter')
        //    ->convertToDisplayDate("U", $defaultRequired);

        try {
            $defaultPickup
                = $catalog->getDefaultPickUpLocation($patron, $gatheredDetails);
        } catch (\Exception $e) {
            $defaultPickup = false;
        }
        try {
            $defaultRequestGroup = empty($requestGroups)
                ? false
                : $catalog->getDefaultRequestGroup($patron, $gatheredDetails);
        } catch (\Exception $e) {
            $defaultRequestGroup = false;
        }

        $requestGroupNeeded = in_array('requestGroup', $extraHoldFields)
            && !empty($requestGroups)
            && (empty($gatheredDetails['level'])
                || $gatheredDetails['level'] != 'copy');

        return $this->createViewModel(
            [
                'gatheredDetails' => $gatheredDetails,
                'pickup' => $pickup,
                'defaultPickup' => $defaultPickup,
                'homeLibrary' => $this->getUser()->home_library,
                'extraHoldFields' => $extraHoldFields,
                'defaultRequiredDate' => $defaultRequired,
                'requestGroups' => $requestGroups,
                'defaultRequestGroup' => $defaultRequestGroup,
                'requestGroupNeeded' => $requestGroupNeeded,
                'helpText' => isset($checkHolds['helpText'])
                    ? $checkHolds['helpText'] : null
            ]
        );
    }

    /**
     * Order copies action
     *
     * @return ViewModel
     */
    public function copyAction()
    {
        if (!is_array($patron = $this->catalogLogin())) {
            return $patron;
        }

        $catalog = $this->getILS();
        /**
         * Copy form
         *
         * @var Form $copyForm
         */
        $copyForm = $this->serviceLocator->get('Swissbib\Record\Form\CopyForm');
        $recordId = $this->request->getQuery('recordId');
        $itemId = $this->request->getQuery('itemId');

        try {
            $pickupLocations = $catalog->getCopyPickUpLocations(
                $patron, $recordId, $itemId
            );
            $pickupLocationsField = $copyForm->get('pickup-location');
            $pickupLocationsField->setOptions(['value_options' => $pickupLocations]);

            if ($this->request->isPost()
                && $this->request->getPost('form-name') === 'order-copy'
            ) {
                $copyForm->setData($this->request->getPost());

                if ($copyForm->isValid()) {

                    $this->getILS()->putCopy(
                        $patron, $recordId, $itemId, $copyForm->getData()
                    );

                    $this->flashMessenger()
                        ->addMessage('copy_place_success', 'success');

                    return $this->redirectToRecord();
                } else {
                    $this->flashMessenger()
                        ->addMessage('copy_place_error', 'error');
                }
            }
        } catch (ILS $e) {
            $this->flashMessenger()->addMessage('copy_error', 'error');

            return $this->createViewModel();
        }

        return $this->createViewModel(
            [
            'form' => $copyForm,
            'driver' => $this->loadRecord(),
            ]
        );
    }

    /**
     * Ajax tab action
     *
     * @return mixed
     */
    public function ajaxtabAction()
    {
        //This is the same Hack as in the $this->homeAction,
        //The MarcFormatter is using a ServiceManager in a static
        // function in an XSLT-Template
        //This call injects the ServiceManager indirectly
        $this->getServiceLocator()->get("MarcFormatter");

        return parent::ajaxtabAction();
    }
}
