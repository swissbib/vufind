<?php
/**
 * AbstractPersonController.php
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

use Swissbib\Util\Config\FlatArrayConverter;
use Swissbib\Util\Config\ValueConverter;
use Zend\Config\Config as ZendConfig;
use Zend\ServiceManager\ServiceLocatorInterface;
use SwissbibRdfDataApi\VuFind\Service\RdfDataApiAdapter\Search\SearchTypeEnum;

/**
 * Class AbstractPersonAction
 *
 * @category VuFind
 * @package  Swissbib\Controller
 * @author   Christoph Boehm <cbo@outermedia.de>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.vufind.org  Main Page
 */
abstract class AbstractPersonController extends AbstractDetailsController
{
    protected $driver;
    protected $bibliographicResources;
    protected $subjects;

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
     * The person action
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function personAction()
    {
        $info = $this->getPersonInfo();

        try {
            $this->driver = $this->getRecordDriver(
                $info->id, $info->index, $info->type
            );
            $this->bibliographicResources = $this->getBibliographicResourcesOf(
                $info->id
            );

            $this->subjectIds = $this->getSubjectIdsFrom();
            $this->subjects = $this->getSubjectsOf();
            $viewModel = $this->createViewModel(
                [
                    "driver" => $this->driver, "subjects" => $this->subjects,
                    "books" => $this->bibliographicResources,
                    "references" => $this->getPersonRecordReferencesConfig()
                ]
            );

            $this->addData(
                $viewModel
            );

            return $viewModel;
        } catch (\Exception $e) {
            return $this->createErrorView($info->id, $e);
        }
    }

    /**
     * The person action
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function personActionApi()
    {
        $info = $this->getPersonInfoApi();

        try {
            $this->driver = $this->getRecordDriverApi(
                $info->id, $info->type
            );
            $this->bibliographicResources = $this->getBibliographicResourcesOfApi(
                $info->id
            );

            $this->subjectIds = $this->getSubjectIdsFrom();
            $this->subjects = $this->getSubjectsOfByApi();
            $viewModel = $this->createViewModel(
                [
                    "driver" => $this->driver, "subjects" => $this->subjects,
                    "books" => $this->bibliographicResources,
                    "references" => $this->getPersonRecordReferencesConfig()
                ]
            );

            $this->addDataApi(
                $viewModel
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
            'index' => 'lsb', 'type' => 'person',
            'id' => $this->params()->fromRoute('id', [])
        ];
    }

    /**
     * Provides an object with index, type and id for a person.
     *
     * @return \stdClass
     */
    protected function getPersonInfoApi(): \stdClass
    {
        return (object)[
            'type' => SearchTypeEnum::ID_SEARCH_PERSON,
            'id' => $this->params()->fromRoute('id', [])
            //'id' => 'd296a8ff-81e1-32a7-8cbb-316dd56df034'
        ];
    }



    /**
     * Provides the record references configuration section.
     *
     * @return \Zend\Config\Config
     */
    protected function getPersonRecordReferencesConfig(): ZendConfig
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
        return $this->serviceLocator->get('elasticsearchsearch')
            ->searchBibliographiResourcesOfPerson($id, $searchSize);
    }

    /**
     * Gets the  BibliographicResources
     *
     * @param string $id The id of the author
     *
     * @return array
     */
    protected function getBibliographicResourcesOfApi(string $id): array
    {
        $searchSize = $this->config->searchSize;
        $sl = $this->serviceLocator->get('swissbibrdfdataapi');
        //return $this->serviceLocator->get('elasticsearchsearch')
        //    ->searchBibliographiResourcesOfPerson($id, $searchSize);

        return $sl->searchBibliographiResourcesOfPerson($id, $searchSize);


    }


}
