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
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
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
        // while _id is prefixed with 'https://d-nb.info/gnd/'
        return $this->getGndIdentifier()[0];
    }

    /**
     * Returns the full unique identifier of this record
     *
     * @return string Full Unique identifier.
     */
    public function getFullUniqueID()
    {
        return $this->fields["_id"];
    }

    /**
     * Returns name of subject
     *
     * @return string
     */
    public function getName(): string
    {
        $name = $this->getPreferredName();

        return $name[0] ?? "";
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
            "variantName",
            "definition",
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
     * @param string $fieldName Name of the field
     *
     * @return string|null
     */
    protected function getDisplayField(
        string $fieldName
    ) {
        $field = $this->getRawField($fieldName);
        $value = null;

        if (null !== $field && is_array($field)) {
            $value = $this->getValueByLanguagePriority($field);
        }

        return $value;
    }

    /**
     * Generic function to get field content
     * If the field contains id, we only return an array of these id's
     * (broader terms, related terms, ...)
     * else we return an array of the values
     *
     * @param string      $fieldName Name of the field
     * @param string|null $prefix    Optional prefix
     * @param string      $delimiter Optional delimiter
     *
     * @return array|null
     */
    protected function getField(
        string $fieldName,
        string $prefix=null,
        string $delimiter = ':'
    ) {
        $field = $this->getRawField($fieldName);

        $ids = [];
        $values = [];

        if (isset($field)
            && is_array($field)
            && count($field) > 0
            && is_array($field[0])
        ) {
            foreach ($field as $entry) {
                if (array_key_exists("id", $entry)) {
                    $ids[] = $entry["id"];
                } else {
                    $values[] = $entry;
                }
            }
            return array_merge($ids, $values);
        } elseif (isset($field) && is_array($field) && count($field) > 0) {
            return $field;
        } elseif (isset($field)) {
            return [$field];
        }

        return null;
    }

    /**
     * Get Raw Field
     *
     * @param string $fieldName Name of the field
     *
     * @return array|null
     */
    protected function getRawField(
        string $fieldName
    ) {
        $fields = $this->fields["_source"];

        return array_key_exists($fieldName, $fields) ? $fields[$fieldName] : null;
    }
}
