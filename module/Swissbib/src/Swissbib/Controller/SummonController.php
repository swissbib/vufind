<?php
/**
 * Swissbib SummonController
 *
 * PHP version 5
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
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @category Swissbib_VuFind2
 * @package  Controller
 * @author   Guenter Hipler  <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.swissbib.org
 */
namespace Swissbib\Controller;

use Zend\Http\PhpEnvironment\Response;

use VuFind\Controller\SummonController as VuFindSummonController;
use Zend\Stdlib\Parameters;

/**
 * Swissbib SummonController
 *
 * @category Swissbib_VuFind2
 * @package  Controller
 * @author   Guenter Hipler  <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org
 */
class SummonController extends VuFindSummonController
{
    /**
     * Return a Search Results object containing advanced facet information.  This
     * data may come from the cache.
     *
     * @return \VuFind\Search\Summon\Results
     */
    protected function getAdvancedFacets()
    {
        // Check if we have facet results cached, and build them if we don't.
        $cache = $this->getServiceLocator()->get('VuFind\CacheManager')
            ->getCache('object');

        /**
         * Loads the Summon Results object. This is necessary because otherwise
         * it would fail to load the object from cache.
         */
        $loadResults = $this->getResultsManager()->get('Summon');
        $loadParams  = $loadResults->getParams();
        $loadParams->getOptions();

        if (!($results = $cache->getItem('summonSearchAdvancedFacets'))) {
            $results = $this->getResultsManager()->get('Summon');
            $params  = $results->getParams();
            $params->addFacet('Language,or,1,20');
            $params->addFacet('ContentType,or,1,20', 'Format');

            // We only care about facet lists, so don't get any results:
            $params->setLimit(0);

            // force processing for cache
            $results->getResults();

            $cache->setItem('summonSearchAdvancedFacets', $results);
        }

        // Restore the real service locator to the object (it was lost during
        // serialization):
        $results->restoreServiceLocator($this->getServiceLocator());

        return $results;
    }

    /**
     * HomePageFacets
     *
     * @return void|\VuFind\Search\Summon\Results
     */
    protected function getHomePageFacets()
    {
        return $this->getFacetResults(
            'initHomePageFacets', 'summonSearchHomeFacets'
        );
    }

    /**
     * Return a Search Results object containing requested facet information.  This
     * data may come from the cache.
     *
     * @param string $initMethod Name of params method to use to request facets
     * @param string $cacheName  Cache key for facet data
     *
     * @return \VuFind\Search\Summon\Results
     */
    protected function getFacetResults($initMethod, $cacheName)
    {
        // Check if we have facet results cached, and build them if we don't.
        $cache = $this->getServiceLocator()->get('VuFind\CacheManager')
            ->getCache('object');

        /**
         * Loads the Summon Results object. This is necessary
         * because otherwise it would fail to load the object from cache.
         */
        $loadResults = $this->getResultsManager()->get('Summon');
        $loadParams  = $loadResults->getParams();
        $loadParams->getOptions();

        if (!($results = $cache->getItem($cacheName))) {
            // Use advanced facet settings to get summary facets on the front page;
            // we may want to make this more flexible later.  Also keep in mind that
            // the template is currently looking for certain hard-coded fields; this
            // should also be made smarter.
            $results = $this->getResultsManager()->get('Summon');
            $params = $results->getParams();
            $params->$initMethod();

            // We only care about facet lists, so don't get any results (this helps
            // prevent problems with serialized File_MARC objects in the cache):
            $params->setLimit(0);

            $results->getResults();                     // force processing for cache

            $cache->setItem($cacheName, $results);
        }

        // Restore the real service locator to the object (it was lost during
        // serialization):
        $results->restoreServiceLocator($this->getServiceLocator());

        return $results;
    }

    /**
     * Checks if client IP is inside Basel / Berne universities (configurable)
     * used to bring information to the view
     * functionality from sbvf2 (mid august 2014)
     *
     * @return boolean
     */
    protected function isRestrictedTarget()
    {
        // check if client is inside Basel / Berne universities
        $targetsProxy = $this->serviceLocator
            ->get('Swissbib\TargetsProxy\TargetsProxy');
        $external = $targetsProxy->detectTarget() === false ? true : false;

        return $external;
    }

    /**
     * Render advanced search
     *
     * @return ViewModel
     */
    public function advancedAction()
    {
        $viewModel              = parent::advancedAction();

        //GH: We need this initialization only to handle personal limit
        // an sort settings for logged in users
        $viewModel->options = $this->getServiceLocator()
            ->get('VuFind\SearchOptionsPluginManager')->get($this->searchClassId);
        $results = $this->getResultsManager()->get($this->searchClassId);
        $params = $results->getParams();
        $requestParams = new Parameters(
            $this->getRequest()->getQuery()->toArray()
            + $this->getRequest()->getPost()->toArray()
        );

        $params->initLimitAdvancedSearch($requestParams);
        $viewModel->setVariable('params', $params);

        return $viewModel;
    }

    /**
     * Results action
     *
     * @throws \Exception
     * @throws \VuFindSearch\Backend\Exception\BackendException
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function resultsAction()
    {
        $viewModel = parent::resultsAction();

        if ($viewModel instanceof Response) {
            return $viewModel;
        }

        $defaultTopRecommend = $this->getServiceLocator()->get('VuFind\Config')
            ->get('Summon')->get('General')->get('default_top_recommend')->toArray();

        $viewModel->setVariable('htmlLayoutClass', 'resultView');

        if (array_search('TopIpRange', $defaultTopRecommend) !== false) {
            $viewModel->setVariable('external', $this->isRestrictedTarget());
        }

        return $viewModel;
    }
}
