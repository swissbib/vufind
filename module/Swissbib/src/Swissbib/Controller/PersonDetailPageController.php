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
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category VuFind
 * @package  Swissbib\Controller
 * @author   Christoph Boehm <cbo@outermedia.de>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.vufind.org  Main Page
 */
namespace Swissbib\Controller;

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
class PersonDetailPageController extends AbstractPersonController
{
    /**
     * /Page/Detail/Person/:id
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function personAction()
    {
        $viewModel = parent::personAction();
        $viewModel->setVariable(
            "references", $this->getRecordReferencesConfig()
        );

        return $viewModel;
    }

    /**
     * Adds additional data to view model
     *
     * @param ViewModel $viewModel The view model
     *
     * @return void
     */
    protected function addData(
        ViewModel &$viewModel
    ) {
        $medias = $this->solrsearch()->getMedias(
            "Author", $this->driver, $this->config->mediaLimit
        );
        $viewModel->setVariable("medias", $medias);
        $es = $this->serviceLocator->get('elasticsearchsearch');
        //issue: https://github.com/swissbib/vufind/issues/719
        //The ElasticSearchSearch Type is responsible for the check if there
        // are CoContributors available. But this methods returns a result type as
        //the last expression value which is created via a search engine request
        //so we can't return just an e.g. empty array expression.
        //The method responsible for checking the co-authors is implemented as
        // protected so not usable just out of the box. For me the most reasonable
        //implementation is to provide an additional helper function on the
        //ElasticSearch Type to get the number of available CoContributors
        //so we do not duplicate the code for this

        if ($es->hasCoContributors(
            $this->bibliographicResources,
            $this->driver->getUniqueID()
        )
        ) {
            $contributors = $this->getCoContributors(
                $this->driver->getUniqueID()
            );
        }
        if (isset($contributors)) {
            $viewModel->setVariable("coContributors", $contributors->getResults());
            $viewModel->setVariable(
                "coContributorsTotal", $contributors->getResultTotal()
            );
        }
        $personsOfSameGenre = $this->getPersonsOfSameGenre($this->driver);
        //GH: I think we can make this request without the risk of ES searches having
        //empty parameter values which throws often an exception because
        //checks if there are genres available
        if (isset($personsOfSameGenre)) {
            $viewModel->setVariable(
                "authorsOfSameGenre", $personsOfSameGenre->getResults()
            );
            $viewModel->setVariable(
                "authorsOfSameGenreTotal", $personsOfSameGenre->getResultTotal()
            );
        }

        //GH: same reason as argued for PersonsOfSameGenre - an ES request is
        //only done if movements are available
        $personsOfSameMovement = $this->getPersonsOfSameMovement($this->driver);
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
     * Gets subjects
     *
     * @return array
     */
    protected function getSubjectsOf(): array
    {
        $subjects = parent::getSubjectsOf();

        if (count($subjects) > 0) {
            return $this->tagcloud()->getTagCloud($this->subjectIds, $subjects);
        }
        return [];
    }

    /**
     * Adds co contributors of author
     *
     * @param string $id The id
     *
     * @return Results
     */
    protected function getCoContributors(string $id): Results
    {
        return $this->serviceLocator->get('elasticsearchsearch')
            ->searchCoContributorsFrom(
                $this->bibliographicResources, $id, $this->config->coAuthorsSize
            );
    }

    /**
     * Adds persons of same genre as author
     *
     * @return Results|null
     */
    protected function getPersonsOfSameGenre()
    {
        $genresIds = $this->driver->getWikidataIdentifiersForField('genre');

        if (is_array($genresIds)) {
            $genresIds = implode(',', $genresIds);
        }

        //only search the q number as this field is now indexed as text instead of keyword
        //should be adapted when https://gitlab.com/swissbib/linked/workflows/-/issues/29
        //is fixed
        $genresIds=str_replace("http://www.wikidata.org/entity/", "", $genresIds);

        $authors = null;
        if (isset($genresIds)) {
            $authors = $this->serviceLocator->get('elasticsearchsearch')
                ->searchElasticSearch(
                    $genresIds, "person_by_genre", null, null,
                    $this->config->sameGenreAuthorsSize
                );
        }

        return $authors;
    }

    /**
     * Adds persons of same movement as author
     *
     * @return Results|null
     */
    protected function getPersonsOfSameMovement()
    {
        $movements = $this->driver->getMovementDisplayField();

        if (is_array($movements)) {
            $movements = $this->arrayToSearchString($movements);
        }

        $authors = null;
        if (isset($movements)) {
            $authors = $this->serviceLocator->get('elasticsearchsearch')
                ->searchElasticSearch(
                    $movements, "person_by_movement", null, null,
                    $this->config->sameMovementAuthorsSize
                );
        }
        return $authors;
    }
}
