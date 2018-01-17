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
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA    02111-1307    USA
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
class ElasticSearch extends AbstractBase
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
     * Gets the ValueByLanguagePriority
     *
     * @param array  $content    The content
     * @param string $userLocale The user locale
     *
     * @return null
     */
    protected function getValueByLanguagePriority(
        array $content, string $userLocale = null
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
                    } else {
                        if (isset($valueArray['@language'])
                            && $valueArray['@language'] === $locale
                            && isset($valueArray['@value'])
                        ) {
                            $results[] = $valueArray['@value'];
                        }
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
}

