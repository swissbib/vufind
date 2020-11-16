<?php
/**
 * Availability Helper
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
 * @package  RecordDriver_Helper
 * @author   Guenter Hipler  <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.swissbib.org
 */
namespace Swissbib\RecordDriver\Helper;

use Laminas\Config\Config;
use Laminas\Http\Client as HttpClient;
use Laminas\Http\Response as HttpResponse;
use Swissbib\RecordDriver\Helper\BibCode as BibCodeHelper;
use VuFind\Log\Logger;

/**
 * Get availability for items
 *
 * @category Swissbib_VuFind
 * @package  RecordDriver_Helper
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
class Availability
{
    /**
     * Config
     *
     * @var Config
     */
    protected $config;

    /**
     * BibCodeHelper
     *
     * @var BibCode
     */
    protected $bibCodeHelper;

    /**
     * Logger
     *
     * @var Logger
     */
    protected $logger;

    /**
     * Initialize
     * Build IDLS mapping for networks
     *
     * @param BibCode $bibCodeHelper BibCodeHelper
     * @param Config  $config        Config
     * @param Logger  $logger        Logger
     */
    public function __construct(BibCodeHelper $bibCodeHelper, Config $config,
        Logger $logger
    ) {
        $this->config        = $config;
        $this->bibCodeHelper = $bibCodeHelper;
        $this->logger = $logger;
    }

    /**
     * Get availability info
     *
     * @param String $sysNumber SysNumer
     * @param Array  $barcode   Array of BarCode Strings
     * @param String $bib       Bib
     * @param String $locale    Locale
     *
     * @return Array|Boolean
     */
    public function getAvailability($sysNumber, $barcode, $bib, $locale)
    {
        $apiUrl = $this->getApiUrl($sysNumber, $barcode, $bib, $locale);

        try {
            $responseBody    = $this->fetch($apiUrl);
            $responseData    = json_decode($responseBody, true);
            //the following line could be used to check on
            //json errors (possible trouble with UTF8 encountered)
            //$error          = json_last_error();

            if (is_array($responseData)) {
                return $responseData;
            }

            throw new \Exception('Unknown response data');
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get availability info by library network
     *
     * @param String $sysNumber SysNumer
     * @param String $bib       Bib
     * @param String $locale    Locale
     *
     * @return Array|Boolean
     */
    public function getAvailabilityByLibraryNetwork($sysNumber, $bib, $locale)
    {
        $apiUrl = $this->getApiUrlByLibraryNetworkUrl($sysNumber, $bib, $locale);
        try {
            $responseBody    = $this->fetch($apiUrl);
            $responseData    = json_decode($responseBody, true);
            if (is_array($responseData)) {
                return $responseData;
            }
            throw new \Exception('Unknown response data');
        } catch (\Exception $e) {
            $msg = 'AvailabilityService has thrown an Exception' .
                ' while called with idls=' . $bib .
                ' and sysNr=' . $sysNumber . ': ' . $e;
            $this->logger->log(Logger::ALERT, $msg);
            return false;
        }
    }

    /**
     * Get IDLS code for network
     *
     * @param String $network Network
     *
     * @return String
     */
    protected function getIDLS($network)
    {
        return $this->bibCodeHelper->getBibCode($network);
    }

    /**
     * Build API url from params
     *
     * @param String $sysNumber Sysnumber
     * @param Array  $barcode   Array of BarCode Strings
     * @param String $bib       Bib
     * @param String $locale    Locale
     *
     * @return String
     */
    protected function getApiUrl($sysNumber, $barcode, $bib, $locale)
    {
        $barcodeParameters = '';

        foreach ($barcode as $singleBarCode) {
            $barcodeParameters .= '&barcode=' . $singleBarCode;
        }

        return     $this->config->apiEndpoint
            . '?sysnumber=' . $sysNumber
            . $barcodeParameters
            . '&idls=' . $bib
            . '&language=' . $locale;
    }

    /**
     * Build API by network library url from params
     *
     * @param String $sysNumber Sysnumber
     * @param String $bib       alephlibrary
     * @param String $locale    Locale
     *
     * @return String
     */
    protected function getApiUrlByLibraryNetworkUrl($sysNumber, $bib, $locale)
    {
        return     $this->config->apiByLibraryNetworkEndpoint
            . '?sysnumber=' . $sysNumber
            . '&idls=' . $bib
            . '&language=' . $locale;
    }

    /**
     * Download data from server
     *
     * @param String $url Url
     *
     * @return Array
     *
     * @throws \Exception
     */
    protected function fetch($url)
    {
        $client = new HttpClient(
            $url, [
                'timeout'      => 10
            ]
        );
        $client->setOptions(['sslverifypeer' => false]);

        /**
         * HttpResponse
         *
         * @var HttpResponse $response
         */
        $response = $client->send();

        if ($response->isSuccess()) {
            return $response->getBody();
        } else {
            throw new \Exception(
                'Availability request failed: ' . $response->getReasonPhrase()
            );
        }
    }
}
