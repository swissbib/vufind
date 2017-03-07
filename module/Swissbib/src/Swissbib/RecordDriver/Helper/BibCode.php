<?php
/**
 * BibCode
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

/**
 * Convert network code into bib code
 * Uses holding config for mapping
 *
 * @category Swissbib_VuFind2
 * @package  RecordDriver_Helper
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
class BibCode
{
    /**
     * Network2Bib mapping
     *
     * @var Array
     */
    protected $network2bib = [];

    /**
     * Bib2Network mapping
     *
     * @var Array
     */
    protected $bib2network = [];

    /**
     * Constructor
     *
     * @param Config $alephNetworkConfig AlephNetworkConfig
     */
    public function __construct(Config $alephNetworkConfig)
    {
        foreach ($alephNetworkConfig as $networkCode => $info) {
            list($url, $idls) = explode(',', $info);
            $networkCode = strtolower($networkCode);

            $this->network2bib[$networkCode] = strtoupper($idls);
        }

        $this->bib2network = array_flip($this->network2bib);
    }

    /**
     * Get bib code for network code
     *
     * @param String $networkCode NetworkCode
     *
     * @return String
     */
    public function getBibCode($networkCode)
    {
        $networkCode = strtolower($networkCode);

        return isset($this->network2bib[$networkCode]) ?
            $this->network2bib[$networkCode] : '';
    }

    /**
     * Get network code
     *
     * @param String $bibCode BibCode
     *
     * @return string
     */
    public function getNetworkCode($bibCode)
    {
        $bibCode = strtoupper($bibCode);

        return isset($this->bib2network[$bibCode]) ?
            $this->bib2network[$bibCode] : '';
    }
}
