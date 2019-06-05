<?php
/**
 * AbstractSubjectController.php
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
 * @author   Christoph Boehm <cbo@outermedia.de>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.vufind.org  Main Page
 */
namespace Swissbib\Controller;

use SwissbibRdfDataApi\VuFind\Service\RdfDataApiAdapter\Search\SearchTypeEnum;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class AbstractPersonAction
 *
 * @category VuFind
 * @package  Swissbib\Controller
 * @author   Christoph Boehm <cbo@outermedia.de>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.vufind.org  Main Page
 */
abstract class AbstractSubjectController extends AbstractDetailsController
{
    /**
     * The subject record driver
     *
     * @var \ElasticSearch\VuFind\RecordDriver\ESSubject
     */
    protected $driver;
    protected $bibliographicResources;

    /**
     * DetailPageController constructor.
     *
     * @param \Zend\ServiceManager\ServiceLocatorInterface $sm Service locator
     */
    public function __construct(ServiceLocatorInterface $sm)
    {
        parent::__construct($sm);
        $this->config = $this->getConfig()->DetailPage;
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
            $this->driver = $this->getRecordDriver(
                $info->id, $info->index, $info->type
            );
            $this->subSubjects = $this->getSubSubjects($info->id);
            $this->parentSubjects = $this->getParentSubjects(
                $this->driver->getParentSubjects()
            );

            return $this->createViewModel(
                [
                    "driver" => $this->driver, "parents" => $this->parentSubjects,
                    "children" => $this->subSubjects
                ]
            );
        } catch (\Exception $e) {
            return $this->createErrorView($info->id, $e);
        }
    }


    /**
     * The subject action
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function subjectActionApi()
    {
        $info = $this->getSubjectInfoApi();

        try {
            $this->driver = $this->getRecordDriverApi(
                $info->id, $info->type
            );

            //$this->subSubjects = $this->getSubSubjectsApi($info->id);
            $this->subSubjects = $this->getSubSubjectsApi($this->driver->getName());
            //$this->parentSubjects = $this->getParentSubjectsApi(
            //    $this->driver->getParentSubjectsApi()
            //);
            $ps = $this->driver->getParentSubjectsApi();
            $this->parentSubjects = count($ps) == 0 ? [] :
                $this->getParentSubjectsApi($ps);

            return $this->createViewModel(
                [
                    "driver" => $this->driver, "parents" => $this->parentSubjects,
                    "children" => $this->subSubjects
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
            'index' => "gnd", 'type' => "DEFAULT",
            'id' => $prefix . $this->params()->fromRoute('id', [])
        ];
    }

    /**
     * Provides an object with index, type and id for a person.
     *
     * @return \stdClass
     */
    protected function getSubjectInfoApi(): \stdClass
    {
        return (object)[
            'type' => SearchTypeEnum::ID_SEARCH_GND,
            'id' => $this->params()->fromRoute('id', [])
            //'id' => 'd296a8ff-81e1-32a7-8cbb-316dd56df034'
        ];
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
        return $this->serviceLocator->get('elasticsearchsearch')
            ->searchElasticSearch(
                $id, "sub_subjects", $this->config->subjectsSize
            )->getResults();
    }


    /**
     * Gets the SubSubjects
     *
     * @param string $id The id
     *
     * @return array
     */
    protected function getSubSubjectsApi(string $id)
    {
        return $this->serviceLocator->get('swissbibrdfdataapi')
            ->searchApiSearch(
                $id, SearchTypeEnum::SUB_SUBJECTS, $this->config->subjectsSize
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
        return $this->serviceLocator->get('elasticsearchsearch')
            ->searchElasticSearch(
                $this->arrayToSearchString($ids),
                "id",
                "gnd",
                "DEFAULT",
                $this->config->subjectsSize
            )->getResults();
    }

    /**
     * Gets the ParentSubjects
     *
     * @param array $ids Array of ids
     *
     * @return array
     */
    protected function getParentSubjectsApi(array $ids)
    {
        return $this->serviceLocator->get('swissbibrdfdataapi')
            ->searchElasticSearch(
                $this->arrayToSearchString($ids),
                "id",
                "gnd",
                "DEFAULT",
                $this->config->subjectsSize
            )->getResults();
    }




    /**
     * Gets the  BibliographicResources
     *
     * @param string $id The id of the subject
     *
     * @return array
     */
    protected function getBibliographicResourcesOf(string $id): array
    {
        $searchSize = $this->config->searchSize;
        return $this->serviceLocator->get('elasticsearchsearch')
            ->searchBibliographiResourcesOfSubject($id, $searchSize);
    }

    /**
     * Gets the  BibliographicResources
     *
     * @param string $id The id of the subject
     *
     * @return array
     */
    protected function getBibliographicResourcesOfApi(string $id): array
    {
        $searchSize = $this->config->searchSize;
        return $this->serviceLocator->get('swissbibrdfdataapi')
            ->searchBibliographiResourcesOfSubject($id, $searchSize);
    }


}
