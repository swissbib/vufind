<?php
/**
 * SolrMarcSimpleTest
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
 * @package  SwissbibTest_RecordDriver
 * @author   Guenter Hipler  <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.swissbib.org
 */
namespace SwissbibTest\RecordDriver;

/**
 * SolrMarcSimpleTest
 *
 * @category Swissbib_VuFind2
 * @package  SwissbibTest_RecordDriver
 * @author   Guenter Hipler  <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org
 */
class SolrMarcSimpleTest extends SolrMarcTestCase
{
    /**
     * Setup
     *
     * @return void
     */
    public function setUp()
    {
        $this->initialize('marc-simple.json');
    }

    /**
     * TestPrimaryAuthor
     *
     * @return void
     */
    public function testPrimaryAuthor()
    {
        $primaryAuthor = $this->driver->getPrimaryAuthor(false);

        $this->assertInternalType('array', $primaryAuthor);
        $this->assertEquals('Telemann', $primaryAuthor['name']);
        $this->assertEquals('Georg Philipp', $primaryAuthor['forename']);

    }

    /**
     * TestGetUniqueId
     *
     * @return void
     *
     * @throws \Exception
     */
    public function testGetUniqueId()
    {
        $id = $this->driver->getUniqueID();

        $this->assertEquals('005378974', $id);
    }

    /**
     * TestGetPublicationDates
     *
     * @return void
     */
    public function testGetPublicationDates()
    {
        $dates = $this->driver->getPublicationDates();

        $this->assertInternalType('array', $dates);
        $this->assertEquals(1954, $dates[1]);
    }

    /**
     * TestGetSecondaryAuthors
     *
     * @return void
     */
    public function testGetSecondaryAuthors()
    {
        $authors = $this->driver->getSecondaryAuthors(false);

        $this->assertInternalType('array', $authors);
        $this->assertEquals(2, sizeof($authors));

        $this->assertEquals('Kölbel', $authors[0]['name']);
        $this->assertEquals('Herbert', $authors[0]['forename']);
    }

    /**
     * TestGetEdition
     *
     * @return void
     */
    public function testGetEdition()
    {
        $edition = $this->driver->getEdition();

        $this->assertFalse($edition);
    }

    /**
     * TestGetGroup
     *
     * @return void
     */
    public function testGetGroup()
    {
        $group = $this->driver->getGroup();

        $this->assertInternalType('string', $group);
        $this->assertEquals('005378974', $group);
    }

    /**
     * TestGetInstitutions
     *
     * @return void
     */
    public function testGetInstitutions()
    {
        $institutions = $this->driver->getInstitutions();

        $this->assertInternalType('array', $institutions);
        $this->assertEquals('LUMH1', $institutions[0]);
    }

    /**
     * TestGetHostItemEntry
     *
     * @return void
     */
    public function testGetHostItemEntry()
    {
        $entry = $this->driver->getHostItemEntry();

        $this->assertInternalType('array', $entry);
        $this->assertEquals(0, sizeof($entry));
    }

    /**
     * TestGetPublisher
     *
     * @return void
     */
    public function testGetPublisher()
    {
        $publishers = $this->driver->getPublishers(false);

        $this->assertInternalType('array', $publishers);
        $this->assertEquals(1, sizeof($publishers));
        $this->assertEquals('Bärenreiter', $publishers[0]);
    }

    /**
     * TestGetPhysicalDescriptions
     *
     * @return void
     */
    public function testGetPhysicalDescriptions()
    {
        $physicalDescriptions = $this->driver->getPhysicalDescriptions(false);

        $this->assertInternalType('array', $physicalDescriptions);
        $this->assertEquals(1, sizeof($physicalDescriptions));
        $this->assertArrayHasKey('1extent', $physicalDescriptions[0]);
        $this->assertEquals('1 Partitur', $physicalDescriptions[0]['1extent']);
    }

    /**
     * TestGetTitle
     *
     * @return void
     */
    public function testGetTitle()
    {
        $title = $this->driver->getTitle();
        $expect = 'Konzert e-Moll, für Blockflöte, Querflöte, zwei Violinen, Viola und Basso continuo, [TWV 52 e 1] :' .
        ' Concerto in e minor, for recorder, flute, two violins, viola and basso continuo';

        $this->assertInternalType('string', $title);
        $this->assertEquals($expect, $title);
    }

    /**
     * TestGetShortTitle
     *
     * @return void
     */
    public function testGetShortTitle()
    {
        $title = $this->driver->getShortTitle();
        $expect = 'Konzert e-Moll, für Blockflöte, Querflöte, zwei Violinen, Viola und Basso continuo, [TWV 52 e 1]';

        $this->assertInternalType('string', $title);
        $this->assertEquals($expect, $title);
    }

    /**
     * TestGetUnions
     *
     * @return void
     */
    public function testGetUnions()
    {
        $unions = $this->driver->getUnions();

        $this->assertInternalType('array', $unions);
        $this->assertEquals(2, sizeof($unions));
        $this->assertEquals('IDSLU', $unions[0]);
    }

    /**
     * TestGetTitleStatementSimple
     *
     * @return void
     */
    public function testGetTitleStatementSimple()
    {
        $titleSimple = $this->driver->getTitleStatement();
        $expectSimple = 'Georg Philipp Telemann ; hrsg. von Herbert Kölbel ; Generalbass-Bearb. von Otto Kiel';

        $this->assertInternalType('string', $titleSimple);
        $this->assertEquals($expectSimple, $titleSimple);
    }

    /**
     * TestGetTitleStatementFull
     *
     * @return void
     */
    public function testGetTitleStatementFull()
    {
        $titleFull = $this->driver->getTitleStatement(true);

        $this->assertInternalType('array', $titleFull);

        $expect = 'Konzert e-Moll, für Blockflöte, Querflöte, zwei Violinen, Viola und Basso continuo, [TWV 52 e 1]';

        $this->assertEquals($expect, $titleFull['title']);
    }

    /**
     * TestGetAddedCorporateNames
     *
     * @return void
     */
    public function testGetAddedCorporateNames()
    {
        $corporateName = $this->driver->getAddedCorporateNames();

        $this->assertInternalType('array', $corporateName);
    }
}
