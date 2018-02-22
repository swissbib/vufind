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
use Swissbib\Util\Config\FlatArrayConverter;
use Swissbib\Util\Config\ValueConverter;
use VuFind\Controller\AbstractBase;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\View\Model\ViewModel;
use Zend\Config\Config as ZendConfig;

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
     * The config.
     *
     * @var \Zend\Config\Config $config The Config
     */
    protected $config;

    /**
     * AbstractDetailsController constructor.
     *
     * @param ServiceLocatorInterface $sm The service locator
     */
    public function __construct(ServiceLocatorInterface $sm)
    {
        parent::__construct($sm);
    }

    /**
     * The person action
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function personAction()
    {
        $info = $this->getPersonInfo();

        try {
            $driver = $this->getRecordDriver($info->id, $info->index, $info->type);

            $bibliographicResources = $this->getBibliographicResourcesOf($info->id);

            $subjectIds = $this->getSubjectIdsFrom($bibliographicResources);

            $subjects = $this->getSubjectsOf($subjectIds);

            $viewModel = $this->createViewModel(
                [
                    "driver" => $driver, "subjects" => $subjects,
                    "books" => $bibliographicResources
                ]
            );

            $this->addData(
                $viewModel, $info->id, $driver, $bibliographicResources,
                $subjectIds, $subjects
            );

            return $viewModel;
        } catch (\Exception $e) {
            return $this->createErrorView($info->id, $e);
        }
    }

    /**
     * Provides an object with index, type and id for a person.
     *
     * @return \stdClass
     */
    protected function getPersonInfo(): \stdClass
    {
        return (object)[
            'index' => 'lsb',
            'type' => 'person',
            'id' => $this->params()->fromRoute('id', [])
        ];
    }

    /**
     * The subject action
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function subjectAction()
    {
        $info = $this->getSubjectInfo();

        try {
            $driver = $this->getRecordDriver($info->id, $info->index, $info->type);
            $subSubjects = $this->getSubSubjects($info->id);
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
            return $this->createErrorView($info->id, $e);
        }
    }

    /**
     * Provides an object with index, type and id for a subject.
     *
     * @return \stdClass
     */
    protected function getSubjectInfo(): \stdClass
    {
        $prefix = 'http://d-nb.info/gnd/';

        return (object)[
            'index' => "gnd",
            'type' => "DEFAULT",
            'id' => $prefix . $this->params()->fromRoute('id', [])
        ];
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
    abstract protected function addData(
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
     *
     * @throws \Exception
     */
    protected function getRecordDriver($id, $index, $type): ElasticSearch
    {
        $content = $this->elasticsearchsearch()->searchElasticSearch(
            $id, "id", $index, $type, 1
        )->getResults();

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
        $searchSize = $this->config->searchSize;
        return $this->elasticsearchsearch()->searchElasticSearch(
            "http://data.swissbib.ch/person/" . $id,
            "bibliographicResources_by_author", "lsb", "bibliographicResource",
            $searchSize
        )->getResults();
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
        return $this->elasticsearchsearch()->searchElasticSearch(
            $this->arrayToSearchString(array_unique($ids)), "id", "gnd", "DEFAULT",
            $this->config->subjectsSize
        )->getResults();
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
        return $this->elasticsearchsearch()->searchElasticSearch(
            $id, "sub_subjects", $this->config->subjectsSize
        )->getResults();
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
        return $this->elasticsearchsearch()->searchElasticSearch(
            $this->arrayToSearchString($ids), "id", "gnd", "DEFAULT",
            $this->config->subjectsSize
        )->getResults();
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
                'message' => 'Can not find a Record for id: ' . $id,
                'display_exceptions' => APPLICATION_ENV === "development",
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

    /**
     * Provides the record references configuration section.
     *
     * @return \Zend\Config\Config
     */
    protected function getRecordReferencesConfig(): ZendConfig
    {
        $flatArrayConverter = new FlatArrayConverter();
        $valueConverter = new ValueConverter();

        $searchesConfig
            = $this->serviceLocator->get('VuFind\Config')->get('searches');
        $recordReferencesConfig = $flatArrayConverter->fromConfigSections(
            $searchesConfig, 'RecordReferences'
        );

        $recordReferencesConfig
            = $recordReferencesConfig->get('RecordReferences')->toArray();

        return $valueConverter->convert(
            new ZendConfig($recordReferencesConfig)
        );
    }
}
