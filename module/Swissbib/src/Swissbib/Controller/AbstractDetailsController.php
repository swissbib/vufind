<?php
/**
 * AbstractDetailsController.php
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
 * @package  Swissbib\Controller
 * @author   Christoph Boehm <cbo@outermedia.de>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.vufind.org  Main Page
 */
namespace Swissbib\Controller;

use ElasticSearch\VuFind\RecordDriver\ElasticSearch;
use VuFind\Controller\AbstractBase;
use Zend\View\Model\ViewModel;

/**
 * Class AbstractDetailsController
 *
 * @category VuFind
 * @package  Swissbib\Controller
 * @author   Christoph Boehm <cbo@outermedia.de>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.vufind.org  Main Page
 */
abstract class AbstractDetailsController extends AbstractBase
{
    /**
     * The person action
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function personAction()
    {
        $personIndex = "lsb";
        $personType = "person";
        $id = $this->params()->fromRoute('id', []);

        try {
            $driver = $this->getRecordDriver($id, $personIndex, $personType);

            $bibliographicResources = $this->getBibliographicResourcesOf($id);

            $subjectIds = $this->getSubjectIdsFrom($bibliographicResources);

            $subjects = $this->getSubjectsOf($subjectIds);

            $viewModel = $this->createViewModel(
                [
                    "driver" => $driver, "subjects" => $subjects,
                    "books" => $bibliographicResources
                ]
            );

            $this->addData(
                $viewModel, $id, $driver, $bibliographicResources, $subjectIds,
                $subjects
            );

            return $viewModel;
        } catch (\Exception $e) {
            return $this->createErrorView($id, $e);
        }
    }

    /**
     * The subject action
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function subjectAction()
    {
        $subjectIndex = "gnd";
        $subjectType = "DEFAULT";

        $id = "http://d-nb.info/gnd/" . $this->params()->fromRoute('id', []);

        try {
            $driver = $this->getRecordDriver($id, $subjectIndex, $subjectType);
            $subSubjects = $this->getSubSubjects($id);
            $parentSubjects = $this->getParentSubjects(
                $driver->getParentSubjects()
            );

            return $this->createViewModel(
                [
                    "driver" => $driver, "parents" => $parentSubjects,
                    "children" => $subSubjects
                ]
            );
        } catch (\Exception $e) {
            return $this->createErrorView($id, $e);
        }
    }

    /**
     * Adds additional data to view model
     *
     * @param ViewModel     $viewModel              The view model
     * @param string        $id                     The id
     * @param ElasticSearch $driver                 The record driver
     * @param array         $bibliographicResources The bibliographic resources
     * @param array         $subjectIds             The subject ids
     * @param array         $subjects               The subjects
     *
     * @return void
     */
    protected abstract function addData(
        ViewModel &$viewModel, string $id, ElasticSearch $driver,
        array $bibliographicResources, array $subjectIds, array $subjects
    );

    /**
     * Gets the record driver for id
     *
     * @param string $id    The id
     * @param string $index The index
     * @param string $type  The type
     *
     * @return ElasticSearch
     */
    protected function getRecordDriver($id, $index, $type): ElasticSearch
    {
        $content = $this->searchElasticSearch($id, "id", $index, $type);

        if ($content !== null && is_array($content) && count($content) === 1) {
            return array_pop($content);
        }
        throw new \Exception("Found no data for id " . $id);
    }

    /**
     * Gets the  BibliographicResources
     *
     * @param string $id The id of the author
     *
     * @return array
     */
    protected function getBibliographicResourcesOf(string $id): array
    {
        return $this->searchElasticSearch(
            "http://data.swissbib.ch/person/" . $id,
            "bibliographicResources_by_author", "lsb", "bibliographicResource"
        );
    }

    /**
     * Gets the Subjects of the bibliographic resources
     *
     * @param array $ids The subject ids
     *
     * @return array
     */
    protected function getSubjectsOf(array $ids): array
    {
        return $this->searchElasticSearch(
            $this->arrayToSearchString(array_unique($ids)), "id", "gnd", "DEFAULT"
        );
    }

    /**
     * Gets the SubSubjects
     *
     * @param string $id The id
     *
     * @return array
     */
    protected function getSubSubjects(string $id)
    {
        return $this->searchElasticSearch($id, "sub_subjects");
    }

    /**
     * Gets the ParentSubjects
     *
     * @param array $ids Array of ids
     *
     * @return array
     */
    protected function getParentSubjects(array $ids)
    {
        return $this->searchElasticSearch(
            $this->arrayToSearchString($ids), "id", "gnd", "DEFAULT"
        );
    }

    /**
     * Execute the search
     *
     * @param string $q        The query string
     * @param string $template The template
     * @param string $index    The index
     * @param string $type     The type
     *
     * @return array
     */
    protected function searchElasticSearch(
        string $q, string $template, string $index = null, string $type = null
    ): array {
        $manager = $this->serviceLocator->get(
            'VuFind\SearchResultsPluginManager'
        );
        // @var Results
        $results = $manager->get("ElasticSearch");

        // @var Params
        $params = $results->getParams();

        if (isset($index)) {
            $params->setIndex($index);
        }
        $params->setTemplate($template);

        // @var Query $query
        $query = $params->getQuery();
        if (isset($type)) {
            $query->setHandler($type);
        }
        $query->setString($q);

        $results->performAndProcessSearch();

        // @var $content array
        $content = $results->getResults();

        return $content ?? [];
    }

    /**
     * Creates ErrorView
     *
     * @param string     $id The id
     * @param \Exception $e  The exception
     *
     * @return \Zend\View\Model\ViewModel
     */
    protected function createErrorView(string $id, \Exception $e): ViewModel
    {
        $model = new ViewModel(
            [
                'message' => 'Can not find a Knowledge Card for id: ' . $id,
                'display_exceptions' => true,
                'exception' => $e
            ]
        );
        $model->setTemplate('error/index');
        return $model;
    }

    /**
     * Converts array to search string
     *
     * @param array $ids Array of ids
     *
     * @return string
     */
    protected function arrayToSearchString(array $ids): string
    {
        return '[' . implode(",", $ids) . ']';
    }

    /**
     * Gets the subject ids from the bibliographic resources
     *
     * @param array $bibliographicResources The bibliographic resources
     *
     * @return array
     */
    protected function getSubjectIdsFrom(array $bibliographicResources): array
    {
        $ids = [];
        // @var ESBibliographicResource $bibliographicResource
        foreach ($bibliographicResources as $bibliographicResource) {
            $subjects = $bibliographicResource->getSubjects();
            if (count($subjects) > 0) {
                $ids = array_merge($ids, $subjects);
            }
        }
        return $ids;
    }
}
