<?php
/**
 * ESSubject.php
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
 * @author   Christoph Boehm <cbo@outermedia.de>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.vufind.org  Main Page
 */
namespace ElasticSearch\View\Helper;

/**
 * Class ESSubject
 *
 * @category VuFind
 * @package  ElasticSearch\View\Helper
 * @author   Christoph Boehm <cbo@outermedia.de>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.vufind.org  Main Page
 */
class ESSubject extends AbstractHelper
{
    /**
     * The subject
     *
     * @var
     */
    private $_subject;

    /**
     * Gets the  s the MetadataPrefix
     *
     * @return string
     */
    protected function getMetadataPrefix(): string
    {
        return 'subject.metadata';
    }

    /**
     * Gets the  s the MetadataMethodMap
     *
     * @return array
     */
    protected function getMetadataMethodMap(): array
    {
        return [
            'variants' => 'getVariantNames',
            'definition' => 'getDefinition'
        ];
    }

    /**
     * Gets the  s the Type
     *
     * @return string
     */
    public function getType(): string
    {
        return 'subject';
    }

    /**
     * Provides the type to use as search queries.
     *
     * @return string
     */
    public function getSearchType(): string
    {
        return 'Subject';
    }

    /**
     * Gets the  s the Subject
     *
     * @return \ElasticSearch\VuFind\RecordDriver\ESSubject
     */
    public function getSubject(): \ElasticSearch\VuFind\RecordDriver\ESSubject
    {
        return $this->_subject;
    }

    /**
     * Sets the  Subject
     *
     * @param \ElasticSearch\VuFind\RecordDriver\ESSubject|null $_subject The
     *                                                                    subject
     *
     * @return void
     */
    public function setSubject(
        \ElasticSearch\VuFind\RecordDriver\ESSubject $_subject = null
    ) {
        parent::setDriver($_subject);
        $this->_subject = $_subject;
    }

    /**
     * Gets the  s the DisplayName
     *
     * @return null|string
     */
    public function getDisplayName()
    {
        $name = $this->getSubject()->getName();
        return strlen($name) > 0 ? $name : null;
    }

    /**
     * Gets the  SubjectLink
     *
     * @param string $template    The template
     * @param string $routePrefix The route prefix
     *
     * @return string
     */
    public function getSubjectLink(string $template, string $routePrefix): string
    {
        $subject = $this->getSubject();
        $route = sprintf('%s-subject', $routePrefix);

        $url = $this->getView()->url($route, ['id' => $subject->getUniqueID()]);

        return sprintf($template, $url, $subject->getName());
    }

    /**
     * Provides the link to the subject based authors search result page.
     * 
     * @return string
     */
    public function getSubjectAuthorsLink(): string
    {
        return $this->getPersonsSearchLink('subject');
    }

    /**
     * Gets the  VariantNames
     *
     * @param string $delimiter The delimiter
     *
     * @return null|string
     */
    public function getVariantNames(string $delimiter = ', ')
    {
        $variants = $this->getSubject()->getVariantNameForTheSubjectHeading();

        if (is_array($variants)) {
            $variants = implode($delimiter, $variants);
        }

        return strlen($variants) > 0 ? trim($variants) : null;
    }

    /**
     * Gets the  Definition
     *
     * @return mixed|null
     */
    public function getDefinition()
    {
        $definition = $this->getSubject()->getDefinitionDisplayField();

        if (is_array($definition)) {
            $definition = count($definition) > 0 ? $definition[0] : null;
        }

        return $definition;
    }

    /**
     * Gets the  DetailPageLinkLabel
     *
     * @return string
     */
    public function getDetailPageLinkLabel()
    {
        return $this->resolveLabelWithDisplayName(
            'subject.page.link'
        );
    }

    /**
     * Gets the  MoreMediaLinkLabel
     *
     * @return string
     */
    public function getMoreMediaLinkLabel()
    {
        return $this->resolveLabelWithDisplayName(
            'subject.medias'
        );
    }

    /**
     * Gets the  MoreMediaSearchLink
     *
     * @param string $template The template
     *
     * @return string
     */
    public function getMoreMediaSearchLink(string $template)
    {
        $label = $this->getMoreMediaLinkLabel();
        $url = $this->getView()->url('search-results');
        $url = sprintf(
            '%s?lookfor=%s&type=Subject', $url,
            urlencode($this->getSubject()->getName())
        );

        return sprintf($template, $url, $label);
    }
}
