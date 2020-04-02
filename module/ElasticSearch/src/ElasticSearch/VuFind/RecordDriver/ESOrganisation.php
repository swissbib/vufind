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
     * @method getSucceedingCorporateBody()
     * @method getSucceedingConferenceOrEvent()
     * @method getAbbreviatedNameForTheCorporateBody()
     * @method getTemporaryNameOfTheCorporateBody()
     * @method getTemporaryNameOfTheConferenceOrEvent()
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
        if ($pos = strpos($name, "DisplayField")) {
            $fieldName = lcfirst(substr(substr($name, 0, $pos), 3));
            $field = $this->getLocalizedField($fieldName);
            return $field;
        }
        $fieldName = lcfirst(substr($name, 3));
        return $this->getField($fieldName, 'gnd');
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
     * Gets the notable works
     *
     * @return array|null
     */
    public function getNotableWorks()
    {
        $name = $this->getField('notableWork', 'dbo');
        if (!isset($name)) {
            $name = $this->getField('publication', 'gnd');
        }
        if (isset($name) && is_array($name)) {
            return array_shift($name);
        }
        return $name;
    }

    /**
     * Gets the AlternateName
     *
     * @return array|null
     */
    public function getAlternateName()
    {
        return $this->getField('alternateName', 'schema');
    }

    /**
     * Gets the genre
     *
     * @return array|null
     */
    public function getGenre()
    {
        $genre = $this->getField('P136', 'wdt');
        return $this->getValueByLanguagePriority($genre);
    }

    /**
     * Gets the dateOfEstablishment
     *
     * @return array|null
     */
    public function getDateOfEstablishment()
    {
        $val = $this->getField('dateOfEstablishment', 'gnd');
        if (!isset($val)) {
            $val = $this->getField('P571', 'wdt');
        }
        return $val;
    }

    /**
     * Gets the genre
     *
     * @return array|null
     */
    public function getDateOfTermination()
    {
        $val = $this->getField('dateOfTermination', 'gnd');
        if (!isset($val)) {
            $val = $this->getDissolvedAbolishedDemolished();
        }
        return $val;
    }

    /**
     * Gets the PrecedingCorporateBody
     *
     * @return array|null
     */
    public function getPrecedingCorporateBody()
    {
        $val = $this->getField('precedingCorporateBody', 'gnd');
        if (isset($val)) $val = $this->localizedArrayToString($val);
        return $val;
    }

    /**
     * Gets the precedingConferenceOrEvent
     *
     * @return array|null
     */
    public function getPrecedingConferenceOrEvent()
    {
        $val = $this->getField('precedingConferenceOrEvent', 'gnd');
        if (isset($val)) $val = $this->localizedArrayToString($val);
        return $val;
    }

    /**
     * Gets the succeedingCorporateBody
     *
     * @return array|null
     */
    public function getSucceedingCorporateBody()
    {
        $val = $this->getField('succeedingCorporateBody', 'gnd');
        if (isset($val)) $val = $this->localizedArrayToString($val);
        return $val;
    }

    /**
     * Gets the succeedingConferenceOrEvent
     *
     * @return array|null
     */
    public function getSucceedingConferenceOrEvent()
    {
        $val = $this->getField('succeedingConferenceOrEvent', 'gnd');
        if (isset($val)) $val = $this->localizedArrayToString($val);
        return $val;
    }

    /**
     * Gets the HierarchicalSuperiorOfTheCorporateBody
     *
     * @return array|null
     */
    public function getHierarchicalSuperiorOfTheCorporateBody()
    {
        $val = $this->getField('hierarchicalSuperiorOfTheCorporateBody', 'gnd');
        if (isset($val)) $val = $this->localizedArrayToString($val);
        return $val;
    }

    /**
     * Gets the RelatedConferenceOrEvent
     *
     * @return array|null
     */
    public function getRelatedConferenceOrEvent()
    {
        $val = $this->getField('relatedConferenceOrEvent', 'gnd');
        if (isset($val)) $val = $this->localizedArrayToString($val);
        return $val;
    }

    /**
     * Gets the RelatedCorporateBody
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
     * Gets the HierarchicalSuperiorOfTheConferenceOrEvent
     *
     * @return array|null
     */
    public function getHierarchicalSuperiorOfTheConferenceOrEvent()
    {
        $val = $this->getField('hierarchicalSuperiorOfTheConferenceOrEvent', 'gnd');
        if (isset($val)) $val = $this->localizedArrayToString($val);
        return $val;
    }

    /**
     * Gets the HierarchicalSuperiorOrganisation ids
     *
     * @return array|null
     */
    public function getHierarchicalSuperiorOrganisationIds()
    {
        $ids = null;
        $total = null;
        $supOrgsFields = ['gnd:hierarchicalSuperiorOfTheConferenceOrEvent','gnd:hierarchicalSuperiorOfTheCorporateBody'];
        foreach ($supOrgsFields as $supOrgsField) {
            if (array_key_exists($supOrgsField, $this->fields["_source"])) {
                $supOrgs = $this->fields["_source"][$supOrgsField];
                foreach ($supOrgs as $id) {
                    $ids[] = $id['@id'];
                }
            }
        }
        return implode(",", $ids);
    }

    /**
     * Gets the corporateBodyIsMember
     *
     * @return array|null
     */
    public function getCorporateBodyIsMember()
    {
        $val = $this->getField('corporateBodyIsMember', 'gnd');
        if (isset($val)) $val = $this->localizedArrayToString($val);
        return $val;
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
        if (isset($location)) $location = $this->localizedArrayToString($location);
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
            $country = $this->getField('P131', 'wdt');
        }
        if (isset($country)) $country = $this->localizedArrayToString($country);
        return $country;
    }

    /**
     * Gets the LegalForm
     *
     * @return array|null
     */
    public function getLegalForm()
    {
        $legalForm = $this->getField('P1454', 'wdt');
        if (isset($legalForm)) {
            $legalForm = $this->localizedArrayToString($legalForm);
        }
        return $legalForm;
    }

    /**
     * Gets the DirectorManager
     *
     * @return array|null
     */
    public function getDirectorManager()
    {
        $legalForm =  $this->getField('P1037', 'wdt');
        if (isset($legalForm)) {
            $legalForm = $this->localizedArrayToString($legalForm);
        }
        return $legalForm;
    }

    /**
     * Gets the NotableWork
     *
     * @return array|null
     */
    public function getNotableWork()
    {
        $notableWork = $this->getField('notableWork', 'dbo');
        if (isset($notableWork)) {
            $notableWork = $this->localizedArrayToString($notableWork);
        } elseif (!isset($notableWork)) {
            $notableWork = $this->getField('publication', 'gnd');
            if (isset($notableWork))$notableWork = $this->arrayToString($notableWork);
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
        return $this->formatDate($this->getField('P571', 'wdt'));
    }

    /**
     * Gets the DissolvedAbolishedDemolished
     *
     * @return array|null
     */
    public function getDissolvedAbolishedDemolished()
    {
        return $this->formatDate($this->getField('P576', 'wdt'));
    }

    /**
     * Gets the description
     *
     * @return array|null
     */
    public function getDescription()
    {
        $description =  $this->getField('description', 'schema');
        $description =  $this->getValueByLanguagePriority($description);
        if (isset($description) && is_array($description)) {
            return array_shift($description);
        }
    }

    /**
     * Gets the biographicalOrHistoricalInformation and/or definition.
     *
     * @return array|null
     */
    public function getbiographicalOrHistoricalInformation()
    {
        $val1 = $this->getField('biographicalOrHistoricalInformation', 'gnd');
        $val2 = $this->getField('definition', 'gnd');
        $val = array_merge((array)$val1, (array)$val2);
        return $val;
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
