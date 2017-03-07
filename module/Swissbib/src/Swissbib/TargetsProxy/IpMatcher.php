<?php
/**
 * IpMatcher
 *
 * PHP version 5
 *
 * Copyright (C) project swissbib, University Library Basel, Switzerland
 * http://www.swissbib.org  / http://www.swissbib.ch / http://www.ub.unibas.ch
 *
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
 * @package  TargetsProxy
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
namespace Swissbib\TargetsProxy;

/**
 * IpMatcher detect whether IP address matches to patterns and ranges of IP addresses
 * Using IPv4 notation
 *
 * Type: single - regular IP address - ex: '127.0.0.1'
 * Type: wildcard - one or more digits use placeholders - ex: '172.0.0.*'
 * Type: mask - two IP addresses separated by slash - ex: '126.1.0.0/255.255.0.0'
 * Type: range - two IP addresses separated by minus - ex: '125.0.0.1-125.0.0.9'
 *
 * Usage:
 * $patterns = array('172.0.*.*', '126.1.0.0/255.255.0.0')
 * $matching = new IpMatcher()->isMatching('126.1.0.2', $patterns);
 *
 * Result: true
 *
 * @category Swissbib_VuFind2
 * @package  TargetsProxy
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
class IpMatcher
{
    /**
     * IP_PATTERN_TYPE_SINGLE
     *
     * @var string
     */
    private static $_IP_PATTERN_TYPE_SINGLE = 'single';

    /**
     * IP_PATTERN_TYPE_SINGLE
     *
     * @var string
     */
    private static $_IP_PATTERN_TYPE_WILDCARD = 'wildcard';

    /**
     * IP_PATTERN_TYPE_MASK
     *
     * @var string
     */
    private static $_IP_PATTERN_TYPE_MASK = 'mask';

    /**
     * IP_PATTERN_TYPE_RANGE
     *
     * @var string
     */
    private static $_IP_PATTERN_TYPE_RANGE = 'range';

    /**
     * AllowedIPs
     *
     * @var array
     */
    private $_allowedIps = [];

    /**
     * Constructor
     */
    public function __construct()
    {

    }

    /**
     * Check whether given IP address matches any of the allowed IP address patterns
     *
     * @param String $ipAddress IpAddress
     * @param Array  $patterns  Array of allow-patterns, possible types:
     *                          IP / IP wildcard / IP mask / IP range
     *
     * @throws Exception
     *
     * @return Boolean
     */
    public function isMatching($ipAddress, array $patterns = [])
    {
        foreach ($patterns as $ipPattern) {
            $type = $this->_detectIpPatternType($ipPattern);
            if (!$type) {
                throw new Exception('Invalid IP Pattern: ' . $ipPattern);
            }

            $subRst = call_user_func(
                [$this, '_isIpMatching' . ucfirst($type)],
                $ipPattern,
                $ipAddress
            );
            if ($subRst) {
                return true;
            }
        }

        return false;
    }

    /**
     * Detect type of given IP "description" (IP address / IP wildcard /
     * IP mask / IP range)
     *
     * @param String $ip Ip
     *
     * @return Boolean|String
     */
    private function _detectIpPatternType($ip)
    {
        if (strpos($ip, '*')) {
            return self::$_IP_PATTERN_TYPE_WILDCARD;
        }

        if (strpos($ip, '/')) {
            return self::$_IP_PATTERN_TYPE_MASK;
        }

        if (strpos($ip, '-')) {
            return self::$_IP_PATTERN_TYPE_RANGE;
        }

        $ip = trim(ltrim($ip, '0'));
        if (ip2long($ip)) {
            return self::$_IP_PATTERN_TYPE_SINGLE;
        }

        return false;
    }

    /**
     * Check whether the given IP address matches the given IP address
     *
     * @param String $allowedIp AllowedIP
     * @param String $ip        IP
     *
     * @return Boolean
     */
    private function _isIpMatchingSingle($allowedIp, $ip)
    {
        return (ip2long($allowedIp) == ip2long($ip));
    }

    /**
     * Check whether the given IP address wildcard matches the given IP address
     *
     * @param String $allowedIp AllowedIP
     * @param String $ip        IP
     *
     * @return Boolean
     */
    private function _isIpMatchingWildcard($allowedIp, $ip)
    {
        $allowedIpArr = explode('.', $allowedIp);
        $ipArr = explode('.', $ip);

        for ($i = 0; $i < count($allowedIpArr); $i++) {
            if ($allowedIpArr[$i] == '*') {
                return true;
            } else {
                if (false == ($allowedIpArr[$i] == $ipArr[$i])) {
                    return false;
                }
            }
        }

        return false;
    }

    /**
     * Check whether the given IP address mask matches the given IP address
     *
     * @param String $allowedIp AllowedIP
     * @param String $ip        IP
     *
     * @return Boolean
     */
    private function _isIpMatchingMask($allowedIp, $ip)
    {
        list($allowedIpIp, $allowedIpMask) = explode('/', $allowedIp);

        $begin = (ip2long($allowedIpIp) &   ip2long($allowedIpMask)) + 1;
        $end = (ip2long($allowedIpIp) | (~ip2long($allowedIpMask))) + 1;
        $ip = ip2long($ip);

        return ($ip >= $begin && $ip <= $end);
    }

    /**
     * Check whether the given IP address range includes the given IP address
     *
     * @param String $allowedIp AllowedIP
     * @param String $ip        IP
     *
     * @return Boolean
     */
    private function _isIpMatchingRange($allowedIp, $ip)
    {
        list($begin, $end) = explode('-', $allowedIp);

        $begin    = ip2long($begin);
        $end    = ip2long($end);
        $ip        = ip2long($ip);

        return ($ip >= $begin && $ip <= $end);
    }
}
