<?php
/**
 * ESSubject.php
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
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA    02111-1307    USA
 *
 * @category VuFind
 * @package  RecordDriver
 * @author   Christoph Boehm <cbo@outermedia.de>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.vufind.org  Main Page
 */
namespace ElasticSearch\VuFind\RecordDriver;

/**
 * Class ESSubject
 *
 * @category VuFind
 * @package  ElasticSearch\VuFind\RecordDriver
 * @author   Christoph Boehm <cbo@outermedia.de>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.vufind.org  Main Page
 */
class ESSubject extends ElasticSearch
{

    const GND_FIELD_PREFIX = 'http://d-nb_info/standards/elementset/gnd';

    /**
     * Magic function to access all fields
     *
     * @param string $name      Name of the field
     * @param array  $arguments Unused but required
     *
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

    /**
     * Returns the unique identifier of this record
     *
     * @return mixed
     */
    public function getUniqueID()
    {
        // actual record id is stored in the gndIdentifier field,
        // while _id is prefix with 'http://d-nb.info/gnd/'
        return $this->getGndIdentifier()[0];
    }

    /**
     * Returns name of subject
     *
     * @return string
     */
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
     * Returns preferred name for $field
     *
     * @param string $field Field for which the name is searched
     *
     * @return mixed|null
     */
    protected function getPreferredName(string $field)
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

    /**
     * Get Parent Subjects
     *
     * @return array
     */
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
            // @codingStandardsIgnoreLineuse
            "http://d-nb_info/standards/elementset/gnd#variantNameForTheSubjectHeading",
            "http://d-nb_info/standards/elementset/gnd#definition",
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
     * Returns localized name for $fieldName
     *
     * @param string      $fieldName Name of the field
     * @param string|null $prefix    Optional prefix
     * @param string      $delimiter Optional delimiter
     *
     * @return string|null
     */
    protected function getDisplayField(
        string $fieldName, string $prefix = null, string $delimiter = '#'
    ) {
        $field = $this->getRawField($fieldName, $prefix, $delimiter);
        $value = null;

        if (!is_null($field) && is_array($field)) {
            $value = $this->getValueByLanguagePriority($field);
        }

        return $value;
    }

    /**
     * Generic function to get field content
     *
     * @param string      $fieldName Name of the field
     * @param string|null $prefix    Optional prefix
     * @param string      $delimiter Optional delimiter
     *
     * @return array|null
     */
    protected function getField(
        string $fieldName, string $prefix = null, string $delimiter = '#'
    ) {
        $field = $this->getRawField($fieldName, $prefix, $delimiter);

        // TODO Can we have fields with id and values? How to return this values?
        $ids = [];
        $values = [];

        if (isset($field) && is_array($field) && count($field) > 0) {
            // TODO: Is this structure correct?
            foreach ($field as $entry) {
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

    /**
     * Get Raw Field
     *
     * @param string      $fieldName Name of the field
     * @param string|null $prefix    Optional prefix
     * @param string      $delimiter Optional delimiter
     *
     * @return string|null
     */
    protected function getRawField(
        string $fieldName, string $prefix = null, string $delimiter = '#'
    ) {
        $prefix = $prefix ?? self::GND_FIELD_PREFIX;

        if (strpos($fieldName, $prefix) === 0) {
            $key = $fieldName;
        } else {
            $key = $prefix . $delimiter . $fieldName;
        }

        $fields = $this->fields["_source"];

        return array_key_exists($key, $fields) ? $fields[$key] : null;
    }

    /**
     * Get All Fields
     *
     * @return array
     */
    public function getAllFields()
    {
        return $this->fields;
    }
}
