<?php

namespace Swissbib\AjaxHandler;

use Zend\Http\Request;
use Zend\Http\Response;

trait AjaxTrait
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Response
     */
    protected $response;

    /**
     * View renderer
     *
     * @var RendererInterface
     */
    protected $renderer;

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
        foreach ($formattedRecord as $arrayElement)
        {
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

}
