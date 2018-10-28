<?php
/**
 * WikidataTranslator
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
 * @author   Matthias Edel <matthias.edel@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.swissbib.org
 */
namespace Swissbib\View\Helper;

use Scriptotek\Marc\Collection;
use Swissbib\Services\NationalLicence;
use Swissbib\TargetsProxy\IpMatcher;
use VuFind\RecordDriver\SolrDefault;
use Wikidata\Wikidata;
use Zend\Http\Client;
use Zend\Http\PhpEnvironment\RemoteAddress;
use Zend\Http\Request;
use Zend\Http\Response;
use Zend\ServiceManager\ServiceManager;
use Zend\View\Helper\AbstractHelper;

/**
 * Return URL for NationalLicence online access if applicable. Otherwise 'false'.
 * Config URLs in TargetsProxy.ini.ini[SwissAcademicLibraries]
 *
 * @category Swissbib_VuFind2
 * @package  RecordDriver_Helper
 * @author   Matthias Edel <matthias.edel@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
class WikidataTranslator extends AbstractHelper
{

    /**
     * WikidataTranslator
     */
    public function __construct()
    {
        //nothing now
    }

    /**
     * Invoke
     *
     * @param array  $gndId     Must be an array because we need multiple values
     *                        ['facetName' => 'name', 'facetValue' => 'value']
     * @param array  $tokens  Tokens to inject into the translated string
     * @param string $default Default value to use if no translation is found (null
     *                        for no default).
     *
     * @return string
     */
    public function __invoke($record)
    {
        //$wikidataId = $this->getWikidataId($gndId);
        $gndNumbers = $record->getAuthorsGndNumbers();
        $labels = [];
        $bnfLabels = [];
        foreach($gndNumbers as $gndNumber){
            //removes (DE-588)
            $gndNumber = substr($gndNumber, 8);
            $wikidataId = $this->getWikidataId($gndNumber);
            array_push($labels, $this->getWikidataTranslation($wikidataId));
            //$bnfId = $this->getBnfId($wikidataId);
            //array_push($bnfLabels, $this->getBnfDescription($bnfId));



        }

        return $labels;
    }

    protected function getWikidataTranslation($wikidataId){

        if(empty($wikidataId)){
            return "no match in wikidata";
        }

        //return $wikidataId;
        //$descriptionBNF = $this->getBnfDescription($bnfId);
        $descriptionWikidata = $this->getWikidataDescription($wikidataId);
        return $descriptionWikidata;
    }

    protected function getWikidataId($gndId)
    {
        $wikidata = new Wikidata();
        $results = $wikidata->searchBy('P227', $gndId);
        //$results = $wikidata->searchBy('P238', 'LON');
        if($results->isEmpty()) {
            return '';
        }
        $singleResult = $results->first();
        $resultId = $singleResult->id; // Q84
        return $resultId;
    }

    protected function getWikidataIdBasedOnString($string)
    {
        $wikidata = new Wikidata();
        $results = $wikidata->search($string);
        //$results = $wikidata->searchBy('P238', 'LON');
        if($results->isEmpty()) {
            return '';
        }
        $singleResult = $results->first();
        $resultId = $singleResult->id; // Q84
        return $resultId;
    }

    protected function getBnfId($wikidataId)
    {
        $wikidata = new Wikidata();
        $bnfId = $wikidata->get($wikidataId)->get('bnf_id')[0];


        return $bnfId;
    }

    protected function getBnfDescription($bnfId)
    {
        $client = new Client("http://catalogue.bnf.fr/api/SRU?version=1.2&operation=searchRetrieve&query=aut.ark%20all%20%22ark:/12148/cb118806093%22&recordSchema=unimarcxchange&maximumRecords=20&startRecord=1");
        $response = $client->send();
        $response = file_get_contents("http://catalogue.bnf.fr/api/SRU?version=1.2&operation=searchRetrieve&query=aut.ark%20all%20%22ark:/12148/cb118806093%22&recordSchema=unimarcxchange&maximumRecords=20&startRecord=1");
        $record = \Scriptotek\Marc\Record::fromString($response);
        foreach ($records as $record) {
            $name = $record->getField('210')->getSubfield('b')->getData() . "\n";
        }



        return "Suisse. Conseil Fédéral";
    }

    protected function getWikidataDescription($wikidataId)
    {
        $wikidata = new Wikidata('fr');
        $entity = $wikidata->get($wikidataId);
        return $entity->label;
    }
}
