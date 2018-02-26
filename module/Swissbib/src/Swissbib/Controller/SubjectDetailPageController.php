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

use ElasticSearch\VuFind\RecordDriver\ElasticSearch;
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
class SubjectDetailPageController extends DetailPageController
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

            $bibliographicResources = $this->getBibliographicResourcesOf($info->id);
            $subjectIds = $this->getSubjectIdsFrom($bibliographicResources);
            $subjects = $this->getSubjectsOf($subjectIds);

            $this->addData(
                $viewModel, $info->id, $driver, $bibliographicResources,
                $subjectIds, $subjects
            );

            return $viewModel;
        } catch (\Exception $e) {
            return $this->createErrorView($info->id, $e);
        }
    }

    /**
     * Adds additional data to view model
     *
     * @param ViewModel     $viewModel              The view model
     * @param string        $id                     The id
     * @param ElasticSearch $driver                 The record driver
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
            "Subject", $driver, $this->config->mediaLimit
        );
        $viewModel->setVariable("medias", $medias);
    }
}
