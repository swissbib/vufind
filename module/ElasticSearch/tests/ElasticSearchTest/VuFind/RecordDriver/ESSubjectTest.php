<?php
/**
 * ESSubjectTest.php
 *
 * PHP Version 7
 *
 * Copyright (C) swissbib 2018
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License version 2,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.    See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category VuFind
 * @package  ElasticSearch\VuFind\RecordDriver
 * @author   Christoph Boehm <cbo@outermedia.de>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.vufind.org  Main Page
 */
namespace ElasticSearchTest\VuFind\RecordDriver;

use ElasticSearch\VuFind\RecordDriver\ESSubject;
use ElasticSearchTest\Bootstrap;
use VuFindTest\Unit\TestCase as VuFindTestCase;

/**
 * Class ESPersonTest
 *
 * @category VuFind
 * @package  ElasticSearch\VuFind\RecordDriver
 * @author   Christoph Boehm <cbo@outermedia.de>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.vufind.org  Main Page
 */
class ESSubjectTest extends VuFindTestCase
{
    /**
     * Set up service manager and National Licence Service.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->sm = Bootstrap::getServiceManager();
    }

    /**
     * Tests getName
     *
     * @return void
     */
    public function testGetName()
    {
        $cut = new ESSubject();

        $data
            = ["_source" =>
                ["preferredName" => "Fernsehsendung"]
            ];

        $cut->setRawData($data);
        $actual = $cut->getName();
        static::assertEquals("Fernsehsendung", $actual);
    }

    /**
     * Tests getBroaderTermGeneral
     *
     * @return void
     */
    public function testGetBroaderTermGeneral()
    {
        $cut = new ESSubject();

        $data
            = [
            "_source" =>
                [
                    "broaderTermGeneral" =>
                        [
                            [
                                "id" => "https://d-nb.info/gnd/4057342-4",
                                "label" => "Stern"
                            ],
                            [
                                "id" => "https://d-nb.info/gnd/4057342-5",
                                "label" => "Stern2"
                            ]
                        ]
                ]
            ];

        $cut->setRawData($data);
        $actual = $cut->getBroaderTermGeneral();
        static::assertEquals("https://d-nb.info/gnd/4057342-4", $actual[0]);
        static::assertEquals("https://d-nb.info/gnd/4057342-5", $actual[1]);
    }
}
