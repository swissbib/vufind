<?php
/**
 * OrganisationDetailPageController.php
 *
 * PHP Version 7
 *
 * Copyright (C) swissbib 2018
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License version 2,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.    See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category VuFind
 * @package  Swissbib\Controller
 * @author   Matthias Edel <matthias.edel@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.vufind.org  Main Page
 */
namespace Swissbib\Controller;

use ElasticSearch\VuFind\Search\ElasticSearch\Results;
use Zend\View\Model\ViewModel;

/**
 * Class OrganisationDetailPageController
 *
 * @category VuFind
 * @package  Swissbib\Controller
 * @author   Matthias Edel <matthias.edel@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.vufind.org  Main Page
 */
class OrganisationDetailPageController extends AbstractOrganisationController
{
    /**
     * /Page/Detail/Organisation/:id
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function organisationAction()
    {
        $viewModel = parent::organisationAction();
        $viewModel->setVariable(
            "references", $this->getRecordReferencesConfig()
        );

        return $viewModel;
    }

    /**
     * Adds additional data to view model
     *
     * @param ViewModel $viewModel The view model
     *
     * @return void
     */
    protected function addData(
        ViewModel &$viewModel
    ) {
        if (!isset($this->sameHierarchicalSuperiorOrganisationIds)) {
            $sameHierarchicalSuperiorOrganisations = '';
        }
        else {
            $sameHierarchicalSuperiorOrganisations = $this->getSameHierarchicalSuperiorOrganisations($this->sameHierarchicalSuperiorOrganisationIds);
        }

        if (isset($sameHierarchicalSuperiorOrganisations) && sizeOf($sameHierarchicalSuperiorOrganisations) > 0) {
            $viewModel->setVariable(
                "sameHierarchicalSuperiorOrganisations", $sameHierarchicalSuperiorOrganisations
            );
            $viewModel->setVariable(
                "sameHierarchicalSuperiorOrganisationsTotal", $this->sameHierarchicalSuperiorOrganisationsTotalCount
            );
        }
    }

}
