<?php
/**
 * ElasticSearch.php
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

use VuFind\RecordDriver\AbstractBase;

/**
 * Class ElasticSearch
 *
 * @category VuFind
 * @package  ElasticSearch\VuFind\RecordDriver
 * @author   Christoph Boehm <cbo@outermedia.de>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.vufind.org  Main Page
 */
abstract class ElasticSearch extends AbstractBase
{
    /**
     * The source indentifier
     *
     * @var string
     */
    protected $sourceIdentifier = 'ElasticSearch';

    /**
     * Get text that can be displayed to represent this record in breadcrumbs.
     *
     * @return string Breadcrumb text to represent this record.
     */
    public function getBreadcrumb()
    {
        return $this->getName();
    }

    /**
     * The name of the person, subbject etc. Needs to be implemented in some way.
     *
     * @return string|null
     */
    abstract public function getName();

    /**
     * Indicates whether the record has sufficient data to be shown in lean record
     * views like knowledge-cards.
     *
     * @return bool
     */
    abstract public function hasSufficientData(): bool;

    /**
     * Return the unique identifier of this record for retrieving additional
     * information (like tags and user comments) from the external MySQL
     * database.
     *
     * @return string Unique identifier.
     */
    public function getUniqueID()
    {
        return $this->fields["_id"];
    }

    /**
     * Gets the Type
     *
     * @return mixed
     */
    public function getType()
    {
        return $this->fields["_type"];
    }

    /**
     * Gets the field
     *
     * @param string $name      Name of the field
     * @param string $prefix    The prefix
     * @param string $delimiter The delimiter
     *
     * @return array|null
     */
    protected function getField(
        string $name, string $prefix, string $delimiter = ':'
    ) {
        $fieldName = $this->getQualifiedFieldName($name, $prefix, $delimiter);

        return array_key_exists($fieldName, $this->fields["_source"])
            ? $this->fields["_source"][$fieldName] : null;
    }

    /**
     * Gets the qualified field name
     *
     * @param string $name      The name
     * @param string $prefix    The prefix
     * @param string $delimiter The delimiter
     *
     * @return string
     */
    protected function getQualifiedFieldName(
        string $name, string $prefix, string $delimiter
    ) {
        return sprintf('%s%s%s', $prefix, $delimiter, $name);
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
     * Returns a localized array field from the index as a string
     *
     * @param string $value     The value found in the index.
     * @param string $delimiter The delimiter join elements in the field's array.
     *
     * @return string
     */
    protected function localizedArrayToString(array $values, string $delimiter = ', ') {
        $value = $this->getArrayOfValuesByLanguagePriority($values);
        return implode($delimiter, $value);
    }

    /**
     * Returns a non localized array field from the index as a string
     *
     * @param string $value     The value found in the index.
     * @param string $delimiter The delimiter join elements in the field's array.
     *
     * @return string
     */
    protected function arrayToString(array $value, string $delimiter = ', ') {
        return implode($delimiter, $value);
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

            if (array_intersect_key(array_flip($locales), $content)) {
                foreach ($locales as $locale) {
                    if (isset($content[$locale])
                        && null !== $content[$locale]
                    ) {
                        return $content[$locale];
                    }
                }
            } else {
                foreach ($content as $contentEntry) {
                    foreach ($locales as $locale) {
                        if (isset($contentEntry[$locale])
                            && null !== $contentEntry[$locale]
                        ) {
                            return $contentEntry[$locale];
                        }
                    }
                }
            }
        }
        return null;
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
     * Render the date in a better format (based on GND Date or Wikidata Dates)
     *
     * @param null|string $dateString a string corresponding to a date
     *
     * @return string
     */
    protected function formatDate($dateString)
    {
        //Julius Cesar
        //https://test.swissbib.ch/Page/Detail/Person/e399d061-eed6-3861-bfee-04ee705e482f

        //Euclides
        //https://test.swissbib.ch/Page/Detail/Person/e5ff3fe0-2e85-3eca-8501-d61e11dc9d00

        //Bach
        //https://test.swissbib.ch/Page/Detail/Person/21517b2f-21d1-34ad-b089-c69fed0f25d4

        //Potter
        //https://test.swissbib.ch/Page/Detail/Person/76364cb3-54cb-3d49-ba6e-06ff0e20a42c

        if (null === $dateString) {
            return null;
        }

        //before Christus (display year only with BC)
        if (preg_match('/-\d\d\d\d.*/', $dateString)) {
            return substr($dateString, 1, 4) . " BC";
        }

        //wikidata style : 1929-12-06T00:00:00Z (with leading 0 for days and months)
        $date = \DateTime::createFromFormat('Y-m-d\TH:i:s\Z', $dateString);
        if ($date) {
            return $date->format('j.n.Y');
        } else {
            //gnd style is often 2012-07-25
            $date = \DateTime::createFromFormat('Y-m-d', $dateString);
            if ($date) {
                return $date->format('j.n.Y');
            } else {
                //otherwise we just return the input string
                return $dateString;
            }
        }
    }
}
