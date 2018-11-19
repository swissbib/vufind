<?php
/**
 * ESBibliographicResource.php
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
 * Class ESBibliographicResource
 *
 * @category VuFind
 * @package  ElasticSearch\VuFind\RecordDriver
 * @author   Christoph Boehm <cbo@outermedia.de>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.vufind.org  Main Page
 */
class ESBibliographicResource extends ElasticSearch
{
    /**
     * Provides the name of the bibliographic resource.
     *
     * @return string
     */
    public function getName()
    {
        return $this->getTitle();
    }

    /**
     * Gets the Title
     *
     * @return string
     */
    public function getTitle(): string
    {
        return $this->getField("title");
    }

    /**
     * Gets the Contributors
     *
     * @return array
     */
    public function getContributors(): array
    {
        return $this->getIdFromUrlSource('dct:contributor');
    }

    /**
     * Gets contributing persons
     *
     * @return array
     */
    public function getContributingPersons(): array
    {
        return $this->getIdFromUrlSource('dct:contributor', "person");
    }

    /**
     * Gets contributing organisations
     *
     * @return array
     */
    public function getContributingOrganisations(): array
    {
        return $this->getIdFromUrlSource('dct:contributor', "organisation");
    }

    /**
     * Gets the Subjects
     *
     * @return array
     */
    public function getSubjects(): array
    {
        return $this->returnAsArray($this->getField("subject"));
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
        string $name, string $prefix = "dct", string $delimiter = ':'
    ) {
        return parent::getField($name, $prefix, $delimiter);
    }

    /**
     * Gets the Id from UrlSource
     *
     * @param string $field The field
     * @param string $type  person or organisation
     *
     * @return mixed
     */
    protected function getIdFromUrlSource(string $field, string $type = "")
    {
        $contributors = array_key_exists($field, $this->fields["_source"])
            ? $this->fields["_source"][$field] : null;
        if (is_array($contributors)) {
            $contributors = implode(",", $contributors);
        }
        preg_match_all(
            "/" . $type . "\/([\w-]+)(,+|$)/", $contributors, $matches
        );
        return $matches[1];
    }

    /**
     * Returns the items as Array
     *
     * @param array|string $items The items
     *
     * @return array
     */
    protected function returnAsArray($items): array
    {
        if (is_array($items)) {
            return $items;
        } else {
            if (isset($items)) {
                return [$items];
            }
        }
        return [];
    }

    /**
     * Indicates whether the record has sufficient data to be shown in lean record
     * views like knowledge-cards.
     *
     * @return bool
     */
    public function hasSufficientData(): bool
    {
        return true;
    }
}
