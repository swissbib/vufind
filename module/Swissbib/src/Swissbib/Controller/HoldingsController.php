<?php
/**
 * Swissbib HoldingsController
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

use Zend\View\Model\ViewModel;

use Swissbib\RecordDriver\SolrMarc;
use Swissbib\Helper\BibCode;
use Swissbib\RecordDriver\Helper\Holdings;
use Swissbib\VuFind\ILS\Driver\Aleph;

/**
 * Swissbib HoldingsController
 *
 * @category Swissbib_VuFind2
 * @package  Controller
 * @author   Guenter Hipler  <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org
 */
class HoldingsController extends BaseController
{
    /**
     * Page size for holding items popup
     *
     * @var Integer
     */
    protected $PAGESIZE_HOLDINGITEMS = 10;

    /**
     * Get list for items or holdings, depending on the data
     *
     * @return ViewModel
     */
    public function listAction()
    {
        $institution = $this->params()->fromRoute('institution');
        $idRecord = $this->params()->fromRoute('record');
        $record = $this->getRecord($idRecord);
        $template = 'Holdings/nodata';

        try {
            $holdingsData = $record->getInstitutionHoldings($institution);
        } catch (\Exception $e) {
            $holdingsData = [];
        }

        $holdingsData['record'] = $idRecord;
        $holdingsData['recordTitle'] = $record->getTitle();
        $holdingsData['institution'] = $institution;

        if (isset($holdingsData['holdings']) && !empty($holdingsData['holdings'])
            || isset($holdingsData['items']) && !empty($holdingsData['items'])
        ) {
            $template = 'Holdings/holdings-and-items';
        }

        return $this->getAjaxViewModel($holdingsData, $template);
    }

    /**
     * Get items of a holding
     * Displayed in a popup, opened from a holdings/items list in holdings tab
     *
     * @return ViewModel
     */
    public function holdingItemsAction()
    {
        $idRecord = $this->params()->fromRoute('record');
        $record = $this->getRecord($idRecord);
        $institution = $this->params()->fromRoute('institution');
        $resourceId = $this->params()->fromRoute('resource');
        $page = (int)$this->params()->fromQuery('page', 1);
        $year = (int)$this->params()->fromQuery('year');
        $volume = $this->params()->fromQuery('volume');
        $offset = ($page - 1) * $this->PAGESIZE_HOLDINGITEMS;

        /**
         * AlephDriver
         *
         * @var Aleph $aleph
         */
        $catalog = $this->getILS();
        $holdingItems = $catalog->getHoldingHoldingItems(
            $resourceId, $institution, $offset, $year, $volume,
            $this->PAGESIZE_HOLDINGITEMS
        );
        $totalItems = $catalog->getHoldingItemCount(
            $resourceId, $institution, $offset, $year, $volume
        );
        /**
         * HoldingsHelper
         *
         * @var Holdings $helper
         */
        $helper = $this->getServiceLocator()->get('Swissbib\HoldingsHelper');
        $dummyHoldingItem = $this->getFirstHoldingItem($idRecord, $institution);
        $networkCode = $dummyHoldingItem['network'];
        $bibSysNumber = $dummyHoldingItem['bibsysnumber'];
        $admCode = $dummyHoldingItem['adm_code'];
        $bib = $dummyHoldingItem['bib_library'];
        $resourceFilters = $catalog->getResourceFilters($resourceId);
        $extendingOptions = [
            'availability' => true
        ];

        // Add missing data to holding items
        $allBarcodes = [];
        foreach ($holdingItems as $index => $holdingItem) {
            $holdingItem['institution'] = $institution;
            $holdingItem['institution_chb'] = $institution;
            $holdingItem['network'] = $networkCode;
            $holdingItem['bibsysnumber'] = $bibSysNumber;
            $holdingItem['adm_code'] = $admCode;
            $holdingItem['bib_library'] = $bib;
            $holdingItems[$index] = $helper->extendItem(
                $holdingItem, $record, $extendingOptions
            );
            if ($helper->isAlephNetwork($networkCode)) {
                if (!isset($extendingOptions['availability'])
                    || $extendingOptions['availability']
                ) {
                    array_push($allBarcodes, $holdingItem['barcode']);
                }
            }
        }

        if (sizeof($allBarcodes) > 0) {
            $holdingItems
                = $helper->getAvailabilityInfosArray($holdingItems, $allBarcodes);
        }

        $data = [
            'items'         => $holdingItems,
            'record'        => $idRecord,
            'recordTitle'   => $record->getTitle(),
            'institution'   => $institution,
            'page'          => $page,
            'year'          => $year,
            'volume'        => $volume,
            'filters'       => $resourceFilters,
            'total'         => $totalItems, // for paging
            'baseUrlParams' => [
                'institution' => $institution,
                'record'      => $idRecord,
                'resource'    => $resourceId
            ]
        ];

        return $this->getAjaxViewModel($data, 'Holdings/holding-holding-items');
    }

    /**
     * FirstHoldingItem
     *
     * @param string $idRecord        Record id
     * @param string $institutionCode Institution code
     *
     * @return Array
     */
    protected function getFirstHoldingItem($idRecord, $institutionCode)
    {
        $holdingItems = $this->getRecord($idRecord)
            ->getInstitutionHoldings($institutionCode, false);

        return $holdingItems['holdings'][0];
    }

    /**
     * Extract network from resource id
     * The five first chars of the resource are the bib code.
     * Convert the bib code into network code
     *
     * @param String $resourceId resource id
     *
     * @return String
     *
     * @todo Is there a more stable version to do this? It works, but..
     */
    protected function getNetworkFromResource($resourceId)
    {
        $bibCode = strtoupper(substr($resourceId, 0, 5));

        return $this->getBibCodeHelper()->getNetworkCode($bibCode);
    }

    /**
     * Get bib code helper service
     *
     * @return BibCode $bibHelper
     */
    protected function getBibCodeHelper()
    {
        return $this->getServiceLocator()->get('Swissbib\BibCodeHelper');
    }

    /**
     * Build a resource id
     *
     * @param string $idRecord record id
     * @param string $network  network
     *
     * @return string
     */
    protected function getResourceId($idRecord, $network)
    {
        /**
         * BibCodeHelper
         *
         * @var BibCode $bibHelper
         */
        $bibHelper = $this->getServiceLocator()->get('Swissbib\BibCodeHelper');
        $idls = $bibHelper->getBibCode($network);

        return strtoupper($idls) . $idRecord;
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
        return $this->getServiceLocator()->get('VuFind\RecordLoader')
            ->load($idRecord, 'Solr');
    }
}
