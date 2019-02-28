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

use Swissbib\VuFind\Search\Results\PluginManager
    as SwissbibSearchResultsPluginManager;
use VuFind\Controller\SearchController as VuFindSearchController;
use VuFind\Search\Results\PluginManager as VuFindSearchResultsPluginManager;

use Zend\Config\Config;
use Zend\Http\PhpEnvironment\Response;

use Zend\Stdlib\Parameters;
use Zend\View\Model\ViewModel;

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
     * @return \Zend\View\Model\ViewModel
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
     * @return \Zend\Stdlib\ResponseInterface
     */
    public function availabilityByLibraryNetworkAction()
    {
        $idRecord = $this->params()->fromRoute('record');
        $record = $this->getRecord($idRecord);
        $institutions = $record->getInstitutions(true);
        $institutions = array_merge($institutions, $record->get949b());
        $availabilities = $groups = [];
        foreach ($institutions as $institution) {
            if (!in_array($institution['group'], $groups)) {
                $institutionCode = $institution['institution'];
                $all035Idls = $record->getAll035Idls();
                foreach ($all035Idls as $idls => $sysNr) {
                    switch ($idls) {
                    case 'RETROS':
                    case 'BORIS':
                    case 'EDOC':
                    case 'ECOD':
                    case 'ALEXREPO':
                    case 'NATIONALLICENCE':
                    case 'FREE':
                    case 'SERSOL':
                        $availabilities = array_merge(
                            $availabilities,
                            [$institutionCode => '0']
                        );
                        break;
                    default:
                        if ($institution['group'] !== $idls) {
                            continue;
                        }
                        array_push($groups, $institution['group']);
                        $availabilities = array_merge(
                            $availabilities,
                            $record->getAvailabilityIconFromServer($idls, $sysNr)
                        );
                        break;
                    }
                }
            }
        }
        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent(json_encode($availabilities));
        return $response;
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
