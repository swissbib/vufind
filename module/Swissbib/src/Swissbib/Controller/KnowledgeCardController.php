<?php
/**
 * KnowledgeCardController.php
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
use VuFind\Controller\AbstractBase;
use Zend\View\Model\ViewModel;

/**
 * Swissbib KnowledgeCardController
 *
 * Provides information to be rendered in knowledge cards (light-boxes).
 *
 * @category Swissbib_VuFind2
 * @package  Controller
 * @author   Edmund Maruhn  <ema@outermedia.de>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org
 */
class KnowledgeCardController extends AbstractBase
{
    /**
     * Person KnowledgeCard
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function personAction()
    {
        $personIndex = "lsb";
        $personType = "person";
        $id = $this->params()->fromRoute('id', []);

        try {
            $driver = $this->getInformation($id, $personIndex, $personType);

            $bibliographicResources = $this->getBibliographicResources($id);

            $subjects = $this->getSubjectsOf($bibliographicResources);

            return $this->createViewModel(
                [
                    "driver"   => $driver,
                    "subjects" => $subjects,
                    "books"    => $bibliographicResources
                ]
            );
        } catch (\Exception $e) {
            return $this->createErrorView($id);
        }
    }

    /**
     * Subject Knowledge Card
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function subjectAction()
    {
        $subjectIndex = "gnd";
        $subjectType = "DEFAULT";

        $id = "http://d-nb.info/gnd/" . $this->params()->fromRoute('id', []);

        try {
            $driver = $this->getInformation($id, $subjectIndex, $subjectType);
            $subSubjects = $this->getSubSubjects($id);
            $parentSubjects = $this->getParentSubjects($driver->getParentSubjects());

            return $this->createViewModel(
                [
                    "driver"         => $driver,
                    "subSubjects"    => $subSubjects,
                    "parentSubjects" => $parentSubjects
                ]
            );
        } catch (\Exception $e) {
            return $this->createErrorView($id);
        }
    }

    /**
     * Gets the record
     *
     * @param string $id    ID of the record
     * @param string $index Index of the record
     * @param string $type  Type of the record
     *
     * @return \ElasticSearch\VuFind\RecordDriver\ElasticSearch
     * @throws \Exception
     */
    protected function getRecord(string $id, string $index, string $type): ElasticSearch
    {
        $content = $this->search(
            $id,
            "id",
            $index,
            $type
        );

        if ($content !== null && is_array($content) && count($content) === 1) {
            return $content[0];
        }
        throw new \Exception("Found no data for id " . $id);
    }

    /**
     * GetBibliographicResources
     *
     * @param string|\Swissbib\Controller\ID $id ID of the record
     *
     * @return array
     */
    protected function getBibliographicResources(string $id): array
    {
        return $this->search(
            "http://data.swissbib.ch/person/" . $id,
            "bibliographicResources_by_author",
            "lsb",
            "bibliographicResource"
        );
    }

    /**
     * GetSubjectsOf
     *
     * @param array $bibliographicResources Array of ESBibliographicResource
     *
     * @return array
     */
    protected function getSubjectsOf(array $bibliographicResources)
    {
        $ids = [];
        // @var ESBibliographicResource $bibliographicResource
        foreach ($bibliographicResources as $bibliographicResource) {
            $s = $bibliographicResource->getSubjects();
            if (count($s) > 0) {
                $ids = array_merge($ids, $s);
            }
        }
        $ids = array_unique($ids);

        return $this->search(
            $this->arrayToSearchString($ids),
            "id",
            "gnd",
            "DEFAULT"
        );
    }

    /**
     * GetSubSubjects
     *
     * @param string $id ID of record
     *
     * @return array
     */
    protected function getSubSubjects(string $id)
    {
        return $this->search($id, "sub_subjects");
    }

    /**
     * GetParentSubjects
     *
     * @param array $ids IDs of parent subjects
     *
     * @return array
     */
    protected function getParentSubjects(array $ids)
    {
        return $this->search(
            $this->arrayToSearchString($ids),
            "id",
            "gnd",
            "DEFAULT"
        );
    }

    /**
     * Executes the search
     *
     * @param string        $q        The query string
     * @param string        $template Template from config
     * @param string | null $index    Index to search in. Not required, if defined in template.
     * @param string | null $type     Type to search for. Not required, if defined in template.
     *
     * @return array
     */
    protected function search(string $q, string $template, $index = null, $type = null): array
    {
        $manager = $this->serviceLocator->get('VuFind\SearchResultsPluginManager');
        // @var Results
        $results = $manager->get("ElasticSearch");

        // @var Params
        $params = $results->getParams();

        if (isset($index)) {
            $params->setIndex($index);
        }
        $params->setTemplate($template);

        // @var Query $query
        $query = $params->getQuery();
        if (isset($type)) {
            $query->setHandler($type);
        }
        $query->setString($q);

        $results->performAndProcessSearch();

        // @var $content array
        $content = $results->getResults();

        return $content;
    }

    /**
     * CreateErrorView
     *
     * @param string $id Id of record
     *
     * @return \Zend\View\Model\ViewModel
     */
    protected function createErrorView(string $id): ViewModel
    {
        $model = new ViewModel(
            [
                'message' => 'Can not find a Knowledge Card for id: ' . $id,
            ]
        );
        $model->setTemplate('error/index');
        return $model;
    }

    /**
     * Converts array to search string
     *
     * @param array $ids Array of ids
     *
     * @return string
     */
    protected function arrayToSearchString(array $ids): string
    {
        return '[' . implode(",", $ids) . ']';
    }
}
