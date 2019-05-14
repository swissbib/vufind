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
namespace SwissbibRdfDataApi\VuFind\RecordDriver;

/**
 * Class APIOrganisation
 *
 * @category VuFind
 * @package  SwissbibRdfDataApi\VuFind\RecordDriver
 * @author   Christoph Boehm <cbo@outermedia.de>
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.vufind.org  Main Page
 */
class APIOrganisation extends RdfDataApi
{
    /**
     * Magic function to access all fields
     *
     * @param string $name      Name of the field
     * @param array  $arguments Unused but required
     *
     * @method getHomepage()
     * @method getMbox()
     * @method getPhone()
     *
     * @return array|null
     */
    public function __call(string $name, $arguments)
    {
        $fieldName = lcfirst(substr($name, 3));
        return $this->getField($fieldName, "foaf");
    }

    /**
     * Gets the Name
     *
     * @return array|null
     */
    public function getName()
    {
        $name = $this->getField('name', 'foaf');
        if (isset($name)) {
            return $name;
        }
        return $this->getField('label', 'rdfs');
    }

    /**
     * Never true
     *
     * @return bool
     */
    public function hasSufficientData(): bool
    {
        return false;
    }
}
