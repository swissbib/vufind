<?php
/**
 * Swissbib SearchController
 *
 * PHP version 7
 *
 * Copyright (C) project swissbib, University Library Basel, Switzerland
 * http://www.swissbib.org  / http://www.swissbib.ch / http://www.ub.unibas.ch
 *
 * Date: 1/2/13
 * Time: 4:09 PM
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License version 2,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category Swissbib_VuFind
 * @package  Controller
 * @author   Guenter Hipler  <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.swissbib.org
 */
namespace Swissbib\Controller;

use Laminas\Config\Config;
use Laminas\Http\PhpEnvironment\Response;
use Laminas\Stdlib\Parameters;
use Laminas\View\Model\ViewModel;

use Swissbib\RecordDriver\SolrMarc;
use Swissbib\VuFind\Search\Results\PluginManager
    as SwissbibSearchResultsPluginManager;

use VuFind\Controller\SearchController as VuFindSearchController;
use VuFind\Search\Results\PluginManager as VuFindSearchResultsPluginManager;

/**
 * Swissbib SearchController
 *
 * @category Swissbib_VuFind
 * @package  Controller
 * @author   Guenter Hipler  <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org
 */
class SearchController extends VuFindSearchController
{
    /**
     * Search targets extended by swissbib
     *
     * @var String[]
     */
    protected $extendedTargets;

    /**
     * Get model for general results view (all tabs, content of active tab only)
     *
     * @return \Laminas\View\Model\ViewModel
     */
    public function resultsAction()
    {
        $resultsFacetConfig = $this->getFacetConfig();
        //do not remember FRBR searches because we ant
        // to jump back to the original search
        $type = $this->params()->fromQuery('type');

        if (!empty($type) && $type == "FRBR") {
            $this->rememberSearch = false;
        }

        $resultViewModel = parent::resultsAction();

        if ($resultViewModel instanceof Response) {
            return $resultViewModel;
        }

        $this->layout()->setVariable(
            'resultViewParams', $resultViewModel->getVariable('params')
        );
        $resultViewModel->setVariable('facetsConfig', $resultsFacetConfig);
        $resultViewModel->setVariable('htmlLayoutClass', 'resultView');

        return $resultViewModel;
    }

    /**
     * Render advanced search
     *
     * @return ViewModel
     */
    public function advancedAction()
    {
        $viewModel = parent::advancedAction();
        $viewModel->options = $this->serviceLocator
            ->get('VuFind\Search\Options\PluginManager')->get($this->searchClassId);
        $results = $this->getResultsManager()->get($this->searchClassId);

        $params = $results->getParams();
        $requestParams = new Parameters(
            $this->getRequest()->getQuery()->toArray()
            + $this->getRequest()->getPost()->toArray()
        );

        //GH: We need this initialization only to handle personal
        // limit an sort settings for logged in users
        $params->initLimitAdvancedSearch($requestParams);
        $viewModel->setVariable('params', $params);

        return $viewModel;
    }

    /**
     * Returns availability by library network
     *
     * @return \Laminas\Stdlib\ResponseInterface
     */
    public function availabilityByLibraryNetworkAction()
    {
        $idRecord = $this->params()->fromRoute('record');
        $record = $this->getRecord($idRecord);
        $availabilities = [];

        if (empty($record->getField('949', ['B','F','b','c','j']))) {
            $field898a = $record->getField('898', ['a']);
            $field898a = $field898a[0];
            if (substr_compare($field898a, '53', -strlen('53')) == 0) {
                // all institutions of the record get the 'available' flag:
                $institutions = $record->getInstitutions(true);
                foreach ($institutions as $institution) {
                    $availabilities = array_merge(
                        $availabilities,
                        [$institution['institution'] => '0']
                    );
                }
            } else {
                $doAlephRequest
                    = substr_compare(
                        $field898a, 'CR02', 0, strlen('CR02')
                    ) === 0 ||
                    substr_compare(
                        $field898a, 'CR0300', 0, strlen('CR0300')
                    ) === 0 ||
                    substr_compare(
                        $field898a, 'CR0301', 0, strlen('CR0301')
                    ) === 0 ||
                    substr_compare(
                        $field898a, 'CR0302', 0, strlen('CR0302')
                    ) === 0 ||
                    substr_compare(
                        $field898a, 'CR0303', 0, strlen('CR0303')
                    ) === 0 ||
                    substr_compare(
                        $field898a, 'CR0304', 0, strlen('CR0304')
                    ) === 0 ||
                    substr_compare(
                        $field898a, 'CR0305', 0, strlen('CR0305')
                    ) === 0 ||
                    substr_compare(
                        $field898a, 'CR0306', 0, strlen('CR0306')
                    ) === 0 ||
                    substr_compare(
                        $field898a, 'CR0307', 0, strlen('CR0307')
                    ) === 0 ||
                    substr_compare(
                        $field898a, 'CR0308', 0, strlen('CR0308')
                    ) === 0;
                if ($doAlephRequest) {
                    $availabilities = $this->doAlephAvailabilityRequest($record);
                }
            }
        } else {
            $availabilities = $this->doAlephAvailabilityRequest($record);
        }

        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent(json_encode($availabilities));
        return $response;
    }

    /**
     * Do Aleph Availability Request
     *
     * @param SolrMarc $record the solrMarc record
     *
     * @return array
     */
    protected function doAlephAvailabilityRequest(SolrMarc $record)
    {
        $availabilities = [];
        $alwaysAvailableGroups = [
            'RETROS',
            'BORIS',
            'EDOC',
            'ECOD',
            'ALEXREPO',
            'NATIONALLICENCE',
            'FREE',
            'SERSOL'
        ];
        $all035Idls = $record->getAll035Idls();
        $recordHasAlwaysAvailableGroup = false;
        foreach ($all035Idls as $index => $one035array) {
            $idls = $one035array['idls'];
            $sysNr = $one035array['sysNr'];
            if (!in_array($idls, $alwaysAvailableGroups)) {
                $availabilities = array_merge(
                    $availabilities,
                    $record->getAvailabilityIconFromServer($idls, $sysNr)
                );
            } else {
                $recordHasAlwaysAvailableGroup = true;
            }
        }

        // all institutions which don't have an availability yet
        // are treated as members of the $alwaysAvailableGroups:
        $institutions = $record->getInstitutions(true);
        $institutions = array_merge($institutions, $record->get949b());
        foreach ($institutions as $institution) {
            if (!array_key_exists($institution['institution'], $availabilities)
                && $recordHasAlwaysAvailableGroup
            ) {
                $availabilities = array_merge(
                    $availabilities,
                    [$institution['institution'] => '0']
                );
            }
        }
        return $availabilities;
    }

    /**
     * Load solr record
     *
     * @param Integer $idRecord record id
     *
     * @return SolrMarc
     */
    protected function getRecord($idRecord)
    {
        return $this->serviceLocator->get('VuFind\RecordLoader')
            ->load($idRecord, 'Solr');
    }

    /**
     * Get facet config
     *
     * @return Config
     */
    protected function getFacetConfig()
    {
        return $this->serviceLocator->get('VuFind\Config\PluginManager')
            ->get('facets')->get('Results_Settings');
    }

    /**
     * Get results manager
     * If target is extended, get a customized manager
     *
     * @return VuFindSearchResultsPluginManager|SwissbibSearchResultsPluginManager
     */
    protected function getResultsManager()
    {
        if (!isset($this->extendedTargets)) {
            $mainConfig = $this->serviceLocator->get('VuFind\Config\PluginManager')
                ->get('config');
            $extendedTargetsSearchClassList
                = $mainConfig->SwissbibSearchExtensions->extendedTargets;

            $this->extendedTargets = array_map(
                'trim', explode(',', $extendedTargetsSearchClassList)
            );
        }

        if (in_array($this->searchClassId, $this->extendedTargets)) {
            return $this->serviceLocator
                ->get('VuFind\Search\Results\PluginManager');
        }

        return parent::getResultsManager();
    }
}
