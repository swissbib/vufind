<?php
/**
 * AbstractHelper.php
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

use ElasticSearch\VuFind\RecordDriver\ElasticSearch;
use VuFind\Search\Base\Results;

/**
 * Class AbstractHelper
 *
 * Abstract view helper that implements some utilities commonly required for
 * several views.
 *
 * @category VuFind
 * @package  ElasticSearch\View\Helper
 * @author   Edmund Maruhn <ema@outermedia.de>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.vufind.org  Main Page
 */
abstract class AbstractHelper extends \Zend\View\Helper\AbstractHelper
{
    private $_driver;

    /**
     * Returns driver
     *
     * @return ElasticSearch
     */
    public function getDriver()
    {
        return $this->_driver;
    }

    /**
     * Sets driver
     *
     * @param ElasticSearch $_driver The driver
     *
     * @return void
     */
    protected function setDriver(ElasticSearch $_driver = null)
    {
        $this->_driver = $_driver;
    }

    /**
     * The name of the underlying record driver to be rendered.
     *
     * @return string|null
     */
    abstract public function getDisplayName();

    /**
     * The type of data this helper handles. Used to resolve type specific urls
     * like for the detail page link.
     *
     * @return string
     */
    abstract public function getType(): string;

    /**
     * Resolves Label With Display Name
     *
     * @param string $translationKeyBase Translation Key Base
     *
     * @return string
     */
    public function resolveLabelWithDisplayName(string $translationKeyBase)
    {
        $displayName = $this->getDisplayName();
        $label = null;

        if (is_null($displayName)) {
            $label = $this->getView()->translate(
                sprintf(
                    '%s.no.name', $translationKeyBase
                )
            );
        } else {
            $label = $this->getView()->translate($translationKeyBase);
            $label = sprintf($label, $displayName);
        }

        return $label;
    }

    private $_metadataHelper;

    /**
     * Returns metadata
     *
     * @return MetadataHelper
     */
    public function getMetadata(): MetadataHelper
    {
        if (is_null($this->_metadataHelper)) {
            $this->_metadataHelper = new MetadataHelper();
            $this->_metadataHelper->setSource($this);
            $this->_metadataHelper->setView($this->getView());
            $this->_metadataHelper->setPrefix($this->getMetadataPrefix());
            $this->_metadataHelper->setMetadataMethodMap(
                $this->getMetadataMethodMap()
            );
        }

        return $this->_metadataHelper;
    }

    /**
     * Template method subclasses may override to provide a prefix for
     * localized
     * labels for a specific purpose. It will be set on the metadata view
     * helper.
     *
     * @return string
     */
    protected function getMetadataPrefix(): string
    {
        return '';
    }

    /**
     * Template method subclasses may override to provide an array that maps
     * metadata keys on methods on this helper. It will be set on the metadata
     * view helper. Then you can call the MetadataViewHelper#getMetadataList()
     * method with the keys of this array to retrieve these metadata
     * information.
     *
     * @return array
     */
    protected function getMetadataMethodMap(): array
    {
        return [];
    }

    /**
     * Converts the field specified by $name into a string. Fields are expected to be
     * an array of values which will be joined by the given $delimiter. In case the
     * field is either null, an empty array or an empty string, null will be the
     * result.
     * This method is useful for fields which contain ready to use lists of strings
     * like the localized display fields.
     *
     * @param string $name      The name of the field to convert into a string.
     * @param string $delimiter The delimiter join elements in the field's array.
     *
     * @return string
     */
    protected function fieldToString(string $name, string $delimiter)
    {
        $field = 'get' . ucfirst($name);
        $value = $this->getDriver()->{$field}();

        if (is_string($value) && strlen($value) > 0) {
            $value = [$value];
        }

        $available = (is_array($value) && count($value) > 0);

        return $available ? $this->escape(implode($delimiter, $value)) : null;
    }

    /**
     * Provides the localized label for the link on the detail page of the
     * underlying managed record driver. The method is used by the
     * getDetailPageLink() method for link generation.
     *
     * @return string
     */
    abstract protected function getDetailPageLinkLabel();

    /**
     * Returns Link to detail page
     * If not null it is treated as the localization key and will be resolved
     * before it is merged into the template.
     *
     * @param string      $template The template
     * @param string|null $label    The label
     *
     * @return string
     */
    public function getDetailPageLink(string $template, string $label = null
    ): string {
        $label = is_null($label) ? $this->getDetailPageLinkLabel()
            : $this->getView()->translate($label);

        $route = sprintf('page-detail-%s', $this->getType());
        $segments = ['id' => $this->getDriver()->getUniqueID()];
        $url = $this->getView()->url($route, $segments);

        return sprintf($template, $url, $label);
    }

    /**
     * Generates a label string with counting information based on the given results.
     *
     * @param \VuFind\Search\Base\Results $results The results to use for retrieving
     *                                             counting information.
     *
     * @return string
     */
    public function getResultsCountingInfoLabel(Results $results)
    {
        $total = $results->getResultTotal();
        $loaded = count($results->getResults());
        $first = $loaded > 0 ? 1 : 0;
        $template = $this->getView()->translate('page.detail.media.list.hits');

        return $this->escape(sprintf($template, $first, $loaded, $total));
    }

    /**
     * Shortcut to HTML-escape the given string using the escapeHtml() Escaper from
     * the connected view.
     *
     * @param string $value The value to HTML-escape
     *
     * @return string|null
     */
    protected function escape(string $value = null)
    {
        return is_null($value) ? null : $this->getView()->escapeHtml($value);
    }
}
