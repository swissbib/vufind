<?php
/**
 * ESPersonTest.php
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

use ElasticSearch\VuFind\RecordDriver\ESPerson;
use Swissbib\Services\NationalLicence;
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
class ESPersonTest extends VuFindTestCase
{
    /**
     * Set up service manager and National Licence Service.
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->sm = Bootstrap::getServiceManager();
    }
    /**
     * Tests getBirthPlaceDisplayField
     *
     * @return void
     */
    public function testGetBirthPlaceDisplayField()
    {
        $cut = new ESPerson();

        $data
            = ["_source" =>
            ["lsb:dbpBirthPlaceAsLiteral" => [0 => ["en" => "value"]]]
            ];

        $cut->setRawData($data);
        $actual = $cut->getBirthPlaceDisplayField();
        static::assertEquals(["value"], $actual);
    }

    /**
     * Tests getOccupation
     *
     * @return void
     */
    public function testGetOccupation()
    {
        $cut = new ESPerson();

        $data
            = ["_source" =>
                ["dbo:occupation" =>
                    [
                        [
                            "en" => "pianist",
                            "de" => "klavierspieler",
                            "fr" => "pianiste",
                            "@id" => "http://d-nb.info/gnd/4131406-2",
                        ],
                        [
                            "@id" => "http://d-nb.info/gnd/1234",
                            "de" => "komponist",
                            "en" => "composer",
                            "fr" => "compositeur",
                        ]
                    ]
                ]
            ];

        $cut->setRawData($data);
        $actual = $cut->getOccupations();
        static::assertEquals(["pianist", "composer"], $actual);
    }
}
