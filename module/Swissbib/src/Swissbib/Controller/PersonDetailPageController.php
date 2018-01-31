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
use ElasticSearch\VuFind\Search\ElasticSearch\Results;
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
        return parent::personAction();
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
        $medias = $this->solrsearch()->getMedias(
            "Author", $driver, $this->config->mediaLimit
        );
        $viewModel->setVariable("medias", $medias);
        $contributors = $this->getCoContributors(
            $driver->getUniqueID(), $bibliographicResources
        );
        if (isset($contributors)) {
            $viewModel->setVariable("coContributors", $contributors->getResults());
            $viewModel->setVariable(
                "coContributorsTotal", $contributors->getResultTotal()
            );
        }
        $personsOfSameGenre = $this->getPersonsOfSameGenre($driver);
        if (isset($personsOfSameGenre)) {
            $viewModel->setVariable(
                "authorsOfSameGenre", $personsOfSameGenre->getResults()
            );
            $viewModel->setVariable(
                "authorsOfSameGenreTotal", $personsOfSameGenre->getResultTotal()
            );
        }
        $personsOfSameMovement = $this->getPersonsOfSameMovement($driver);
        if (isset($personsOfSameMovement)) {
            $viewModel->setVariable(
                "personsOfSameMovement", $personsOfSameMovement->getResults()
            );
            $viewModel->setVariable(
                "personsOfSameMovementTotal",
                $personsOfSameMovement->getResultTotal()
            );
        }
    }

    /**
     * Adds co contributors of author
     *
     * @param string $id                     The id
     * @param array  $bibliographicResources The bibliographic resources
     *
     * @return Results
     */
    protected function getCoContributors(string $id, array $bibliographicResources
    ): Results {
        return $this->elasticsearchsearch()->searchCoContributorsFrom(
            $bibliographicResources, $id, $this->config->coAuthorsSize
        );
    }

    /**
     * Adds persons of same genre as author
     *
     * @param ESPerson $driver The driver
     *
     * @return Results|null
     */
    protected function getPersonsOfSameGenre(ESPerson $driver)
    {
        $genres = $driver->getGenre();

        if (is_array($genres)) {
            $genres = $this->arrayToSearchString($genres);
        }

        $authors = null;
        if (isset($genres)) {
            $authors = $this->elasticsearchsearch()->searchElasticSearch(
                $genres, "person_by_genre", null, null,
                $this->config->sameGenreAuthorsSize
            );
        }

        return $authors;
    }

    /**
     * Adds persons of same movement as author
     *
     * @param ESPerson $driver The driver
     *
     * @return Results|null
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
            $authors = $this->elasticsearchsearch()->searchElasticSearch(
                $movements, "person_by_movement", null, null,
                $this->config->sameMovementAuthorsSize
            );
        }
        return $authors;
    }
}
