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
    public function __call(string $name, $arguments)
    {

        $fieldName = lcfirst(substr($name, 3));
        return $this->getField($fieldName);
    }

    public function getShortID() : string
    {
        return substr($this->getUniqueID(), strlen("http://d-nb.info/gnd/"));
    }

    public function getName() : string
    {
        $field = "SubjectHeading";
        $name = $this->getPreferredName($field);

        if (!isset($name)) {
            $type = parent::getField("@type", "", "")[0];
            $field = substr($type, strpos($type, "#") + 1);

            $name = $this->getPreferredName($field);
    }

        return isset($name) ? $name : "";
    }

    /**
     * @param $field
     * @return mixed|null
     */
    protected function getPreferredName($field)
    {
        $name = $this->getField('preferredNameForThe' . $field);
        if (isset($name) && is_array($name) && count($name) > 0) {
            return $name[0];
        }
        $keys = array_keys($this->fields["_source"]);
        foreach ($keys as $key)
        {
            $found = preg_match("/preferredNameForThe(.+)/", $key, $matches);
            if ($found)
            {
                $name = $matches[1];
                return $this->getPreferredName($name);
            }
        }

        return null;
    }

    public function getDeprecatedUri(): array
    {
        return $this->fields["_source"]["http://d-nb_info/standards/elementset/dnb/deprecatedUri"];
    }

    public function getParentSubjects(): array
    {
        return array_unique(
          array_merge(
            [],
            $this->getField("broaderTermGeneral") ?? [],
            $this->getField("broaderTermGeneric") ?? [],
            $this->getField("broaderTermInstantial") ?? [],
            $this->getField("broaderTermPartitive") ?? []
          ));
    }

    /**
     * Caveat: Does not check for sub subjects
     *
     * @return bool
     */
    public function hasSufficientData(): bool
    {
        $fields = [
          // TODO Is this the only variant?
          "http://d-nb_info/standards/elementset/gnd#variantNameForTheSubjectHeading",
            // TODO Add Erläuterungen
        ];

        foreach ($fields as $field) {
            if (array_key_exists($field, $this->fields["_source"])) {
                return true;
            }
        }
        if (count($this->getParentSubjects()) > 0)
        {
            return true;
        }
        return false;
    }

    /**
     * @param $fieldName
     * @param $prefix
     * @return mixed
     */
    protected function getField(
      string $fieldName,
      string $prefix = "http://d-nb_info/standards/elementset/gnd",
      string $delimiter = ":"
    ) {
        if (substr($fieldName, 0, strlen($prefix)) === $prefix) {
            $key = $fieldName;
        } else {
            $key = $prefix . '#' . $fieldName;
        }

        $fields = $this->fields["_source"];
        // TODO Can we have fields with id and values? How to return this values?
        $ids = [];
        $values = [];
        if (array_key_exists($key, $fields)) {
            $type = $this->fields["_source"][$key];
            if (isset($type) && is_array($type) && count($type) > 0)
            {
                // TODO: Is this structure correct?
                $type = $type[0];
                if (array_key_exists("@id", $type)) {
                    $ids[] = $type["@id"];
                }
                if (array_key_exists("@value", $type)) {
                    $values[] = $type["@value"];
                }
                return array_merge($ids, $values);
            }
        }
        return null;
    }
}
