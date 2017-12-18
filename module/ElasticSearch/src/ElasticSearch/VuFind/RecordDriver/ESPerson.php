<?php
/**
 * Created by IntelliJ IDEA.
 * User: boehm
 * Date: 12.12.17
 * Time: 15:26
 */

namespace ElasticSearch\VuFind\RecordDriver;


class ESPerson extends ElasticSearch
{

    public function getFirstName()
    {
        return $this->fields["_source"]['foaf:firstName'];
    }

    public function getLastName()
    {
        return $this->fields["_source"]['foaf:lastName'];
    }

    public function getName()
    {
        return $this->fields["_source"]['rdfs:label'];
    }

    public function getBirthDate()
    {
        $date = $this->fields["_source"]['dbp:birthDate'];
        if ($date !== null) {
            return new \DateTime($date);
        }
        return null;
    }
}