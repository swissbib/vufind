<?php
/**
 * AbstractOrganisationController.php
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
use Zend\View\Model\ViewModel;

/**
 * Class AbstractOrganisationAction
 *
 * @category VuFind
 * @package  Swissbib\Controller
 * @author   Matthias Edel <matthias.edel@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.vufind.org  Main Page
 */
abstract class AbstractOrganisationController extends AbstractDetailsController
{
    /*
     * @var ElasticSearch\VuFind\RecordDriver\ESOrganisation
     */
    protected  $driver;

    protected $sameHierarchicalSuperiorOrganisationIds;
    protected $sameHierarchicalSuperiorOrganisationsTotalCount;

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
     * The organisation action
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function organisationAction()
    {
        $info = $this->getOrganisationInfo();

        try {
            $this->driver = $this->getRecordDriver(
                $info->id, $info->index, $info->type
            );

            $superiorOrgIds = $this->driver->getHierarchicalSuperiorOrganisationIds();
            $viewModel = $this->createViewModel(
                [
                    "driver" => $this->driver,
                    "sameHierarchicalSuperiorOrganisations" => $superiorOrgIds,
                    "references" => $this->getOrganisationRecordReferencesConfig()
                ]
            );

            $this->addDataForCarousel($superiorOrgIds);

            $this->addData(
                $viewModel
            );

            return $viewModel;
        } catch (\Exception $e) {
            return $this->createErrorView($info->id, $e);
        }
    }

    /**
     * Provides an object with index, type and id for a organisation.
     *
     * @return \stdClass
     */
    protected function getOrganisationInfo(): \stdClass
    {
        return (object)[
            'index' => 'lsb', 'type' => 'organisation',
            'id' => $this->params()->fromRoute('id', [])
        ];
    }

    /**
     * Provides the record references configuration section.
     *
     * @return \Zend\Config\Config
     */
    protected function getOrganisationRecordReferencesConfig(): ZendConfig
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
     * Gets the sameHierarchicalSuperiorOrganisations
     *
     * @param string $id The id
     *
     * @return array
     */
    protected function getSameHierarchicalSuperiorOrganisations(string $ids)
    {
        $val =  $this->serviceLocator->get('elasticsearchsearch')
            ->searchElasticSearch(
                $ids,
                "sameHierarchicalSuperior_organisations",
                null,
                "organisation",
                $this->getConfig()->DetailPage->sameHierarchicalOrganisationsSize
            );
        $this->sameHierarchicalSuperiorOrganisationsTotalCount = $val->getResultTotal();
        return $val->getResults();
    }

    /**
     * Adds additional data
     *
     * @param ViewModel $viewModel necessary ids
     *
     * @return void
     */
    protected function addDataForCarousel(
        $sameHierarchicalSuperiorOrganisationIds
    ) {
        $this->sameHierarchicalSuperiorOrganisationIds = $sameHierarchicalSuperiorOrganisationIds;
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
