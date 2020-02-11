<?php
/**
 * AbstractInstitutionController.php
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
 * @package  Swissbib\Controller
 * @author   Matthias Edel <matthias.edel@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.vufind.org  Main Page
 */
namespace Swissbib\Controller;

use Swissbib\Util\Config\FlatArrayConverter;
use Swissbib\Util\Config\ValueConverter;
use Zend\Config\Config as ZendConfig;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class AbstractInstitutionAction
 *
 * @category VuFind
 * @package  Swissbib\Controller
 * @author   Matthias Edel <matthias.edel@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.vufind.org  Main Page
 */
abstract class AbstractInstitutionController extends AbstractDetailsController
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
     * The institution action
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function institutionAction()
    {
        $info = $this->getInstitutionInfo();

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
                    "references" => $this->getInstitutionRecordReferencesConfig()
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
     * Provides an object with index, type and id for a institution.
     *
     * @return \stdClass
     */
    protected function getInstitutionInfo(): \stdClass
    {
        return (object)[
            'index' => 'lsb', 'type' => 'institution',
            'id' => $this->params()->fromRoute('id', [])
        ];
    }

    /**
     * Provides the record references configuration section.
     *
     * @return \Zend\Config\Config
     */
    protected function getInstitutionRecordReferencesConfig(): ZendConfig
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
            ->searchBibliographiResourcesOfInstitution($id, $searchSize);
    }
}