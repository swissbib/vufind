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
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA    02111-1307    USA
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
            $fieldName = substr(substr($name, 0, $pos), 3);
            $field = $this->getField(
                sprintf('dbp%sAsLiteral', $fieldName), 'lsb'
            );

            return !is_null($field) ? $this->getValueByLanguagePriority($field)
                : null;
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
     * @return array|null
     */
    public function getName()
    {
        $firstName = $this->getFirstName();
        $lastName = $this->getLastName();
        if (isset($firstName) && isset($lastName)) {
            return $lastName . ", " . $firstName;
        }
        return $this->getField('label', 'rdfs');
    }

    /**
     * Gets the BirthDate
     *
     * @return \DateTime|null
     */
    public function getBirthDate()
    {
        $date = $this->getField('birthDate');
        return $this->extractDate($date);
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
            ? $localizedAbstract[0] : null;
    }

    /**
     * Gets the Pseudonym
     *
     * @return array|null
     */
    public function getPseudonym()
    {
        $pseudonym = $this->getField("pseudonym");
        return $this->getValueByLanguagePriority($pseudonym);
    }

    /**
     * Gets the BirthPlaceDisplayField
     *
     * @return array|null
     */
    public function getBirthPlaceDisplayField()
    {
        $place = $this->getField("dbpBirthPlaceAsLiteral", "lsb");
        return $this->getValueByLanguagePriority($place);
    }

    /**
     * Gets the DeathPlaceDisplayField
     *
     * @return array|null
     */
    public function getDeathPlaceDisplayField()
    {
        $place = $this->getField("dbpDeathPlaceAsLiteral", "lsb");
        return $this->getValueByLanguagePriority($place);
    }

    /**
     * Gets the DeathDate
     *
     * @return \DateTime|null
     */
    public function getDeathDate()
    {
        $date = $this->getField("deathDate");
        return $this->extractDate($date);
    }

    /**
     * Gets the SameAs
     *
     * @return array|null
     */
    public function getSameAs()
    {
        return $this->getField("sameAs", "owl");
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
            "dbp:thumbnail",
            "dbp:abstract",
            "dbp:birthDate",
            "lsb:dbpBirthPlaceAsLiteral",
            "dbp:deathDate",
            "lsb:dbpDeathPlaceAsLiteral",
            "dbp:abstract",
            "lsb:dbpNationalityAsLiteral",
            "lsb:dbpOccupationAsLiteral"
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
            $userLocale = is_null($userLocale) ? $this->getTranslatorLocale()
                : $userLocale;
            $locales = $this->getPrioritizedLocaleList($userLocale);

            foreach ($locales as $locale) {
                $results = [];

                foreach ($content as $valueArray) {
                    if (isset($valueArray[$locale])
                        && !is_null(
                            $valueArray[$locale]
                        )
                    ) {
                        $results[] = $valueArray[$locale];
                    }
                }

                if (count($results) > 0) {
                    return $results;
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
     * Extracts date
     *
     * @param string $date The date
     *
     * @return \DateTime|null
     */
    protected function extractDate(string $date = null)
    {
        if ($date !== null) {
            return new \DateTime($date);
        }
        return null;
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
        string $name, string $prefix = "dbp", string $delimiter = ":"
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
