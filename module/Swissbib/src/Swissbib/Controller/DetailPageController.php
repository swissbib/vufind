<?php
/**
 * DetailPageController.php
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

use ElasticSearch\VuFind\RecordDriver\ESPerson;
use ElasticSearch\VuFind\RecordDriver\ESSubject;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\View\Model\ViewModel;

/**
 * Class DetailPageController
 *
 * @category VuFind
 * @package  Swissbib\Controller
 * @author   Christoph Boehm <cbo@outermedia.de>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.vufind.org  Main Page
 */
class DetailPageController extends AbstractDetailsController
{
    /**
     * The config for the detail page
     *
     * @var \Zend\Config\Config $_config The Config
     */
    private $_config;

    /**
     * DetailPageController constructor.
     *
     * @param \Zend\ServiceManager\ServiceLocatorInterface $sm Service locator
     */
    public function __construct(ServiceLocatorInterface $sm)
    {
        parent::__construct($sm);
        $this->_config = $this->serviceLocator->get('VuFind\Config')->get(
            'config'
        )->DetailPage;
    }

    /**
     * TODO Improve error handling
     * /Page/Detail/Person/:id
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function personAction()
    {
        $viewModel = parent::personAction();

        $this->addMedia($viewModel, "Author");
        $this->addCoContributors($viewModel);
        $this->addPersonsOfSameGenre($viewModel);
        $this->addPersonsOfSameMovement($viewModel);

        return $viewModel;
    }

    /**
     * TODO Improve error handling
     * /Page/Detail/Subject/:id
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function subjectAction()
    {
        $viewModel = parent::subjectAction();

        $this->addMedia($viewModel, "Subject");

        return $viewModel;
    }

    /**
     * Retrieves list of media by query
     *
     * @param string $query The author
     * @param string $type  The type
     *
     * @return mixed
     */
    public function searchSolr(string $query, string $type): array
    {
        // Set up the search:
        $searchClassId = "Solr";

        // @var \Swissbib\VuFind\Search\Solr\Results $results
        $results = $this->getResultsManager()->get($searchClassId);

        // @var \Swissbib\VuFind\Search\Solr\Params $params
        $params = $results->getParams();
        $params->setBasicSearch($query, $type);
        $params->setLimit($this->_config->mediaLimit);

        // Attempt to perform the search; if there is a problem, inspect any Solr
        // exceptions to see if we should communicate to the user about them.
        try {
            // Explicitly execute search within controller -- this allows us to
            // catch exceptions more reliably:
            $results->performAndProcessSearch();
        } catch (\VuFindSearch\Backend\Exception\BackendException $e) {
            if ($e->hasTag('VuFind\Search\ParserError')) {
                // We need to create and process an "empty results" object to
                // ensure that recommendation modules and templates behave
                // properly when displaying the error message.
                $results = $this->getResultsManager()->get('EmptySet');
                $results->setParams($params);
                $results->performAndProcessSearch();
            } else {
                throw $e;
            }
        }

        return $results->getResults();
    }

    /**
     * Gets subjects
     *
     * @param array $subjectIds Ids of subjects
     *
     * @return array
     */
    protected function getSubjectsOf(array $subjectIds): array
    {
        $subjects = parent::getSubjectsOf($subjectIds);

        return $this->getTagCloud($subjectIds, $subjects);
    }

    /**
     * Gets the Tagcloud
     *
     * @param array $subjectIds All subject ids, including duplicates
     * @param array $subjects   All subjects
     *
     * @return array
     */
    protected function getTagCloud(array $subjectIds, array $subjects)
    {
        $counts = array_count_values($subjectIds);
        $cloud = [];
        $max = max($counts);

        foreach ($counts as $id => $count) {
            $filtered = array_filter(
                $subjects,
                function (ESSubject $item) use ($id) {
                    return $item->getFullUniqueID() === $id;
                }
            );
            if (count($filtered) > 0) {
                // @var ESSubject $subject
                $subject = array_shift($filtered);
                $name = $subject->getName();
                $cloud[$name] = [
                    "subject" => $subject, "count" => $count,
                    "weight" => $this->calculateFontSize($count, $max)
                ];
            }
        }

        return $cloud;
    }

    /**
     * Adds media of author to ViewModel
     *
     * @param \Zend\View\Model\ViewModel $viewModel The view model
     * @param string                     $type      The type (Author or Subject)
     *
     * @return void
     */
    protected function addMedia(ViewModel &$viewModel, string $type): void
    {
        $record = $viewModel->getVariable("driver");
        $name = $record->getName();
        $results = $this->searchSolr($name, $type);
        $viewModel->setVariable("media", $results);
    }

    /**
     * Adds co contributors of author
     *
     * @param \Zend\View\Model\ViewModel $viewModel The view model
     *
     * @return void
     */
    protected function addCoContributors(ViewModel &$viewModel)
    {
        // @var ESPerson $driver
        $driver = $viewModel->getVariable("driver");
        $authorId = $driver->getUniqueId();
        $bibliographicResources = $viewModel->getVariable("books");
        $contributorIds = [];
        // @var \ElasticSearch\VuFind\RecordDriver\ESBibliographicResource
        // $bibliographicResource
        foreach ($bibliographicResources as $bibliographicResource) {
            $contributorIds = array_merge(
                $contributorIds, $bibliographicResource->getContributors()
            );
        }
        $contributorIds = array_unique($contributorIds);

        $contributorIds = array_filter(
            $contributorIds,
            function ($val) use ($authorId) {
                return $val !== $authorId;
            }
        );

        $contributors = $this->search(
            $this->arrayToSearchString($contributorIds), "id", "lsb", "person"
        );

        $viewModel->setVariable("coContributors", $contributors);
    }

    /**
     * Adds persons of same genre as author
     *
     * @param \Zend\View\Model\ViewModel $viewModel The view model
     *
     * @return void
     */
    protected function addPersonsOfSameGenre($viewModel)
    {
        // @var ESPerson $driver
        $driver = $viewModel->getVariable("driver");
        $genres = $driver->getGenre();

        if (is_array($genres)) {
            $genres = $this->arrayToSearchString($genres);
        }

        if (isset($genres)) {
            $authors = $this->search($genres, "person_by_genre");
            $viewModel->setVariable("authorsOfSameGenre", $authors);
        }
    }

    /**
     * Adds persons of same movement as author
     *
     * @param \Zend\View\Model\ViewModel $viewModel The view model
     *
     * @return void
     */
    protected function addPersonsOfSameMovement($viewModel)
    {
        // @var ESPerson $driver
        $driver = $viewModel->getVariable("driver");
        $authorId = $driver->getUniqueId();

        $movements = $driver->getMovement();

        if (is_array($movements)) {
            $movements = $this->arrayToSearchString($movements);
        }

        if (isset($movements)) {
            $authors = $this->search($movements, "person_by_movement");
            $authors = array_filter(
                $authors,
                function (ESPerson $author) use ($authorId) {
                    return $author->getUniqueID() !== $authorId;
                }
            );
            $viewModel->setVariable("authorsOfSameGenre", $authors);
        }
    }

    /**
     * Calculates the font size for the tag cloud
     *
     * @param int $count The count
     * @param int $max   Max count
     *
     * @return int
     */
    protected function calculateFontSize($count, $max): int
    {
        $tagCloudMaxFontSize = $this->_config->tagCloudMaxFontSize;
        $tagCloudMinFontSize = $this->_config->tagCloudMinFontSize;
        return round(
            ($tagCloudMaxFontSize - $tagCloudMinFontSize) * ($count / $max)
            + $tagCloudMinFontSize
        );
    }

    /**
     * Convenience method for accessing results
     *
     * @return \VuFind\Search\Results\PluginManager
     */
    protected function getResultsManager()
    {
        return $this->serviceLocator->get('VuFind\SearchResultsPluginManager');
    }
}
