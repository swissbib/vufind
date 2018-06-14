<?php
/**
 * Swissbib / VuFind: enhancements for AjaxController in Swissbib module
 *
 * PHP version 5
 *
 * Copyright (C) project swissbib, University Library Basel, Switzerland
 * http://www.swissbib.org  / http://www.swissbib.ch / http://www.ub.unibas.ch
 *
 * Date: 1/2/13
 * Time: 4:09 PM
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License version 2,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @category Swissbib_VuFind2
 * @package  Controller
 * @author   Guenter Hipler  <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.swissbib.org
 */
namespace Swissbib\Controller;

use VuFind\Controller\AjaxController as VFAjaxController;
use VuFind\View\Helper\Root\RecordDataFormatter;
use Zend\Stdlib\ResponseInterface;

/**
 * Swissbib / VuFind: enhancements for AjaxController in Swissbib module
 *
 * @category Swissbib_VuFind2
 * @package  Controller
 * @author   Guenter Hipler  <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org
 */
class AjaxController extends VFAjaxController
{
    /**
     * Utility function for clients to control the workflow with shibboleth
     * login we can't login in popup dialogs (makes it to complex if at all
     * possible)
     *
     * @return \Zend\Http\Response
     */
    public function shibloginAction()
    {
        $this->outputMode = 'json';
        $config = $this->getConfig();
        if ((!isset($config->Mail->require_login) || $config->Mail->require_login)
            && strcmp(
                strtolower(
                    $config->Authentication->method
                ), "shibboleth"
            ) == 0
            && !$this->getUser()
        ) {
            //no JSON.parse in client
            return $this->output(
                //json_encode(array("useshib" => true)), self::STATUS_OK
                "true", self::STATUS_OK
            );
        } else {
            return $this->output(
                "false", self::STATUS_OK
            );
        }
    }

    /**
     * Gets Authors by id. Expects comma separated ids.
     *
     * @return \Zend\Stdlib\ResponseInterface
     */
    protected function getCoAuthorsAjax()
    {
        $id = $this->getRequest()->getQuery()['person'] ?? "";
        $page = $this->getRequest()->getQuery()['page'] ?? 1;
        $pageSize = $this->getRequest()->getQuery()['size'] ??
            $this->getConfig()->DetailPage->coAuthorsSize;

        $authors = $this->elasticsearchsearch()->searchCoContributorsOfPerson(
            $id, $pageSize, $this->getConfig()->DetailPage->searchSize, $page
        )->getResults();

        return $this->buildResponse($authors, $this->getAuthorPaginationSpec());
    }

    /**
     * Gets authors by genre supports pagination
     *
     * @return \Zend\Stdlib\ResponseInterface
     */
    protected function getSameGenreAuthorsAjax()
    {
        $genre = $this->getRequest()->getQuery()['genre'] ?? "";
        $genre =  "[" . urldecode($genre) . "]";
        $page = $this->getRequest()->getQuery()['page'] ?? 1;
        $pageSize = $this->getRequest()->getQuery()['size'] ??
            $this->getConfig()->DetailPage->sameGenreAuthorsSize;

        $authors = $this->elasticsearchsearch()->searchElasticSearch(
            $genre, "person_by_genre", null, null, $pageSize, $page ?? 1
        )->getResults();

        return $this->buildResponse($authors, $this->getAuthorPaginationSpec());
    }

    /**
     * Gets authors by movement supports pagination
     *
     * @return \Zend\Stdlib\ResponseInterface
     */
    protected function getSameMovementAuthorsAjax()
    {
        $movement = $this->getRequest()->getQuery()['movement'] ?? "";
        $movement = "[" . urldecode($movement) . "]";
        $page = $this->getRequest()->getQuery()['page'] ?? 1;
        $pageSize = $this->getRequest()->getQuery()['size'] ??
            $this->getConfig()->DetailPage->sameMovementAuthorsSize;

        $authors = $this->elasticsearchsearch()->searchElasticSearch(
            $movement, "person_by_movement", null, null, $pageSize, $page ?? 1
        )->getResults();

        return $this->buildResponse($authors, $this->getAuthorPaginationSpec());
    }

    /**
     * Gets authors by subject, supports pagination
     *
     * @return \Zend\Stdlib\ResponseInterface
     */
    protected function getSubjectAuthorsAjax()
    {
        $id = $this->getRequest()->getQuery()['subject'] ?? "";
        $page = $this->getRequest()->getQuery()['page'] ?? 1;
        $pageSize = $this->getRequest()->getQuery()['size'] ??
            $this->getConfig()->DetailPage->coAuthorsSize;

        $authors = $this->elasticsearchsearch()->searchContributorsOfSubject(
            $id, $pageSize, $this->getConfig()->DetailPage->searchSize, $page
        )->getResults();

        return $this->buildResponse($authors, $this->getAuthorPaginationSpec());
    }

    /**
     * Get Subjects
     *
     * @return \Zend\Stdlib\ResponseInterface
     */
    protected function getSubjectsAjax(): ResponseInterface
    {
        $content = $this->search();

        // TODO externalize spec
        $specBuilder = new RecordDataFormatter\SpecBuilder();
        $specBuilder->setLine(
            "id", "getUniqueID", "Simple", ['allowZero' => false]
        );
        $specBuilder->setLine(
            "type", "getType", "Simple", ['allowZero' => false]
        );
        $specBuilder->setLine(
            "name", "getName", "Simple", ['allowZero' => false]
        );
        $specBuilder->setLine(
            "hasSufficientData", "hasSufficientData", "Simple",
            ['allowZero' => false]
        );
        $spec = $specBuilder->getArray();

        $response = $this->buildResponse($content, $spec);
        return $response;
    }

    /**
     * Gets organisations
     *
     * @return \Zend\Stdlib\ResponseInterface
     */
    protected function getOrganisationsAjax(): ResponseInterface
    {
        $content = $this->search();

        // TODO externalize spec
        $specBuilder = new RecordDataFormatter\SpecBuilder();
        $specBuilder->setLine(
            "id", "getUniqueID", "Simple", ['allowZero' => false]
        );
        $specBuilder->setLine(
            "type", "getType", "Simple", ['allowZero' => false]
        );
        $specBuilder->setLine(
            "name", "getName", "Simple", ['allowZero' => false]
        );
        $specBuilder->setLine(
            "hasSufficientData", "hasSufficientData", "Simple",
            ['allowZero' => false]
        );
        $spec = $specBuilder->getArray();

        $response = $this->buildResponse($content, $spec);
        return $response;
    }

    /**
     * Get Authors
     *
     * @return \Zend\Stdlib\ResponseInterface
     */
    protected function getAuthorsAjax(): ResponseInterface
    {
        $content = $this->search();

        // TODO externalize spec
        $specBuilder = new RecordDataFormatter\SpecBuilder();
        $specBuilder->setLine(
            "id", "getUniqueID", "Simple", ['allowZero' => false]
        );
        $specBuilder->setLine(
            "type", "getType", "Simple", ['allowZero' => false]
        );
        $specBuilder->setLine(
            "name", "getName", "Simple", ['allowZero' => false]
        );
        $specBuilder->setLine(
            "firstName", "getFirstName", "Simple", ['allowZero' => false]
        );
        $specBuilder->setLine(
            "lastName", "getlastName", "Simple", ['allowZero' => false]
        );
        $specBuilder->setLine(
            "hasSufficientData", "hasSufficientData", "Simple",
            ['allowZero' => false]
        );
        $spec = $specBuilder->getArray();

        $response = $this->buildResponse($content, $spec);
        return $response;
    }

    /**
     * Get Bibliographic Resource Ajax
     *
     * @return \Zend\Stdlib\ResponseInterface
     */
    protected function getBibliographicResourceAjax(): ResponseInterface
    {
        $content = $this->search();

        // TODO externalize spec
        $specBuilder = new RecordDataFormatter\SpecBuilder();
        $specBuilder->setLine(
            "persons", "getContributingPersons", "Simple",
            ['allowZero' => true, 'separator' => ',']
        );
        $specBuilder->setLine(
            "organisations", "getContributingOrganisations", "Simple",
            ['allowZero' => true, 'separator' => ',']
        );
        $spec = $specBuilder->getArray();

        $response = $this->buildResponse($content, $spec);
        return $response;
    }

    /**
     * Search
     *
     * @param array $searchOptions Search Options
     *
     * @return array
     */
    protected function search(array $searchOptions = []): array
    {
        $manager = $this->serviceLocator->get(
            'VuFind\Search\Results\PluginManager'
        );
        $searcher = $this->getRequest()->getQuery()['searcher'];
        /*
         * @var Results
         */
        $results = $manager->get($searcher);

        /*
         * @var Params $params
         */
        $params = $results->getParams();

        // Send both GET and POST variables to search class:
        $params->initFromRequest(
            new \Zend\Stdlib\Parameters(
                $this->getRequest()->getQuery()->toArray() + $this->getRequest()
                    ->getPost()->toArray()
            )
        );

        $results->performAndProcessSearch();

        // @var $content array
        $content = $results->getResults();
        return $content;
    }

    /**
     * Builds the Response
     *
     * @param array $content Content
     * @param array $spec    Specification
     *
     * @return \Zend\Stdlib\ResponseInterface
     */
    protected function buildResponse($content, $spec): \Zend\Stdlib\ResponseInterface
    {
        $data = [];
        // @var RecordDataFormatter $recordFormatter
        $recordFormatter = $this->getViewRenderer()->plugin(
            'RecordDataFormatter'
        );
        // @var AbstractBase $record
        foreach ($content as $record) {
            $formattedRecord = $recordFormatter->getData($record, $spec);
            $this->_format($formattedRecord);
            array_push($data, $formattedRecord);
        }
        $response = $this->getResponse();
        $response->getHeaders()->addHeaderLine(
            'Content-Type', 'application/json'
        );
        $response->getHeaders()->addHeaderLine(
            'Access-Control-Allow-Origin', '*'
        );
        $response->setContent(json_encode($data));
        return $response;
    }

    /**
     * Formats the record
     *
     * @param array $formattedRecord Formatted Record
     *
     * @return void
     */
    private function _format(&$formattedRecord)
    {
        array_walk(
            $formattedRecord,
            function (&$value, $key) {
                $value = $value['value'];
            }
        );
    }

    /**
     * Get the spec for author pagination
     *
     * @return array
     */
    protected function getAuthorPaginationSpec(): array
    {
        $specBuilder = new RecordDataFormatter\SpecBuilder();
        $specBuilder->setLine(
            "id", "getUniqueID", "Simple", ['allowZero' => false]
        );
        $specBuilder->setLine(
            "name", "getName", "Simple", ['allowZero' => false]
        );
        $specBuilder->setLine(
            "displayName", "getName", "RecordHelper",
            ['allowZero' => false, 'helperMethod' => 'getDisplayName']
        );
        $specBuilder->setLine(
            "thumbnail", "getThumbnail", "RecordHelper",
            ['allowZero' => false, 'helperMethod' => 'getThumbnailFromRecord']
        );
        $specBuilder->setLine(
            "sufficientData", "hasSufficientData", "RecordHelper",
            ['allowZero' => false, 'helperMethod' => 'hasSufficientData']
        );
        return $specBuilder->getArray();
    }
}
