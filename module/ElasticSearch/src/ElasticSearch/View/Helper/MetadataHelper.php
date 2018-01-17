<?php
/**
 * MetadataHelper.php
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
 * @package  ElasticSearch\View\Helper
 * @author   Edmund Maruhn <ema@outermedia.de>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.vufind.org  Main Page
 */
namespace ElasticSearch\View\Helper;

use Zend\View\Helper\AbstractHelper;

/**
 * Class MetadataHelper
 *
 * @category VuFind
 * @package  ElasticSearch\View\Helper
 * @author   Edmund Maruhn <ema@outermedia.de>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.vufind.org  Main Page
 */
class MetadataHelper extends AbstractHelper
{
    /**
     * The source
     *
     * @var
     */
    private $_source;

    /**
     * Get Source
     *
     * @return mixed
     */
    public function getSource()
    {
        return $this->_source;
    }

    /**
     * Sets Source
     *
     * @param \Zend\View\Helper\AbstractHelper $_source The source
     *
     * @return void
     */
    public function setSource(AbstractHelper $_source)
    {
        $this->_source = $_source;
    }

    /**
     * The prefix
     *
     * @var
     */
    private $_prefix;

    /**
     * Gets the prefix
     *
     * @return mixed
     */
    public function getPrefix()
    {
        return $this->_prefix;
    }

    /**
     * Sets the prefix
     *
     * @param string $_prefix The prefix
     *
     * @return void
     */
    public function setPrefix(string $_prefix)
    {
        $this->_prefix = $_prefix;
    }

    /**
     * The metadata method map
     *
     * @var
     */
    private $_metadataMethodMap;

    /**
     * Gets the MetadataMethodMap
     *
     * @return mixed
     */
    public function getMetadataMethodMap()
    {
        return $this->_metadataMethodMap;
    }

    /**
     * Sets the MetadataMethodMap
     *
     * @param array $map The MetadataMethodMap
     *
     * @return void
     */
    public function setMetadataMethodMap(array $map)
    {
        $this->_metadataMethodMap = $map;
    }

    /**
     * Gets the List
     *
     * @param string[] ...$keys The keys
     *
     * @return array
     */
    public function getList(string ...$keys)
    {
        $metadataList = [];

        foreach ($keys as $key) {
            $entry = $this->_getMetadataListEntry($key);

            if (!is_null($entry)) {
                $metadataList[] = $entry;
            }
        }

        return $metadataList;
    }

    /**
     * Gets the  MetadataListEntry
     *
     * @param string $key The key
     *
     * @return array|null
     */
    private function _getMetadataListEntry(string $key)
    {
        $entry = null;

        if (isset($this->_metadataMethodMap[$key])) {
            $method = $this->_metadataMethodMap[$key];
            $value = $this->getSource()->{$method}();

            if (!is_null($value)) {
                $translationKey = sprintf('%s.%s', $this->getPrefix(), $key);
                $entry = [
                    'label'    => $this->getView()->translate($translationKey),
                    'value'    => $value,
                    'cssClass' => $key
                ];
            }
        }

        return $entry;
    }

}
