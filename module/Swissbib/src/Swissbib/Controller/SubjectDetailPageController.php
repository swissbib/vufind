<?php
/**
 * SubjectDetailPageController.php
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

use Zend\View\Model\ViewModel;

/**
 * Class SubjectDetailPageController
 *
 * @category VuFind
 * @package  Swissbib\Controller
 * @author   Christoph Boehm <cbo@outermedia.de>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.vufind.org  Main Page
 */
class SubjectDetailPageController extends AbstractSubjectController
{
    /**
     * /Page/Detail/Subject/:id
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function subjectAction()
    {
        $viewModel = parent::subjectAction();

        if (!isset($viewModel->exception)) {
            // in case parent class implementation did not generate an error already
            $viewModel = $this->extendViewModel($viewModel);
            $viewModel->setVariable(
                "references", $this->getRecordReferencesConfig()
            );
        }

        return $viewModel;
    }

    /**
     * Extends the view model by media data if possible.
     *
     * @param \Zend\View\Model\ViewModel $viewModel The view model to extend.
     *
     * @return \Zend\View\Model\ViewModel
     * Either the extended model or a new view model in case an exception occurred.
     */
    protected function extendViewModel(ViewModel $viewModel): ViewModel
    {
        $info = $this->getSubjectInfo();

        try {
            // access the driver from the view model directly instead of triggering
            // complex resolution jobs again
            $driver = $viewModel->driver;

            $this->bibliographicResources
                = $this->getBibliographicResourcesOf($this->driver->getUniqueID());
            $this->subjectIds = $this->getSubjectIdsFrom();
            $this->subjects = $this->getSubjectsOf();

            $this->addData(
                $viewModel
            );

            return $viewModel;
        } catch (\Exception $e) {
            return $this->createErrorView($info->id, $e);
        }
    }

    /**
     * Adds additional data to view model
     *
     * @param ViewModel $viewModel The view model
     *
     * @return void
     */
    protected function addData(ViewModel &$viewModel)
    {
        $medias = $this->solrsearch()->getMedias(
            "Subject", $this->driver, $this->config->mediaLimit
        );
        $viewModel->setVariable("medias", $medias);

        $parentSubjectsMedias = $this->solrsearch()->getMedias(
            "Subject", $this->parentSubjects, $this->config->mediaLimit
        );
        $viewModel->setVariable("parentMedias", $parentSubjectsMedias);

        $subSubjectsMedias = $this->solrsearch()->getMedias(
            "Subject", $this->subSubjects, $this->config->mediaLimit
        );
        $viewModel->setVariable("childrenMedias", $subSubjectsMedias);

        $relatedTermsIds = $this->driver->getRelatedTerm();
        if (isset($relatedTermsIds)) {
            $relatedTermsIds = is_array($relatedTermsIds)
                ? $this->arrayToSearchString($relatedTermsIds) : $relatedTermsIds;
            $relatedTerms = $this->elasticsearchsearch()->searchElasticSearch(
                $relatedTermsIds, "id", "gnd", "DEFAULT", 100
            )->getResults();
            $viewModel->setVariable("relatedTerms", $relatedTerms);
        }
        $personIds = $this->getContributorsIdsFrom();
        if (isset($personIds)) {
            $viewModel->setVariable("personsTotal", count($personIds));
        }
    }

    /**
     * Gets the subject ids from the bibliographic resources
     *
     * @return array
     */
    protected function getContributorsIdsFrom(): array
    {
        $ids = [];
        // @var ESBibliographicResource $bibliographicResource
        foreach ($this->bibliographicResources as $bibliographicResource) {
            $persons = $bibliographicResource->getContributors();
            if (count($persons) > 0) {
                $ids = array_merge($ids, $persons);
            }
        }
        return array_unique($ids);
    }
}
