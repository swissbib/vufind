<?php
/**
 * PersonDetailPageController.php
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
 * @package  Swissbib\Controller
 * @author   Christoph Boehm <cbo@outermedia.de>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.vufind.org  Main Page
 */
namespace Swissbib\Controller;

use ElasticSearch\VuFind\RecordDriver\ElasticSearch;
use ElasticSearch\VuFind\RecordDriver\ESPerson;
use ElasticSearch\VuFind\RecordDriver\ESSubject;
use Zend\View\Model\ViewModel;

/**
 * Class PersonDetailPageController
 *
 * @category VuFind
 * @package  Swissbib\Controller
 * @author   Christoph Boehm <cbo@outermedia.de>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.vufind.org  Main Page
 */
class PersonDetailPageController extends DetailPageController
{
    /**
     * /Page/Person/Subject/:id
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function personAction()
    {
        return parent::personAction()->setTemplate("detailpage/person");
    }

    /**
     * Adds additional data to view model
     *
     * @param ViewModel     $viewModel              The view model
     * @param string        $id                     The id
     * @param ElasticSearch $driver                 The view model
     * @param array         $bibliographicResources The bibliographic resources
     * @param array         $subjectIds             The subject ids
     * @param array         $subjects               The subjects
     *
     * @return void
     */
    protected function addData(
        ViewModel &$viewModel, string $id, ElasticSearch $driver,
        array $bibliographicResources, array $subjectIds, array $subjects
    ) {
        $media = $this->getMedia("Author", $driver);
        $viewModel->setVariable("media", $media);
        $contributors = $this->getCoContributors(
            $driver, $bibliographicResources
        );
        $viewModel->setVariable("coContributors", $contributors);
        $personsOfSameGenre = $this->getPersonsOfSameGenre($driver);
        $viewModel->setVariable("authorsOfSameGenre", $personsOfSameGenre);
        $personsOfSameMovement = $this->getPersonsOfSameMovement($driver);
        $viewModel->setVariable("authorsOfSameGenre", $personsOfSameMovement);
    }

    /**
     * Adds co contributors of author
     *
     * @param ESPerson $driver                 The driver
     * @param array    $bibliographicResources The bibliographic resources
     *
     * @return array
     */
    protected function getCoContributors(
        ESPerson $driver, array $bibliographicResources
    ) {
        $authorId = $driver->getUniqueId();
        $contributorIds = [];
        // @var \ElasticSearch\VuFind\RecordDriver\ESBibliographicResource
        // $bibliographicResource
        foreach ($bibliographicResources as $bibliographicResource) {
            $contributorIds = array_merge(
                $contributorIds, $bibliographicResource->getContributors()
            );
        }
        $contributorIds = array_unique($contributorIds);

        $contributorIds = array_filter(
            $contributorIds,
            function ($val) use ($authorId) {
                return $val !== $authorId;
            }
        );

        $contributors = $this->searchElasticSearch(
            $this->arrayToSearchString($contributorIds), "id", "lsb", "person"
        );

        return $contributors;
    }

    /**
     * Adds persons of same genre as author
     *
     * @param ESPerson $driver The driver
     *
     * @return array
     */
    protected function getPersonsOfSameGenre(ESPerson $driver)
    {
        $genres = $driver->getGenre();

        if (is_array($genres)) {
            $genres = $this->arrayToSearchString($genres);
        }

        $authors = null;
        if (isset($genres)) {
            $authors = $this->searchElasticSearch($genres, "person_by_genre");
        }

        return $authors;
    }

    /**
     * Adds persons of same movement as author
     *
     * @param ESPerson $driver The driver
     *
     * @return array
     */
    protected function getPersonsOfSameMovement(ESPerson $driver)
    {
        $authorId = $driver->getUniqueId();

        $movements = $driver->getMovement();

        if (is_array($movements)) {
            $movements = $this->arrayToSearchString($movements);
        }

        $authors = null;
        if (isset($movements)) {
            $authors = $this->searchElasticSearch($movements, "person_by_movement");
            $authors = array_filter(
                $authors,
                function (ESPerson $author) use ($authorId) {
                    return $author->getUniqueID() !== $authorId;
                }
            );
        }
        return $authors;
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
                $name = $subject->getName();
                $cloud[$name] = [
                    "subject" => $subject, "count" => $count,
                    "weight" => $this->calculateFontSize($count, $max)
                ];
            }
        }

        return $cloud;
    }

    /**
     * Calculates the font size for the tag cloud
     *
     * @param int $count The count
     * @param int $max   Max count
     *
     * @return int
     */
    protected function calculateFontSize($count, $max): int
    {
        $tagCloudMaxFontSize = $this->config->tagCloudMaxFontSize;
        $tagCloudMinFontSize = $this->config->tagCloudMinFontSize;
        return round(
            ($tagCloudMaxFontSize - $tagCloudMinFontSize) * ($count / $max)
            + $tagCloudMinFontSize
        );
    }
}
