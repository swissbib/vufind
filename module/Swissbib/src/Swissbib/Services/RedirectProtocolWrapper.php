<?php
 /**
 * RedirectProtocolWrapper
 *
 * PHP version 5
 *
 * Copyright (C) project swissbib, University Library Basel, Switzerland
 * http://www.swissbib.org  / http://www.swissbib.ch / http://www.ub.unibas.ch
 *
 * Date: 2/13/14
 * Time: 4:49 PM
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
 * @package  Services
 * @author   Guenter Hipler  <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.swissbib.org
 */
namespace Swissbib\Services;

/**
 * RedirectProtocolWrapper
 *
 * @category Swissbib_VuFind2
 * @package  Services
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
class RedirectProtocolWrapper
{
    /**
     * VuFindConfig
     *
     * @var Config
     */
    protected $vfConfig = null;

    /**
     * Constructor
     *
     * @param Config $config VuFindConfig
     */
    public function __construct($config)
    {
        $this->vfConfig = $config;
    }

    /**
     * Wrapper Function to wrap URL'S for another service for swissbib we use the
     * service for http URL's which should used within a https environment
     *
     * @param String $url Url
     *
     * @return String
     */
    public function getWrappedURL($url)
    {
        $wrapper = $this->vfConfig->Content->redirectProtocolWrapper;

        if (!empty($wrapper)) {
            //& has to be escaped otherwise the wrapper service isn't able to
            // group parts of the QUERY_STRING correctly
            $url = preg_replace("/&/", "ESCAPED_AND_PERCENT", $url);
            $url = $this->vfConfig->Content->redirectProtocolWrapper . "?" .
                "targetURL=" . $url;
        }

        return $url;
    }
}
