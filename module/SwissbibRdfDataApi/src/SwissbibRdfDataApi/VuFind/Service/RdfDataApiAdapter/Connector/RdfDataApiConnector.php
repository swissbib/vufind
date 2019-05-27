<?php
namespace SwissbibRdfDataApi\VuFind\Service\RdfDataApiAdapter\Connector;

use Elasticsearch\Client;

use ML\JsonLD\JsonLD;
use SwissbibRdfDataApi\VuFind\Service\RdfDataApiAdapter\Result\RdfDataApiResult;
use SwissbibRdfDataApi\VuFind\Service\RdfDataApiAdapter\Result\Result;
use VuFindSearch\Backend\Exception\HttpErrorException;
use VuFindSearch\Backend\Exception\RemoteErrorException;
use VuFindSearch\Backend\Exception\RequestErrorException;
use VuFindSearch\Backend\Solr\HandlerMap;
use Zend\Http\Client\Adapter\AdapterInterface;
use Zend\Http\Client\Adapter\Exception\TimeoutException;
use Zend\Http\Client as HttpClient;
use SwissbibRdfDataApi\VuFind\Service\RdfDataApiAdapter\Search\Search;

use Zend\Http\Request;





/**
 * Elasticsearch client connector
 *
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>, Markus Mächler <markus.maechler@students.fhnw.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php
 * @link     http://linked.swissbib.ch
 */
class RdfDataApiConnector implements Connector
{
    /**
     * @var Client
     */
    protected $client;

    protected $adapter = 'Zend\Http\Client\Adapter\Socket';

    /**
     * HTTP read timeout.
     *
     * @var int
     */
    protected $timeout = 30;



    /**
     * Constructor
     *
     * @param string|array $url       SOLR core URL or an array of alternative URLs
     * @param HandlerMap   $map       Handler map
     * @param string       $uniqueKey Solr field used to store unique identifier
     *
     * @return void
     */
    public function __construct()
    {
        //$this->url = $url;
        //$this->uniqueKey = $uniqueKey;
    }



    /**
     * Send request the SOLR and return the response.
     *
     * @param HttpClient $client Prepared HTTP client
     *
     * @return string Response body
     *
     * @throws RemoteErrorException  SOLR signaled a server error (HTTP 5xx)
     * @throws RequestErrorException SOLR signaled a client error (HTTP 4xx)
     */
    protected function send(HttpClient $client)
    {

        //todo: actually we do not integrate a looging trait

        //useful to display links to solr queries directly on screen
        /*
        echo '<a href="' .
            sprintf('%s', $client->getUri()) .
            '&debug=all&echoParams=all&debug.explain.structured=true"' .
            ' target="_blank">solr link</a>';
        */

        //$this->debug(
        //    sprintf('=> %s %s', $client->getMethod(), $client->getUri())
        //);

        $time     = microtime(true);
        $response = $client->send();
        $time     = microtime(true) - $time;

        //$this->debug(
        //    sprintf(
        //        '<= %s %s', $response->getStatusCode(),
        //        $response->getReasonPhrase()
        //    ), ['time' => $time]
        //);

        if (!$response->isSuccess()) {
            throw HttpErrorException::createFromResponse($response);
        }
        return $response->getBody();
    }

    /**
     * Create the HTTP client.
     *
     * @param string $url    Target URL
     * @param string $method Request method
     *
     * @return HttpClient
     */
    protected function createClient($url, $method)
    {
        $client = new HttpClient();
        $client->setAdapter($this->adapter);
        $client->setOptions(['timeout' => $this->timeout]);
        $client->setUri($url);
        $client->setMethod($method);
        //if ($this->proxy) {
        //    $this->proxy->proxify($client);
        //}
        return $client;
    }


    public function search(Search $search): Result
    {
        $client = $this->createClient("http://" . $search->getUrl(), "GET");



        $result = json_decode($this->send($client));

        //$result1 = json_decode($this->send($client));
        //$flatten =  JsonLD::flatten($result);
        //$compact = JsonLD::compact($result,"http://lobid.org/gnd/context.jsonld");
        //$compact = JsonLD::compact($result);

        //$id = $result1->gndIdentifier;
        //$describedBy = $result1->describedBy->id;

        //return  is_array($result) ? new RdfDataApiResult($result) : new RdfDataApiResult([$result]);
        return  new RdfDataApiResult($result);

    }
}
