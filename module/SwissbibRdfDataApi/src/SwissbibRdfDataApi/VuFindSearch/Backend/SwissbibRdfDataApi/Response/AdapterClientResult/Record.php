<?php
/**
 * Record.php
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
 * @package  ElasticSearch\VuFindSearch\Backend\ElasticSearch\Response\AdapterClientResult
 * @author   Christoph Boehm <cbo@outermedia.de>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.vufind.org  Main Page
 */
// @codingStandardsIgnoreLineuse
namespace SwissbibRdfDataApi\VuFindSearch\Backend\SwissbibRdfDataApi\Response\AdapterClientResult;

use VuFindSearch\Response\RecordInterface;

/**
 * Class Record
 *
 * @category VuFind
 * @package  ElasticSearch\VuFindSearch\Backend\ElasticSearch\Response\AdapterClientResult
 * @author   Christoph Boehm <cbo@outermedia.de>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.vufind.org  Main Page
 */
class Record implements RecordInterface
{
    /**
     * Fields
     *
     * @var array
     */
    private $_fields;

    /**
     * Record constructor.
     *
     * @param array $fields The fields
     */
    public function __construct(array $fields)
    {
        $this->_fields = $fields;
    }

    /**
     * Set the source backend identifier.
     *
     * @param string $identifier Backend identifier
     *
     * @return void
     */
    public function setSourceIdentifier($identifier)
    {
        $this->source = $identifier;
    }

    /**
     * Return the source backend identifier.
     *
     * @return string
     */
    public function getSourceIdentifier()
    {
        return $this->_fields['_id'];
    }
}
