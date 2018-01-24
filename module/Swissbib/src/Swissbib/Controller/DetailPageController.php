<?php
/**
 * DetailPageController.php
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
 * @package  Controller
 * @author   Christoph Boehm <cbo@outermedia.de>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.vufind.org  Main Page
 */
namespace Swissbib\Controller;

use ElasticSearch\VuFind\RecordDriver\ElasticSearch;
use ElasticSearch\VuFind\RecordDriver\ESSubject;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\View\Model\ViewModel;

/**
 * Class DetailPageController
 *
 * @category VuFind
 * @package  Swissbib\Controller
 * @author   Christoph Boehm <cbo@outermedia.de>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.vufind.org  Main Page
 */
abstract class DetailPageController extends AbstractDetailsController
{
    /**
     * The config for the detail page
     *
     * @var \Zend\Config\Config $config The Config
     */
    protected $config;

    /**
     * DetailPageController constructor.
     *
     * @param \Zend\ServiceManager\ServiceLocatorInterface $sm Service locator
     */
    public function __construct(ServiceLocatorInterface $sm)
    {
        parent::__construct($sm);
        $this->config = $this->serviceLocator->get('VuFind\Config')->get(
            'config'
        )->DetailPage;
    }

    /**
     * Retrieves list of media by query
     *
     * @param string $query The author
     * @param string $type  The type
     *
     * @return mixed
     */
    public function searchSolr(string $query, string $type): array
    {
        // Set up the search:
        $searchClassId = "Solr";

        // @var \Swissbib\VuFind\Search\Solr\Results $results
        $results = $this->getResultsManager()->get($searchClassId);

        // @var \Swissbib\VuFind\Search\Solr\Params $params
        $params = $results->getParams();
        $params->setBasicSearch($query, $type);
        $params->setLimit($this->config->mediaLimit);

        // Attempt to perform the search; if there is a problem, inspect any Solr
        // exceptions to see if we should communicate to the user about them.
        try {
            // Explicitly execute search within controller -- this allows us to
            // catch exceptions more reliably:
            $results->performAndProcessSearch();
        } catch (\VuFindSearch\Backend\Exception\BackendException $e) {
            if ($e->hasTag('VuFind\Search\ParserError')) {
                // We need to create and process an "empty results" object to
                // ensure that recommendation modules and templates behave
                // properly when displaying the error message.
                $results = $this->getResultsManager()->get('EmptySet');
                $results->setParams($params);
                $results->performAndProcessSearch();
            } else {
                throw $e;
            }
        }

        return $results->getResults();
    }

    /**
     * Gets subjects
     *
     * @param array $subjectIds Ids of subjects
     *
     * @return array
     */
    protected function getSubjectsOf(array $subjectIds): array
    {
        $subjects = parent::getSubjectsOf($subjectIds);

        if (count($subjects) > 0) {
            return $this->getTagCloud($subjectIds, $subjects);
        }

        return [];
    }

    /**
     * Adds media of author to ViewModel
     *
     * @param string        $type   The type (Author or Subject)
     * @param ElasticSearch $record The record
     *
     * @return array
     */
    protected function getMedias(string $type, ElasticSearch $record)
    {
        $name = $record->getName();
        $results = $this->searchSolr($name, $type);
        return $results;
    }

    /**
     * Convenience method for accessing results
     *
     * @return \VuFind\Search\Results\PluginManager
     */
    protected function getResultsManager()
    {
        return $this->serviceLocator->get('VuFind\SearchResultsPluginManager');
    }

    /**
     * Gets the Tagcloud
     *
     * @param array $subjectIds All subject ids, including duplicates
     * @param array $subjects   All subjects
     *
     * @return array
     */
    protected function getTagCloud(array $subjectIds, array $subjects)
    {
        $counts = array_count_values($subjectIds);
        $cloud = [];
        $max = max($counts);

        foreach ($counts as $id => $count) {
            $filtered = array_filter(
                $subjects,
                function (ESSubject $item) use ($id) {
                    return $item->getFullUniqueID() === $id;
                }
            );
            if (count($filtered) > 0) {
                // @var ESSubject $subject
                $subject = array_shift($filtered);

                $subject->setAbsoluteFrequency($count);
                $subject->setRelativeFrequency(
                    $this->calculateFontSize($count, $max)
                );

                $cloud[] = $subject;

            }
        }

        return $cloud;
    }

    /**
     * Calculates the font sizes for the tag cloud
     *
     * @param int $count The count
     * @param int $max   Max count
     *
     * @return float
     */
    protected function calculateFontSize($count, $max): float
    {
        $tagCloudMaxFontSize = $this->config->tagCloudMaxFontSize;
        $tagCloudMinFontSize = $this->config->tagCloudMinFontSize;

        $delta = $tagCloudMaxFontSize - $tagCloudMinFontSize;

        return $delta * ($count / $max) + $tagCloudMinFontSize;
    }
}
