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
     *
     * @return mixed
     */
    protected function getIdFromUrlSource(string $field)
    {
        $contributors = $this->fields["_source"][$field];
        preg_match_all(
            "/\/([\w-]+)(,+|$)/", implode(",", $contributors), $matches
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
        } else if (isset($items)) {
            return [$items];
        }
        return [];
    }
}
