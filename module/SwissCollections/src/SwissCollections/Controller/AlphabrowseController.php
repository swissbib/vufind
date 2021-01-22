<?php
/**
 * SwissCollections: AlphaBrowse Module Controller
 *
 * PHP version 7
 *
 * Copyright (C) project swissbib, University Library Basel, Switzerland
 * http://www.swisscollections.org  / http://www.swisscollections.ch / http://www.ub.unibas.ch
 *
 * Date: 1/12/20
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
 * @category SwissCollections_VuFind
 * @package Controller
 * @author   Christoph Böhm <cbo@outermedia.de>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.swisscollections.org Project Wiki
 */

namespace SwissCollections\Controller;

use Exception;
use Laminas\Config\Config;
use Laminas\Http\Client as HttpClient;
use Laminas\Http\Client\Adapter\Curl as CurlAdapter;
use Laminas\ServiceManager\ServiceLocatorInterface;
use VuFind\Controller\AlphabrowseController as VuFindAlphabrowseController;
use VuFindSearch\ParamBag;

/**
 * AlphabrowseController Class
 *
 * Controls the alphabetical browsing feature
 *
 * @category SwissCollections_VuFind
 * @package Controller
 * @author   Christoph Böhm <cbo@outermedia.de>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
class AlphabrowseController extends VuFindAlphabrowseController
{

  /**
   * @var HttpClient
   */
    protected $client;
  
    /**
     * @var Config
     */
    protected $config;

    /**
     * AlphabrowseController constructor.
     * @param ServiceLocatorInterface $sm
     * @param Config $config
     */
    public function __construct(ServiceLocatorInterface $sm, Config $config)
    {
        parent::__construct($sm);
        $this->config = $config;
        $this->client = new HttpClient();
        $this->client->setOptions(['timeout' => $settings['timeout'] ?? 120]);
        $adapter = new CurlAdapter();
        $adapter->setOptions(
            [
            'curloptions' => [
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_FOLLOWLOCATION => true,
            ]
            ]
        );
        $this->client->setAdapter($adapter);
    }

    /**
     * Gathers data for the view of the AlphaBrowser and does some initialization
     *
     * @return \Laminas\View\Model\ViewModel
     * @throws Exception
     */
    public function homeAction()
    {
        // Load browse types from config file, or use defaults if unavailable:
        if (isset($this->config->AlphaBrowse_Types)
        && !empty($this->config->AlphaBrowse_Types)
        ) {
            $types = [];
            foreach ($this->config->AlphaBrowse_Types as $key => $value) {
                $types[$key] = $value;
            }
        } else {
            $types = [
            'topic' => 'By Topic',
            'author' => 'By Author',
            'title' => 'By Title',
            'lcc' => 'By Call Number'
            ];
        }

        // Load any extras from config file
        $extras = [];
        if (isset($this->config->AlphaBrowse_Extras)) {
            foreach ($this->config->AlphaBrowse_Extras as $key => $value) {
                $extras[$key] = $value;
            }
        } else {
            $extras = [
            'title' => 'author:format:publishDate',
            'lcc' => 'title',
            'dewey' => 'title'
            ];
        }

        // Load remaining config parameters
        $limit = isset($this->config->AlphaBrowse->page_size)
        && is_numeric($this->config->AlphaBrowse->page_size)
        ? (int) $this->config->AlphaBrowse->page_size : 20;

        // Process incoming parameters:
        $source = $this->params()->fromQuery('source', false);
        $position = $this->params()->fromQuery('page', false);
        $from = $this->params()->fromQuery('from', false);

        // Set up any extra parameters to pass
        $extraParams = new ParamBag();
        if (isset($extras[$source])) {
            $extraParams->add('extras', $extras[$source]);
        }

        // Create view model:
        $view = $this->createViewModel();

        // If required parameters are present, load results:
        if ($source && $from !== false) {
            // https://docs.laminas.dev/laminas-http/client/intro/
            $this->client->resetParameters();
            // Build headers
//    $headers = [
//      'Accept' => $this->accept,
//      'Content-Type' => $this->contentType,
//      'Accept-Encoding' => 'gzip,deflate'
//    ];
//    $this->client->setHeaders($headers);
            $method = 'GET';
            $this->client->setMethod($method);

            $query = [ 'term' => $position ? $position : $from ];
            $this->client->setUri($this->config->AlphaBrowse->base_url . '/' . $source);
            $this->client->setParameterGet($query);
//    $this->client->setEncType($messageFormat);
            $result = $this->client->send();
            if (!$result->isSuccess()) {
                $error = $result->getBody();
                $decodedError = json_decode($error, true);
                throw new Exception($decodedError ? $decodedError : $error);
            }
            $result = json_decode($result->getBody(), true);


            // No results?    Try the previous page just in case we've gone past
            // the end of the list....
            if (empty($result)) {
                // TODO How to browse backwards?
//                $page--;
//        $result = $db->alphabeticBrowse(
//          $source, $from, $page, $limit, $extraParams, 0
//        );
            }

            // Only display next/previous page links when applicable:
            if (count($result) > $limit) {
                $view->nextpage = $result[$limit +1 ]['fieldvalue'];
            }
            // TODO How to browse backwards?
//            if ($result['Browse']['offset'] + $result['Browse']['startRow'] > 1) {
//                $view->prevpage = $page - 1;
//            }
            $view->result = array_slice($result, 0, $limit);
        }

        $view->alphaBrowseTypes = $types;
        $view->from = $from;
        $view->source = $source;

        return $view;
    }
}
