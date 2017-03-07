<?php
/**
 * SolrMarcSubjectVocabulariesTest
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
 * SolrMarcSubjectVocabulariesTest
 *
 * @category Swissbib_VuFind2
 * @package  SwissbibTest_RecordDriver
 * @author   Guenter Hipler  <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org
 */
class SolrMarcSubjectVocabulariesTest extends SolrMarcTestCase
{
    /**
     * Setup
     *
     * @return void
     */
    public function setUp()
    {
        $this->initialize('marc-subjectheadings.json');
    }

    /**
     * TestGetAllSubjectVocabularies
     *
     * @return void
     */
    public function testGetAllSubjectVocabularies()
    {
        $subjectVocabularies = $this->driver->getAllSubjectVocabularies();

        $this->assertInternalType('array', $subjectVocabularies);

        $this->assertEquals(2, sizeof($subjectVocabularies));
        $this->assertArrayHasKey('gnd', $subjectVocabularies);
        $this->assertArrayHasKey('lcsh', $subjectVocabularies);
        //    $this->assertArrayHasKey('bisacsh', $subjectVocabularies);
        //    $this->assertArrayHasKey('ids zbz', $subjectVocabularies);
        $this->assertArrayNotHasKey('local', $subjectVocabularies);

        //  $this->assertEquals(0, $subjectVocabularies['lcsh']['650'][0]['@ind2']);

        $this->assertInternalType('array', $subjectVocabularies['gnd']);
        $this->assertArrayHasKey('650', $subjectVocabularies['gnd']);
    }
}
