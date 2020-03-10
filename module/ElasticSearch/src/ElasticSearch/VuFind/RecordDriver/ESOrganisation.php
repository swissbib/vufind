<?php
/**
 * ESOrganisation.php
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
 * Class ESOrganisation
 *
 * @category VuFind
 * @package  ElasticSearch\VuFind\RecordDriver
 * @author   Christoph Boehm <cbo@outermedia.de>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.vufind.org  Main Page
 */
class ESOrganisation extends ElasticSearch
{
    /**
     * Magic function to access all fields
     *
     * @param string $name      Name of the field
     * @param array  $arguments Unused but required
     *
     * @method getDateOfEstablishment()
     * @method getDateOfTermination()
     * @method getDateOfConferenceOrEvent()
     * @method getStartDate()
     * @method getEndDate()
     * @method getPrecedingCorporateBody()
     * @method getPrecedingConferenceOrEvent()
     * @method getSuceedingCorporateBody()
     * @method getSuceedingConferenceOrEvent()
     * @method getAbbreviatedNameForTheCorporateBody()
     * @method getTemporaryNameOfTheCorporateBody()
     * @method getTemporaryNameOfConferenceOrEvent()
     * @method getBiographicalOrHistoricalInformation()
     * @method getDefinition()
     * @method getHierarchicalSuperiorOfTheCorporateBody()
     * @method getHierarchicalSuperiorOfTheConferenceOrEvent()
     * @method getRelatedCorporateBody()
     * @method getRelatedConferenceOrEvent()
     * @method getCorporateBodyIsMember()
     *
     * @return array|null
     */
    public function __call(string $name, $arguments)
    {
        $fieldName = lcfirst(substr($name, 3));
        return $this->getField($fieldName, 'gnd');
    }


    /**
     * Gets the DisplayName
     *
     * @return array|null
     */
    public function getDisplayName()
    {
        return $this->getName();
    }


    /**
     * Gets the Name
     *
     * @return array|null
     */
    public function getName()
    {
        $name = $this->getField('name', 'foaf');
        if (!isset($name)) {
            $name = $this->getField('label', 'rdfs');
        }
        if (isset($name) && is_array($name)) {
            return array_shift($name);
        }
        return $name;
    }

    /**
     * Gets the AlternateNames
     *
     * @return array|null
     */
    public function getAlternateNames()
    {
        return $this->getField('alternateNames', 'schema');
    }

    /**
     * Gets the Location
     *
     * @return array|null
     */
    public function getLocation()
    {
        $location = $this->getField('location', 'schema');
        if (!isset($location)) {
            $location = $this->getField('P131', 'wdt');
        }
        return $location;
    }

    /**
     * Gets the Country
     *
     * @return array|null
     */
    public function getCountry()
    {
        $country = $this->getField('country', 'dbo');
        if (!isset($country)) {
            $country = $this->getField('P131', 'P495');
        }
        return $country;
    }

    /**
     * Gets the LegalForm
     *
     * @return array|null
     */
    public function getLegalForm()
    {
        return $this->getField('P1454', 'wdt');
    }

    /**
     * Gets the DirectorManager
     *
     * @return array|null
     */
    public function getDirectorManager()
    {
        return $this->getField('P1037', 'wdt');
    }

    /**
     * Gets the NotableWork
     *
     * @return array|null
     */
    public function getNotableWork()
    {
        $notableWork = $this->getField('notableWork', 'dbo');
        if (!isset($notableWork)) {
            $notableWork = $this->getField('gnd', 'publication');
        }
        return $notableWork;
    }

    /**
     * Gets the Inception
     *
     * @return array|null
     */
    public function getInception()
    {
        return $this->getField('P571', 'wdt');
    }

    /**
     * Gets the DissolvedAbolishedDemolished
     *
     * @return array|null
     */
    public function getDissolvedAbolishedDemolished()
    {
        return $this->getField('P576', 'wdt');
    }

    /**
     * Gets the Description
     *
     * @return array|null
     */
    public function getDescription()
    {
        return $this->getField('description', 'schema');
    }

    /**
     * Has sufficient data
     *
     * @return bool
     */
    public function hasSufficientData(): bool
    {
        $fields = [
            "rdfs:label",
            "foaf:name"
        ];

        foreach ($fields as $field) {
            if (array_key_exists($field, $this->fields["_source"])) {
                return true;
            }
        }
        return false;
    }
}
