<?php
/**
 * Ajax Trait.
 *
 * PHP version 7
 *
 * Copyright (C) Villanova University 2018.
 *
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
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category VuFind
 * @package  AJAX
 * @author   Matthias Edel <matthias.edel@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://vufind.org/wiki/development Wiki
 */
namespace Swissbib\AjaxHandler;

use VuFind\View\Helper\Root\RecordDataFormatter;
use Zend\Http\Request;
use Zend\Http\Response;

/**
 * Trait AjaxTrait
 *
 * @category Swissbib_VuFind2
 * @package  Swissbib\AjaxHandler
 * @author   Matthias Edel <matthias.edel@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org
 */
trait AjaxTrait
{
    /**
     * Request
     *
     * @var request Request
     */
    protected $request;

    /**
     * Response
     *
     * @var response Response
     */
    protected $response;

    /**
     * View renderer
     *
     * @var RendererInterface
     */
    protected $renderer;

    /**
     * Get the configuration
     *
     * @return mixed
     */
    protected function getConfig()
    {
        return $this->serviceLocator->get('VuFind\Config')->get('config');
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
        $recordFormatter = $this->renderer->plugin(
            'recordDataFormatter'
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
        $response->setContent($data);
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
        $returnArray = [];
        foreach ($formattedRecord as $arrayElement) {
            $returnArray[$arrayElement['label']] = $arrayElement['value'];
        }
        $formattedRecord = $returnArray;
    }

    /**
     * Get response object
     *
     * @return Response
     */
    public function getResponse()
    {
        if (!$this->response) {
            $this->response = new Response();
        }

        return $this->response;
    }

    /**
     * Get request object
     *
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
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
        $searcher = $this->request->getQuery()['searcher'];
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
                $this->request->getQuery()->toArray() + $this->request
                    ->getPost()->toArray()
            )
        );

        $results->performAndProcessSearch();

        // @var $content array
        $content = $results->getResults();
        return $content;
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
