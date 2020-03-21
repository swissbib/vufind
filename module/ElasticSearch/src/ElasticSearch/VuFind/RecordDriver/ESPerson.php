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
     * @method getPeriodOfActivity()
     * @method getInfluenced()
     * @method getInfluencedBy()
     * @method getMovement()
     * @method getNationality()
     * @method getOccupation()
     * @method getPartner()
     * @method getSpouse()
     * @method getThumbnail()
     * @method getBirthPlaceDisplayField()
     * @method getGenre()
     * @method getGenreDisplayField()
     * @method getInfluencedDisplayField()
     * @method getInfluencedByDisplayField()
     * @method getMovementDisplayField()
     * @method getNationalityDisplayField()
     * @method getOccupationDisplayField()
     * @method getPartnerDisplayField()
     * @method getSpouseDisplayField()
     * @method getOccupationDisplayField()
     * @method getRelatedCorporateBody()
     * @method getEmployer()
     * @method getChild()
     * @method getChildDisplayField()
     * @method getParent()
     * @method getParentDisplayField()
     * @method getSibling()
     * @method getSiblingDisplayField()
     * @method getProfessionalRelationship()
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
     * Gets the getPeriodOfActivity
     *
     * @return string|null
     */
    public function getPeriodOfActivity()
    {
        $val = $this->getField('periodOfActivity', 'gnd');
        if (!isset($val)) {
            $val = $this->getField('P1317', 'wdt');
        }
        return $val;
    }

    /**
     * Gets the Abstract
     *
     * @return mixed|null
     */
    public function getAbstract()
    {
        $abstract = $this->getField('abstract');
        $abstract = $this->getValueByLanguagePriority($abstract);
        if (!isset($abstract)) {
            $abstract = $this->getField('description', 'schema');
            $abstract = $this->getValueByLanguagePriority($abstract);
        }
        if (!isset($abstract)) {
            $abstract = $this->getField('biographicalOrHistoricalInformation', 'gnd');
        }
        return is_array($abstract) && count($abstract) > 0
            ? $abstract[0] : $abstract;
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
            return $this->arrayToString($pseudonym);
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
     * Gets the award received.
     *
     * @return array|null
     */
    public function getAwardReceived()
    {
        $val = $this->getField('P166', 'wdt');
        if (isset($val)) $val = $this->localizedArrayToString($val);
        return $val;
    }

    /**
     * Gets the position held.
     *
     * @return array|null
     */
    public function getPositionHeld()
    {
        $val = $this->getField('P39', 'wdt');
        if (!isset($val)) {
            $val = $this->getField('functionOrRole', 'gnd');
        }
        if (isset($val)) $val = $this->localizedArrayToString($val);
        return $val;
    }

    /**
     * Gets the played instrument.
     *
     * @return array|null
     */
    public function getPlayedInstrument()
    {
        $val = $this->getField('playedInstrument', 'gnd');
        if (isset($val)) $val = $this->localizedArrayToString($val);
        return $val;
    }

    /**
     * Gets the award received.
     *
     * @return array|null
     */
    public function getReligion()
    {
        $val = $this->getField('P140', 'wdt');
        if (isset($val)) $val = $this->localizedArrayToString($val);
        return $val;
    }

    /**
     * Gets the notable work
     *
     * @return string|null
     */
    public function getNotableWork()
    {
        $val = $this->getField('notableWork', 'dbo');
        if (isset($val)) $val = $this->localizedArrayToString($val);
        else if (!isset($val)) {
            $val = $this->getField('publication', 'gnd');
        }
        return $val;
    }

    /**
     * Gets the award received.
     *
     * @return array|null
     */
    public function getNativeLanguage()
    {
        $val = $this->getField('P103', 'wdt');
        if (isset($val)) $val = $this->localizedArrayToString($val);
        return $val;
    }

    /**
     * Gets the award received.
     *
     * @return array|null
     */
    public function getLanguageSpoken()
    {
        $val = $this->getField('P1412', 'wdt');
        if (isset($val)) $val = $this->localizedArrayToString($val);
        return $val;
    }

    /**
     * Gets the award received.
     *
     * @return array|null
     */
    public function getFieldOfStudy()
    {
        $val = $this->getField('fieldOfStudy', 'gnd');
        if (isset($val)) $val = $this->localizedArrayToString($val);
        return $val;
    }

    /**
     * Gets the realIdentity.
     *
     * @return array|null
     */
    public function getRealIdentity()
    {
        $val = $this->getField('realIdentity', 'gnd');
        if (isset($val)) $val = $this->localizedArrayToString($val);
        return $val;
    }

    /**
     * Gets the genre.
     *
     * @return array|null
     */
    public function getGenre()
    {
        $val = $this->getField('genre', 'dbo');
        return $this->getValueByLanguagePriority($val);
    }

    /**
     * Gets the affiliation.
     *
     * @return array|null
     */
    public function getAffiliation($delimiter)
    {
        $val1 = $this->getField('affiliation', 'gnd');
        if (isset($val1)) $val1 = $this->localizedArrayToString($val1);
        $val2 = $this->getField('affiliationAsLiteral', 'gnd');
        if (isset($val2)) {
            $val2 = $this->arrayToString($val2);
            if (isset($val1)) {
                $val1 = $val1 . $delimiter . $val2;
            }
            else {
                $val1  = $val2;
            }
        }
        return $val1;
    }

    /**
     * Gets the relatedCorporateBody.
     *
     * @return array|null
     */
    public function getRelatedCorporateBody()
    {
        $val = $this->getField('relatedCorporateBody', 'gnd');
        if (isset($val)) $val = $this->localizedArrayToString($val);
        return $val;
    }

    /**
     * Gets the employer.
     *
     * @return array|null
     */
    public function getEmployer()
    {
        $val = $this->getField('P108', 'wdt');
        if (isset($val)) $val = $this->localizedArrayToString($val);
        return $val;
    }

    /**
     * Gets the memberOfPoliticalParty.
     *
     * @return array|null
     */
    public function getMemberOfPoliticalParty()
    {
        $val = $this->getField('P102', 'wdt');
        if (isset($val)) $val = $this->localizedArrayToString($val);
        return $val;
    }

    /**
     * Gets the participantOf.
     *
     * @return array|null
     */
    public function getParticipantOf()
    {
        $val = $this->getField('P1344', 'wdt');
        if (isset($val)) $val = $this->localizedArrayToString($val);
        return $val;
    }

    /**
     * Gets the educatedAt.
     *
     * @return array|null
     */
    public function getEducatedAt()
    {
        $val = $this->getField('P69', 'wdt');
        if (isset($val)) $val = $this->localizedArrayToString($val);
        return $val;
    }

    /**
     * Gets the professionalRelationship.
     *
     * @return array|null
     */
    public function getProfessionalRelationship()
    {
        $val = $this->getField('professionalRelationship', 'gnd');
        if (isset($val)) $val = $this->localizedArrayToString($val);
        return $val;
    }

    /**
     * Gets the acquaintanceshipOrFriendship.
     *
     * @return array|null
     */
    public function getAcquaintanceshipOrFriendship()
    {
        $val = $this->getLocalizedField('acquaintanceshipOrFriendship', 'gnd');
        return $val;
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
