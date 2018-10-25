<?php
/**
 * PersonSearchController.php
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
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA    02111-1307    USA
 *
 * @category VuFind
 * @package  Controller
 * @author   Christoph Boehm <cbo@outermedia.de>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.vufind.org  Main Page
 */
namespace Swissbib\Controller;

use VuFind\Controller\AbstractBase;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class PersonSearchController
 *
 * @category VuFind
 * @package  Swissbib\Controller
 * @author   Christoph Boehm <cbo@outermedia.de>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.vufind.org  Main Page
 */
class PersonSearchController extends AbstractBase
{
    /**
     * PersonSearchController constructor.
     *
     * @param ServiceLocatorInterface $sm The service locator
     */
    public function __construct(ServiceLocatorInterface $sm)
    {
        parent::__construct($sm);
        $this->config = $this->getConfig()->PersonSearch;
    }

    /**
     * The action for co authors
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function coAuthorAction()
    {
        $id = $this->getRequest()->getQuery()['lookfor'] ?? "";
        $page = $this->getRequest()->getQuery()['page'] ?? 1;
        $limit = $this->getRequest()->getQuery()['limit'] ?? 20;

        $authors = $this->serviceLocator->get('elasticsearchsearch')->searchCoContributorsOfPerson(
            $id, $limit, $this->config->searchSize ?? 100, $page
        );

        return $this->createViewModel(["results" => $authors]);
    }

    /**
     * The action for same genre authors
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function sameGenreAction()
    {
        $genre = $this->getRequest()->getQuery()['lookfor'] ?? "";
        $page = $this->getRequest()->getQuery()['page'] ?? 1;
        $limit = $this->getRequest()->getQuery()['limit'] ?? 20;

        $authors = $this->serviceLocator->get('elasticsearchsearch')->searchElasticSearch(
            $genre, "person_by_genre", null, null, $limit, $page
        );

        return $this->createViewModel(["results" => $authors]);
    }

    /**
     * The action for same movement authors
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function sameMovementAction()
    {
        $movement = $this->getRequest()->getQuery()['lookfor'] ?? "";
        $page = $this->getRequest()->getQuery()['page'] ?? 1;
        $limit = $this->getRequest()->getQuery()['limit'] ?? 20;

        $authors = $this->serviceLocator->get('elasticsearchsearch')->searchElasticSearch(
            $movement, "person_by_movement", null, null, $limit, $page
        );

        return $this->createViewModel(["results" => $authors]);
    }

    /**
     * The action for same subject authors
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function subjectAction()
    {
        $id = $this->getRequest()->getQuery()['lookfor'] ?? "";
        $page = $this->getRequest()->getQuery()['page'] ?? 1;
        $limit = $this->getRequest()->getQuery()['limit'] ?? 20;

        $authors = $this->serviceLocator->get('elasticsearchsearch')->searchContributorsOfSubject(
            $id, $limit, $this->config->searchSize ?? 100, $page
        );

        return $this->createViewModel(["results" => $authors]);
    }
}
