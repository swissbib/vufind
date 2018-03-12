<?php
/**
 * ESSubjectCollection.php
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
 * Class ESSubjectCollection
 *
 * @category VuFind
 * @package  ElasticSearch\View\Helper
 * @author   Edmund Maruhn <ema@outermedia.de>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.vufind.org  Main Page
 */
class ESSubjectCollection extends AbstractHelper
{
    /**
     * Array of subjects
     *
     * @var \ElasticSearch\VuFind\RecordDriver\ESSubject[]
     */
    private $_collection;

    /**
     * Gets the Collection
     *
     * @return array
     */
    public function getCollection(): array
    {
        return $this->_collection;
    }

    /**
     * Sets the Collection
     *
     * @param array $collection The collection
     *
     * @return void
     */
    public function setCollection(array $collection)
    {
        $this->_collection = $collection;
    }

    /**
     * Has SubjectsInCollection
     *
     * @return bool
     */
    public function hasSubjectsInCollection()
    {
        return isset($this->_collection) && count($this->_collection) > 0;
    }

    /**
     * Gets the SubjectCollectionLinkList
     *
     * @param string $template    The template
     * @param string $routePrefix The route prefix
     * @param string $separator   The seperator
     *
     * @return string
     */
    public function getSubjectCollectionLinkList(
        string $template, string $routePrefix, string $separator = ', '
    ): string {
        $helper = $this->getSubjectHelper();
        $subjects = [];

        foreach ($this->_collection as $subject) {
            $helper->setSubject($subject);
            $subjects[] = $helper->getSubjectLink($template, $routePrefix);
        }

        $helper->setSubject(null);

        return implode($separator, $subjects);
    }

    /**
     * Indicates whether the collection contains any items.
     *
     * @return bool
     */
    public function hasItems(): bool
    {
        return isset($this->_collection) ? count($this->_collection) > 0 : false;
    }

    /**
     * The subject helper
     *
     * @var
     */
    private $_subjectHelper;

    /**
     * Gets the SubjectHelper
     *
     * @return \ElasticSearch\View\Helper\ESSubject
     */
    protected function getSubjectHelper(): ESSubject
    {
        $this->_subjectHelper = $this->_subjectHelper ?? new ESSubject();
        $this->_subjectHelper->setView($this->getView());
        return $this->_subjectHelper;
    }
}
