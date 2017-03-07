<?php
/**
 * Swissbib FavoritesController
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

use Zend\View\Model\ViewModel;
use Swissbib\Favorites\DataSource as FavoriteDataSource;
use Swissbib\Favorites\Manager as FavoriteManager;

/**
 * Swissbib FavoritesController
 *
 * @category Swissbib_VuFind2
 * @package  Controller
 * @author   Guenter Hipler  <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org
 */
class FavoritesController extends BaseController
{
    /**
     * Show list of already defined favorites
     *
     * @return ViewModel
     */
    public function displayAction()
    {
        $favoriteManager = $this->getFavoriteManager();

            // Are institutions already in browser cache?
        if ($favoriteManager->hasInstitutionsDownloaded()) {
            $autocompleterData    = false;
        } else {
            $autocompleterData    = $this->getAutocompleterData();

            // mark as downloaded
            $favoriteManager->setInstitutionsDownloaded();
        }

        $data = [
            'autocompleterData'     => $autocompleterData,
            'userInstitutionsList'    => $this->getUserInstitutionsList()
        ];

        //facetquery   ->>> facet.query=institution:z01

        $viewModel = new ViewModel($data);
        $viewModel->setTemplate('myresearch/favorites');
        return $viewModel;
    }

    /**
     * Add an institution to users favorite list
     * Return view for selection
     *
     * @return ViewModel
     */
    public function addAction()
    {
        $institutionCode = $this->params()->fromPost('institution');
        $sendList        = !!$this->params()->fromPost('list');

        if ($institutionCode) {
            $this->addUserInstitution($institutionCode);
        }

        if ($sendList) {
            return $this->getSelectionList();
        } else {
            return $this->getResponse();
        }
    }

    /**
     * Delete a user institution
     *
     * @return ViewModel
     */
    public function deleteAction()
    {
        $institutionCode    = $this->params()->fromPost('institution');
        $sendList        = !!$this->params()->fromPost('list');

        if ($institutionCode) {
            $this->removeUserInstitution($institutionCode);
        }

        if ($sendList) {
            return $this->getSelectionList();
        } else {
            return $this->getResponse();
        }
    }

    /**
     * Get select list view model
     *
     * @return ViewModel
     */
    public function getSelectionList()
    {
        return $this->getAjaxViewModel(
            ['userInstitutionsList' => $this->getUserInstitutionsList()],
            'favorites/selectionList'
        );
    }

    /**
     * Get data for user institution list
     *
     * @return Array[]
     */
    protected function getUserInstitutionsList()
    {
        return $this->getFavoriteManager()->getUserInstitutionsListingData();
    }

    /**
     * Add an institution to users favorite list
     *
     * @param String $institutionCode Instution code
     *
     * @return void
     */
    protected function addUserInstitution($institutionCode)
    {
        $userInstitutions = $this->getUserInstitutions();

        if (!in_array($institutionCode, $userInstitutions)) {
            $userInstitutions[] = $institutionCode;

            $this->getFavoriteManager()->saveUserInstitutions($userInstitutions);
        }
    }

    /**
     * Remove an institution from users favorite list
     *
     * @param String $institutionCode Institution code
     *
     * @return void
     */
    protected function removeUserInstitution($institutionCode)
    {
        $userInstitutions = $this->getUserInstitutions();

        if (($pos = array_search($institutionCode, $userInstitutions)) !== false) {
            unset($userInstitutions[$pos]);

            $this->getFavoriteManager()->saveUserInstitutions($userInstitutions);
        }
    }

    /**
     * Get autocompleter user institutions data
     * Fetch the translated institution name from label files and
     * append general info (not translated)
     *
     * @return Array
     */
    protected function getAutocompleterData()
    {
        $availableInstitutions = $this->getAvailableInstitutions();
        $data = [];
        $translator = $this->getServiceLocator()->get('VuFind\Translator');

        $i = 0;
        foreach ($availableInstitutions as $institutionCode => $additionalInfo) {
            $i++;
            $data[$i] = [
                    'value' => $institutionCode,
                    'label' => $translator->translate(
                        $institutionCode, 'institution'
                    ) . ' ' . $additionalInfo];
        }

        return $data;
    }

    /**
     * Get all available institutions
     *
     * @return Array
     */
    protected function getAvailableInstitutions()
    {
        return $this->getFavoriteDataSource()->getFavoriteInstitutions();
    }

    /**
     * Get institutions which are users favorite
     *
     * @return String[]
     */
    protected function getUserInstitutions()
    {
        return $this->getFavoriteManager()->getUserInstitutions();
    }

    /**
     * FavoriteManager
     *
     * @return FavoriteManager
     */
    protected function getFavoriteManager()
    {
        return $this->getServiceLocator()
            ->get('Swissbib\FavoriteInstitutions\Manager');
    }

    /**
     * FavoriteDataSource
     *
     * @return FavoriteDataSource
     */
    protected function getFavoriteDataSource()
    {
        return $this->getServiceLocator()
            ->get('Swissbib\FavoriteInstitutions\DataSource');
    }
}
