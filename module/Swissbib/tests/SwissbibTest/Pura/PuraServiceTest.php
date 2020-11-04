<?php
/**
 * NationalLicenceServiceTest.
 *
 * PHP version 7
 * Copyright (C) project swissbib, University Library Basel, Switzerland
 * http://www.swissbib.org  / http://www.swissbib.ch / http://www.ub.unibas.ch
 * Date: 1/2/13
 * Time: 4:09 PM
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License version 2,
 * as published by the Free Software Foundation.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category Swissbib_VuFind
 * @package  SwissbibTest_Pura
 * @author   Lionel Walter <lionel.walter@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.swissbib.org
 */
namespace SwissbibTest\Pura;

use Swissbib\Services\Pura;
use SwissbibTest\Bootstrap;
use VuFindTest\Unit\TestCase as VuFindTestCase;

/**
 * Class PuraServiceTest.
 *
 * @category Swissbib_VuFind
 * @package  SwissbibTest_Pura
 * @author   Lionel Walter <lionel.walter@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.swissbib.org
 */
class PuraServiceTest extends VuFindTestCase
{
    /**
     * ServiceLocator.
     *
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * Config.
     *
     * @var array $config
     */
    protected $config;

    /**
     * Pura Service
     *
     * @var Pura $puraService Pura Service
     */
    protected $puraService;

    /**
     * Set up service manager and Pura Service.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->sm = Bootstrap::getServiceManager();
        $this->puraService = $this->sm->get(
            'Swissbib\PuraService'
        );
    }

    /**
     * Test getPublishers
     *
     * @return void
     */
    public function testGetPublishers()
    {
        $publishers = $this->puraService->getPublishers();
        $this->assertGreaterThan(sizeof($publishers), 0);
    }
}
