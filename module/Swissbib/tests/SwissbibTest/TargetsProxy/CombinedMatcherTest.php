<?php
/**
 * CombinedMatcherTest
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
 * @package  SwissbibTest_TargetsProxy
 * @author   Guenter Hipler  <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.swissbib.org
 */
namespace SwissbibTest\TargetsProxy;

/**
 * Test detection of targets from combined match patterns (IP + URL)
 *
 * @category Swissbib_VuFind
 * @package  SwissbibTest_TargetsProxy
 * @author   Guenter Hipler  <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org
 */
class CombinedMatcherTest extends TargetsProxyTestCase
{
    /**
     * Setup
     *
     * @return void
     */
    public function setUp(): void
    {
        $path = SWISSBIB_TESTS_PATH . '/SwissbibTest/TargetsProxy';
        $this->initialize($path . '/config_detect_combined.ini');
    }

    /**
     * Test single IP address to NOT match
     *
     * @return void
     */
    public function testBothFail()
    {
        $proxyDetected = $this->targetsProxy->detectTarget(
            '1.2.3.4',
            'swiishbiib.ch'
        );

        $this->assertIsBool( $proxyDetected);
        $this->assertFalse($proxyDetected);
    }

    /**
     * Test single hostname
     *
     * @return void
     */
    public function testUrlSb()
    {
        $proxyDetected = $this->targetsProxy->detectTarget('200.20.0.4', 'swsb');

        $this->assertIsBool( $proxyDetected);
        $this->assertTrue($proxyDetected);
        $this->assertEquals(
            'Target_Both_Match',
            $this->targetsProxy->getTargetKey()
        );
        $this->assertEquals(
            'apiKeyBothMatch',
            $this->targetsProxy->getTargetApiKey()
        );
    }
}
