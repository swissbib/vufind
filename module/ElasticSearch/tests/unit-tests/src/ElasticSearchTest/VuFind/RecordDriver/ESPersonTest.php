<?php
/**
 * Created by IntelliJ IDEA.
 * User: boehm
 * Date: 18.12.17
 * Time: 13:41
 */

namespace ElasticSearch\VuFind\RecordDriver;


class ESPersonTest extends \PHPUnit_Framework_TestCase
{

    public function testGetBirthPlaceDisplayField()
    {
        $cut = new ESPerson();

        $data = ["_source" => ["lsb:dbpBirthPlaceAsLiteral" => [0 => ["en" => "value"]]]];

        $cut->setRawData($data);
        $actual = $cut->getBirthPlaceDisplayField();
        $this->assertEquals(["value"], $actual);
    }
}

