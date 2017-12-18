<?php
/**
 * Created by IntelliJ IDEA.
 * User: boehm
 * Date: 13.12.17
 * Time: 18:13
 */

namespace ElasticSearch\VuFind\RecordDriver;


class ESSubject extends ElasticSearch
{

    public function getName()
    {
        $value = $this->fields["_source"]['http://d-nb_info/standards/elementset/gnd#preferredNameForTheSubjectHeading'][0]["@value"];
        return $value;
    }
}