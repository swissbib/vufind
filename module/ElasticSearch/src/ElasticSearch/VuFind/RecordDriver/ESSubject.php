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

    /**
     * @method getAacademicDegree()
     * @method getAccordingWork()
     * @method getAccreditedArtist()
     * @method getAccreditedAuthor()
     * @method getAccreditedComposer()
     * @method getAcquaintanceshipOrFriendship()
     * @method getAddition()
     * @method getAffiliation()
     * ...
     *
     * @param string $name
     * @param $arguments
     * @return mixed
     */
    public function __call(string $name, $arguments) : array
    {

        $fieldName = lcfirst(substr($name, 3));
        return $this->getField($fieldName);
    }

    public function getName()
    {
        $value = $this->fields["_source"]['http://d-nb_info/standards/elementset/gnd#preferredNameForTheSubjectHeading'][0]["@value"];
        return $value;
    }

    public function getDeprecatedUri() : array
    {
        return $this->fields["_source"]["http://d-nb_info/standards/elementset/dnb/deprecatedUri"];
    }

    /**
     * Should be more than label
     * @return bool
     */
    public function hasSufficientData() : bool
    {
        $count = count($this->fields["_source"]);
        return $count > 1;
    }

    /**
     * @param $fieldName
     * @param $prefix
     * @return mixed
     */
    protected function getField(string $fieldName, string $prefix = "http://d-nb_info/standards/elementset/gnd")
    {
        $key = $prefix . '#' . $fieldName;
        $fields = $this->fields["_source"];
        // TODO Can we have fields with id and values? How to return this values?
        $ids = [];
        $values = [];
        if(array_key_exists($key, $fields))
        {
            $type = $this->fields["_source"][$prefix . '#' . $fieldName];
            if (array_key_exists("@id", $type))
            {
                $ids = $type["@id"];
            }
            if (array_key_exists("@value", $type))
            {
                $values = $type["@value"];
            }
            return array_merge($ids, $values);
        }
        return null;
    }
}