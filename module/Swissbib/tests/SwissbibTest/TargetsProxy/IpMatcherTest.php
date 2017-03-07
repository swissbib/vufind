<?php
/**
 * IpMatcherTest
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
 * @package  SwissbibTest_TargetsProxy
 * @author   Guenter Hipler  <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.swissbib.org
 */
namespace SwissbibTest\TargetsProxy;

/**
 * IpMatcherTest
 *
 * @category Swissbib_VuFind2
 * @package  SwissbibTest_TargetsProxy
 * @author   Guenter Hipler  <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org
 */
class IpMatcherTest extends TargetsProxyTestCase
{
    /**
     * Setup
     *
     * @return void
     */
    public function setUp()
    {
        $path = SWISSBIB_TESTS_PATH . '/SwissbibTest/TargetsProxy';
        $this->initialize($path . '/config_detect_ip.ini');
    }

    /**
     * Test single IP address to NOT match
     *
     * @return void
     */
    public function testIpAddressFalse()
    {
        $proxyDetected = $this->targetsProxy->detectTarget('99.99.99.99', 'xxx.xxx.xx');

        $this->assertInternalType('bool', $proxyDetected);
        $this->assertFalse($proxyDetected);
    }

    /**
     * Test single IP address match (exact)
     *
     * @return void
     */
    public function testIpAddressSingle()
    {
        $proxyDetected = $this->targetsProxy->detectTarget('120.0.0.1', 'unibas.swissbib.ch');

        $this->assertInternalType('bool', $proxyDetected);
        $this->assertTrue($proxyDetected);
        $this->assertEquals('Target_Ip_Single', $this->targetsProxy->getTargetKey());
        $this->assertEquals('apiKeyIpSingle', $this->targetsProxy->getTargetApiKey());
    }

    /**
     * Test IP address wildcard match
     *
     * @return void
     */
    public function testIpAddressWildcard()
    {
        $proxyDetected = $this->targetsProxy->detectTarget('121.0.2.3', 'unibas.swissbib.ch');

        $this->assertInternalType('bool', $proxyDetected);
        $this->assertTrue($proxyDetected);
        $this->assertEquals('Target_Ip_Wildcard', $this->targetsProxy->getTargetKey());
        $this->assertEquals('apiKeyIpWildcard', $this->targetsProxy->getTargetApiKey());
    }

    /**
     * Test IP address wildcard match
     *
     * @return void
     */
    public function testIpAddressSection()
    {
        $proxyDetected = $this->targetsProxy->detectTarget('0.0.5.5', 'unibas.swissbib.ch');

        $this->assertInternalType('bool', $proxyDetected);
        $this->assertTrue($proxyDetected);
        $this->assertEquals('Target_Ip_Section', $this->targetsProxy->getTargetKey());
        $this->assertEquals('apiKeyIpSection', $this->targetsProxy->getTargetApiKey());
    }

    /**
     * Test single IP address match (exact) from comma separated list of patterns
     *
     * @return void
     */
    public function testIpAddressSingleCSV()
    {
        $proxyDetected = $this->targetsProxy->detectTarget('124.0.0.1', 'unibas.swissbib.ch');

        $this->assertInternalType('bool', $proxyDetected);
        $this->assertTrue($proxyDetected);
        $this->assertEquals('Target_Ip_Single_CSV', $this->targetsProxy->getTargetKey());
        $this->assertEquals('apiKeyIpSingleCSV', $this->targetsProxy->getTargetApiKey());
    }

    /**
     * Test wildcard IP address match from comma separated list of patterns
     *
     * @return void
     */
    public function testIpAddressWildcardCSV()
    {
        $proxyDetected = $this->targetsProxy->detectTarget('125.0.2.3', 'unibas.swissbib.ch');

        $this->assertInternalType('bool', $proxyDetected);
        $this->assertTrue($proxyDetected);
        $this->assertEquals('Target_Ip_Wildcard_CSV', $this->targetsProxy->getTargetKey());
        $this->assertEquals('apiKeyIpWildcardCSV', $this->targetsProxy->getTargetApiKey());
    }

    /**
     * Test wildcard IP address match from comma separated list of patterns
     *
     * @return void
     */
    public function testIpAddressSectionCSV()
    {
        $proxyDetected = $this->targetsProxy->detectTarget('150.0.0.0', 'unibas.swissbib.ch');

        $this->assertInternalType('bool', $proxyDetected);
        $this->assertTrue($proxyDetected);
        $this->assertEquals('Target_Ip_Section_CSV', $this->targetsProxy->getTargetKey());
        $this->assertEquals('apiKeyIpSectionCSV', $this->targetsProxy->getTargetApiKey());
    }
}
