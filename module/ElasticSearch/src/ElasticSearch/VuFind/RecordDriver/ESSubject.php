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

    const GND_FIELD_PREFIX = 'http://d-nb_info/standards/elementset/gnd';

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
     * @method getDefinitionDisplayField()
     *
     * @param string    $name
     * @param $arguments
     * @return mixed
     */
    public function __call(string $name, $arguments): array
    {
        if ($pos = strpos($name, "DisplayField")) {
            $fieldName = lcfirst(substr(substr($name, 0, $pos), 3));
            $fieldValue = $this->getDisplayField($fieldName);
        } else {
            $fieldName = lcfirst(substr($name, 3));
            $fieldValue = $this->getField($fieldName);
        }

        return $fieldValue ?? [];
    }

    public function getUniqueID()
    {
        # actual record id is stored in the gndIdentifier field, while _id is prefix with 'http://d-nb.info/gnd/'
        return $this->getGndIdentifier()[0];
    }

    public function getName(): string
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
        foreach ($keys as $key) {
            $found = preg_match("/preferredNameForThe(.+)/", $key, $matches);
            if ($found) {
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
            )
        );
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
        if (count($this->getParentSubjects()) > 0) {
            return true;
        }
        return false;
    }

    /**
     * @param string $fieldName
     * @param string|null $prefix
     * @param string $delimiter
     * @return null
     */
    protected function getDisplayField(string $fieldName, string $prefix = null, string $delimiter = '#')
    {
        $field = $this->getRawField($fieldName, $prefix, $delimiter);
        $value = null;

        if (!is_null($field) && is_array($field)) {
            $value = $this->getValueByLanguagePriority($field);
        }

        return $value;
    }

    /**
     * @param string $fieldName
     * @param string|null $prefix
     * @param string $delimiter
     * @return array|null
     */
    protected function getField(string $fieldName, string $prefix = null, string $delimiter = '#')
    {
        $field = $this->getRawField($fieldName, $prefix, $delimiter);

        // TODO Can we have fields with id and values? How to return this values?
        $ids = [];
        $values = [];


        if (isset($field) && is_array($field) && count($field) > 0)
        {
            // TODO: Is this structure correct?
            foreach ($field as $entry)
            {
                if (array_key_exists("@id", $entry)) {
                    $ids[] = $entry["@id"];
                }
                if (array_key_exists("@value", $entry)) {
                    $values[] = $entry["@value"];
                }
            }
            return array_merge($ids, $values);
        }

        return null;
    }

    protected function getRawField(string $fieldName, string $prefix = null, string $delimiter = '#')
    {
        $prefix = $prefix ?? self::GND_FIELD_PREFIX;

        if (strpos($fieldName, $prefix) === 0) {
            $key = $fieldName;
        } else {
            $key = $prefix . $delimiter. $fieldName;
        }

        $fields = $this->fields["_source"];

        return array_key_exists($key, $fields) ? $fields[$key] : null;
    }


    public function getAllFields() {
        return $this->fields;
    }
}
