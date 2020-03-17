<?php
/**
 * ESPerson.php
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
 * @package  ElasticSearch\VuFind\RecordDriver
 * @author   Christoph Boehm <cbo@outermedia.de>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.vufind.org  Main Page
 */
namespace ElasticSearch\VuFind\RecordDriver;

/**
 * Class ESPerson
 *
 * @category VuFind
 * @package  ElasticSearch\VuFind\RecordDriver
 * @author   Christoph Boehm <cbo@outermedia.de>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.vufind.org  Main Page
 */
class ESPerson extends ElasticSearch
{
    /**
     * Magic function to access all fields
     *
     * @param string $name      Name of the field
     * @param array  $arguments Unused but required
     *
     * @method getAbstract()
     * @method getBirthPlace()
     * TODO Possibly rather date than string
     * @method getBirthYear()
     * @method getDeathPlace()
     * TODO Possibly rather date than string
     * @method getDeathYear()
     * @method getGenre()
     * @method getInfluenced()
     * @method getInfluencedBy()
     * @method getMovement()
     * @method getNationality()
     * @method getNotableWork()
     * @method getOccupation()
     * @method getPartner()
     * @method getSpouse()
     * @method getThumbnail()
     * @method getBirthPlaceDisplayField()
     * @method getGenreDisplayField()
     * @method getInfluencedDisplayField()
     * @method getInfluencedByDisplayField()
     * @method getMovementDisplayField()
     * @method getNationalityDisplayField()
     * @method getNotableWorkDisplayField()
     * @method getOccupationDisplayField()
     * @method getPartnerDisplayField()
     * @method getSpouseDisplayField()
     * @method getOccupationDisplayField()
     *
     * @return array|null
     */
    public function __call(string $name, $arguments)
    {
        if ($pos = strpos($name, "DisplayField")) {
            $fieldName = lcfirst(substr(substr($name, 0, $pos), 3));
            $field = $this->getLocalizedField($fieldName);

            return $field;
        }
        $fieldName = lcfirst(substr($name, 3));
        return $this->getField($fieldName);
    }

    /**
     * Gets the PersonId
     *
     * @return array|null
     */
    public function getPersonId()
    {
        return $this->getField('id', '@', '');
    }

    /**
     * Gets the GenreId
     *
     * @return array|null
     */
    public function getGenreIds()
    {
        return $this->getField('dbo:genre.@id', '', '');
    }

    /**
     * Gets the FirstName
     *
     * @return array|null
     */
    public function getFirstName()
    {
        return $this->getField('firstName', 'foaf');
    }

    /**
     * Gets the LastName
     *
     * @return array|null
     */
    public function getLastName()
    {
        return $this->getField('lastName', 'foaf');
    }

    /**
     * Gets the Name
     *
     * @return string|null
     */
    public function getName()
    {
        $firstName = $this->getFirstName();
        $lastName = $this->getLastName();
        if (isset($firstName) && isset($lastName)) {
            if (is_array($firstName)) {
                $firstName = array_shift($firstName);
            }
            if (is_array($lastName)) {
                $lastName = array_shift($lastName);
            }
            return $lastName . ", " . $firstName;
        }
        $name = $this->getField('label', 'rdfs');
        if (isset($name) && is_array($name)) {
            return array_shift($name);
        }
        return $name;
    }

    /**
     * Gets the BirthDate
     *
     * @return string|null
     */
    public function getBirthDate()
    {
        return $this->getField('birthDate');
    }

    /**
     * Gets the Abstract
     *
     * @return mixed|null
     */
    public function getAbstract()
    {
        $abstract = $this->getField('abstract');
        $localizedAbstract = $this->getValueByLanguagePriority($abstract);
        return is_array($localizedAbstract) && count($localizedAbstract) > 0
            ? $localizedAbstract[0] : $localizedAbstract;
    }

    /**
     * Gets the Pseudonym
     *
     * @return array|null
     */
    public function getPseudonym()
    {
        $pseudonym = $this->getField("pseudonym");
        if (is_array($pseudonym)) {
            return $this->getValueByLanguagePriority($pseudonym);
        } else {
            return $pseudonym;
        }
    }

    /**
     * Gets the DeathDate
     *
     * @return string|null
     */
    public function getDeathDate()
    {
        return $this->getField("deathDate");
    }

    /**
     * Gets the SameAs
     *
     * @return array|null
     */
    public function getSameAs()
    {
        $sameAs = $this->getField("sameAs", "owl");

        //multiple GND identifiers are present, we only keep the
        //most recent which is in the gnd:gndIdentifier field
        if (is_array($sameAs)) {
            return array_filter($sameAs, [$this, 'isObsoleteGndId']);
        }
        return null;
    }

    /**
     * Check if an uri is an obsolete GND id
     *
     * @param string $uri The uri to test, for example http://d-nb.info/gnd/12162692X
     *
     * @return bool
     */
    protected function isObsoleteGndId($uri)
    {
        if ($this->isGndUri($uri)) {
            $currentGndId = $this->getField('gndIdentifier', 'gnd');
            $gndIdToTest = substr($uri, strrpos($uri, '/') + 1);
            if ($currentGndId === $gndIdToTest) {
                return true;
            } else {
                return false;
            }
        } else {
            return true;
        }
    }

    /**
     * Check if an uri is a GND uri
     *
     * @param string $uri The uri to test, for example
     *                    http://www.wikidata.org/entity/Q220340
     *
     * @return bool
     */
    protected function isGndUri(string $uri)
    {
        $gndRegExp = "/^https{0,1}:\\/\\/d-nb.info\\/gnd\\/.*$/";
        return preg_match($gndRegExp, $uri) === 1 ? true : false;
    }

    /**
     * Gets the RdfType
     *
     * @return array|null
     */
    public function getRdfType()
    {
        return $this->getField("type", "rdf");
    }

    /**
     * Has sufficient data
     * Caveat: Does not check for related subjects
     *
     * @return bool
     */
    public function hasSufficientData(): bool
    {
        $fields = [
            "dbo:thumbnail",
            "dbo:abstract",
            "dbo:birthDate",
            "dbo:birthPlace",
            "dbo:deathDate",
            "dbo:deathPlace",
            "dbo:nationality",
            "dbo:occupation"
        ];

        foreach ($fields as $field) {
            if (array_key_exists($field, $this->fields["_source"])) {
                return true;
            }
        }
        return false;
    }

    // TODO
    /*
     * "rdfs:label": {
            "type": "text"
          },
          "schema:birthDate": {
            "type": "date",
            "format": "year"
          },
          "schema:deathDate": {
            "type": "date",
            "format": "year"
          },
          "schema:familyName": {
            "type": "text"
          },
          "schema:gender": {
            "type": "keyword"
          },
          "schema:givenName": {
            "type": "text"
          },
          "schema:sameAs": {
            "type": "keyword"
          },
          "skos:note": {
            "type": "text"
          }
     */

    /**
     * Gets the alternate names available from the underlying fields.
     *
     * @return array|null
     */
    public function getAlternateNames()
    {
        return $this->getField('alternateName', 'schema');
    }

    /**
     * Gets an array of all [wikidata] identifiers for a specific field
     *
     * @param string $name   field name
     * @param string $prefix prefix
     *
     * @return array|null
     */
    public function getWikidataIdentifiersForField(
        string $name, string $prefix='dbo'
    ) {
        $field = $this->getField($name, $prefix);

        $ids = [];

        if (isset($field) && is_array($field) && count($field) > 0
            && is_array($field[0])
        ) {
            foreach ($field as $entry) {
                if (array_key_exists("@id", $entry)) {
                    $ids[] = $entry["@id"];
                }
            }
            return array_merge($ids);
        }
        return null;
    }

    /**
     * Gets the field labels in the locale language
     *
     * @param string $name   field name
     * @param string $prefix prefix
     *
     * @return array|null
     */
    protected function getLocalizedField(string $name, string $prefix='dbo')
    {
        $field = $this->getField($name, $prefix);
        if ($field !== null) {
            $localizedField = $this->getArrayOfValuesByLanguagePriority(
                $field
            );
            return $localizedField;
        } else {
            return null;
        }
    }

    /**
     * Get an Array of all the values in the locale language
     *
     * @param array|null  $content    the content
     * @param string|null $userLocale the locale
     *
     * @return array
     */
    protected function getArrayOfValuesByLanguagePriority(
        array $content = null, string $userLocale = null
    ) {
        $results = [];
        foreach ($content as $value) {
            $results[] = $this->getValueByLanguagePriority($value);
        }
        return $results;
    }

    /**
     * Gets the ValueByLanguagePriority
     *
     * @param array  $content    The content
     * @param string $userLocale The (optional) locale
     *
     * @return array|null
     */
    protected function getValueByLanguagePriority(
        array $content = null, string $userLocale = null
    ) {
        $results = null;

        if ($content !== null && is_array($content) && count($content) > 0) {
            $userLocale = null === $userLocale ? $this->getTranslatorLocale()
                : $userLocale;
            $locales = $this->getPrioritizedLocaleList($userLocale);

            foreach ($locales as $locale) {
                if (isset($content[$locale])
                    && null !== $content[$locale]
                ) {
                    return $content[$locale];
                }
            }
        }

        return null;
    }

    /**
     * Gets the PrioritizedLocaleList
     *
     * @param string $userLocale The user locale
     *
     * @return array
     */
    protected function getPrioritizedLocaleList(string $userLocale)
    {
        $locales = ['en', 'de', 'fr', 'it'];
        $userLocaleIndex = array_search($userLocale, $locales);

        // remove user locale from its current position if available
        if ($userLocaleIndex !== false) {
            array_splice($locales, $userLocaleIndex, 1);
        }

        // and prepend it to gain highest priority
        array_unshift($locales, $userLocale);

        return $locales;
    }

    /**
     * Gets the Field
     *
     * @param string $name      Name of the field
     * @param string $prefix    The prefix
     * @param string $delimiter The delimiter
     *
     * @return array|null
     */
    protected function getField(
        string $name, string $prefix = "dbo", string $delimiter = ":"
    ) {
        return parent::getField($name, $prefix, $delimiter);
    }

    /**
     * Gets AllFields
     *
     * @return array
     */
    public function getAllFields()
    {
        return $this->fields;
    }
}
