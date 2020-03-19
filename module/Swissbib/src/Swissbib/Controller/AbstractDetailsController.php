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
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
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
use Zend\Config\Config as ZendConfig;
use Zend\ServiceManager\ServiceLocatorInterface;
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
    protected $subSubjects;
    protected $parentSubjects;
    protected $subjectIds;
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
     * Adds additional data to view model
     *
     * @param ViewModel $viewModel The view model
     *
     * @return void
     */
    abstract protected function addData(
        ViewModel &$viewModel
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
        $content = $this->serviceLocator->get('elasticsearchsearch')
            ->searchElasticSearch(
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
     * @param string $id The id
     *
     * @return array
     */
    abstract protected function getBibliographicResourcesOf(string $id): array;

    /**
     * Gets the Subjects of the bibliographic resources
     *
     * @return array
     */
    protected function getSubjectsOf(): array
    {
        if (count(array_unique($this->subjectIds)) > 0) {
            return $this->serviceLocator->get('elasticsearchsearch')
                ->searchElasticSearch(
                    $this->arrayToSearchString(array_unique($this->subjectIds)),
                    "id",
                    null,
                    "subject", $this->config->subjectsSize
                )->getResults();
        } else {
            return [];
        }
    }

    /**
     * Gets the subject ids from the bibliographic resources
     *
     * @return array
     */
    protected function getSubjectIdsFrom(): array
    {
        $ids = [];
        // @var ESBibliographicResource $bibliographicResource
        foreach ($this->bibliographicResources as $bibliographicResource) {
            $subjects = $bibliographicResource->getSubjects();
            if (count($subjects) > 0) {
                $ids = array_merge($ids, $subjects);
            }
        }
        return $ids;
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
        //remove ids equal to ""
        $ids = array_filter(
            $ids,
            function ($a) {
                return $a !== "";
            }
        );
        return '[' . implode(",", $ids) . ']';
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
