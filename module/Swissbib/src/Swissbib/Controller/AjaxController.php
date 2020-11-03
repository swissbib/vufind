<?php
/**
 * Swissbib / VuFind: enhancements for AjaxController in Swissbib module
 *
 * PHP version 7
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
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category Swissbib_VuFind
 * @package  Controller
 * @author   Guenter Hipler  <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.swissbib.org
 */
namespace Swissbib\Controller;

use VuFind\Controller\AjaxController as VFAjaxController;
use VuFind\View\Helper\Root\RecordDataFormatter;

/**
 * Swissbib / VuFind: enhancements for AjaxController in Swissbib module
 *
 * @category Swissbib_VuFind
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
     * @return \Laminas\Http\Response
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
            new \Laminas\Stdlib\Parameters(
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
     * @return \Laminas\Stdlib\ResponseInterface
     */
    protected function buildResponse(
        $content,
        $spec
    ): \Laminas\Stdlib\ResponseInterface {
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
