<?php
/**
 * Availability Helper
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
 * @package  RecordDriver_Helper
 * @author   Guenter Hipler  <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.swissbib.org
 */
namespace Swissbib\RecordDriver\Helper;

use Zend\Config\Config;
use Zend\Http\Client as HttpClient;
use Zend\Http\Response as HttpResponse;

use Swissbib\RecordDriver\Helper\BibCode as BibCodeHelper;

/**
 * Get availability for items
 *
 * @category Swissbib_VuFind2
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
     * Initialize
     * Build IDLS mapping for networks
     *
     * @param BibCode $bibCodeHelper BibCodeHelper
     * @param Config  $config        Config
     */
    public function __construct(BibCodeHelper $bibCodeHelper, Config $config)
    {
        $this->config        = $config;
        $this->bibCodeHelper = $bibCodeHelper;
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
        $apiUrl    = $this->getApiUrl($sysNumber, $barcode, $bib, $locale);

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
