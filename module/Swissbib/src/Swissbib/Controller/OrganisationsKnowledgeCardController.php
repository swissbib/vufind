<?php
/**
 * OrganisationKnowledgeCardController.php
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

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\View\Model\ViewModel;

/**
 * Class OrganisationKnowledgeCardController
 *
 * Provides information to be rendered in knowledge cards (light-boxes).
 *
 * @category VuFind
 * @package  Swissbib\Controller
 * @author   Matthias Edel <matthias.edel@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.vufind.org  Main Page
 */
class OrganisationKnowledgeCardController extends AbstractPersonController
{
    /**
     * KnowledgeCardController constructor.
     *
     * @param ServiceLocatorInterface $sm The service locator
     */
    public function __construct(ServiceLocatorInterface $sm)
    {
        parent::__construct($sm);
        $this->config = $this->getConfig()->KnowledgeCard;
    }

    /**
     * /Page/Detail/Organisation/:id
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function organisationAction()
    {
        return parent::organisationAction();
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
        // Not used right now
    }
}
