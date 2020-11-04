<?php
/**
 * AbstractSubjectController.php
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
 * @package  Swissbib\Controller
 * @author   Christoph Boehm <cbo@outermedia.de>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.vufind.org  Main Page
 */
namespace Swissbib\Controller;

use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * Class AbstractPersonAction
 *
 * @category VuFind
 * @package  Swissbib\Controller
 * @author   Christoph Boehm <cbo@outermedia.de>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.vufind.org  Main Page
 */
abstract class AbstractSubjectController extends AbstractDetailsController
{
    /**
     * The subject record driver
     *
     * @var \ElasticSearch\VuFind\RecordDriver\ESSubject
     */
    protected $driver;
    protected $bibliographicResources;

    /**
     * DetailPageController constructor.
     *
     * @param \Laminas\ServiceManager\ServiceLocatorInterface $sm Service locator
     */
    public function __construct(ServiceLocatorInterface $sm)
    {
        parent::__construct($sm);
        $this->config = $this->getConfig()->DetailPage;
    }

    /**
     * The subject action
     *
     * @return \Laminas\View\Model\ViewModel
     */
    public function subjectAction()
    {
        $info = $this->getSubjectInfo();

        try {
            $this->driver = $this->getRecordDriver(
                $info->id, $info->index, $info->type
            );

            //Todo - id-issue:
            //we have to check if there are IDS available send to ES -
            // otherwise empty ID lists will throw an error
            $this->subSubjects = $this->getSubSubjects($info->id);
            $tParentSubj = $this->driver->getParentSubjects();
            if (null !== $tParentSubj
                && is_array($tParentSubj)
                && count($tParentSubj) > 0
            ) {
                $this->parentSubjects = $this->getParentSubjects(
                    $tParentSubj
                );
            } else {
                $this->parentSubjects = [];
            }

            return $this->createViewModel(
                [
                    "driver" => $this->driver, "parents" => $this->parentSubjects,
                    "children" => $this->subSubjects
                ]
            );
        } catch (\Exception $e) {
            return $this->createErrorView($info->id, $e);
        }
    }

    /**
     * Provides an object with index, type and id for a subject.
     *
     * @return \stdClass
     */
    protected function getSubjectInfo(): \stdClass
    {
        $prefix = 'https://d-nb.info/gnd/';

        return (object)[
            'index' => "lsb", 'type' => "subject",
            'id' => $prefix . $this->params()->fromRoute('id', [])
        ];
    }

    /**
     * Gets the SubSubjects
     *
     * @param string $id The id
     *
     * @return array
     */
    protected function getSubSubjects(string $id)
    {
        return $this->serviceLocator->get('elasticsearchsearch')
            ->searchElasticSearch(
                $id,
                "sub_subjects",
                null,
                "subject",
                $this->config->subjectsSize
            )->getResults();
    }

    /**
     * Gets the ParentSubjects
     *
     * @param array $ids Array of ids
     *
     * @return array
     */
    protected function getParentSubjects(array $ids)
    {
        return $this->serviceLocator->get('elasticsearchsearch')
            ->searchElasticSearch(
                $this->arrayToSearchString($ids),
                "id",
                null,
                "subject",
                $this->config->subjectsSize
            )->getResults();
    }

    /**
     * Gets the  BibliographicResources
     *
     * @param string $id The id of the subject
     *
     * @return array
     */
    protected function getBibliographicResourcesOf(string $id): array
    {
        $searchSize = $this->config->searchSize;
        return $this->serviceLocator->get('elasticsearchsearch')
            ->searchBibliographiResourcesOfSubject($id, $searchSize);
    }
}
