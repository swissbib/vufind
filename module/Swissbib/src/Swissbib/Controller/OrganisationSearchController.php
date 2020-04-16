<?php
/**
 * OrganisationSearchController.php
 *
 * PHP Version 7
 *
 * Copyright (C) swissbib 2020
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
 * @package  Controller
 * @author   Matthias Edel <matthias.edel@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.vufind.org  Main Page
 */
namespace Swissbib\Controller;

use VuFind\Controller\AbstractBase;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\View\Model\ViewModel;

/**
 * Class OrganisationSearchController
 *
 * @category VuFind
 * @package  Swissbib\Controller
 * @author   Matthias Edel <matthias.edel@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.vufind.org  Main Page
 */
class OrganisationSearchController extends AbstractOrganisationController
{
    /**
     * OrganisationSearchController constructor.
     *
     * @param ServiceLocatorInterface $sm The service locator
     */
    public function __construct(ServiceLocatorInterface $sm)
    {
        parent::__construct($sm);
        //$this->config = $this->getConfig()->PersonSearch;
    }

    /**
     * The action for hierarchicalSuperiors
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function hierarchicalSuperiorsAction()
    {
        $info = $this->getOrganisationInfo();
        $id = $this->params()->fromRoute('id', []);
        $movement = $this->getRequest()->getQuery()['id'] ?? "";
        $page = $this->getRequest()->getQuery()['page'] ?? 1;
        $limit = $this->getRequest()->getQuery()['limit'] ?? 20;

        $this->driver = $this->getRecordDriver(
            $info->id, $info->index, $info->type
        );
        $superiorOrgIds = $this->driver->getHierarchicalSuperiorOrganisationIds();
        $orgs = $this->serviceLocator->get('elasticsearchsearch')
            ->searchElasticSearch(
                $superiorOrgIds, "sameHierarchicalSuperior_organisations", null, null, $limit, $page
            );

        return $this->createViewModel(["results" => $orgs]);
    }

}
