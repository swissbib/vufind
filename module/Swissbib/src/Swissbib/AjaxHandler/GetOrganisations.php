<?php
/**
 * "Get Subjects" AJAX handler
 *
 * PHP version 7
 *
 * Copyright (C) Swissbib 2018.
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

use Interop\Container\ContainerInterface;
use VuFind\AjaxHandler\AbstractBase as VFAjax;
use VuFind\AjaxHandler\AjaxHandlerInterface;
use VuFind\View\Helper\Root\RecordDataFormatter;
use Zend\Http\PhpEnvironment\Request;
use Zend\Mvc\Controller\Plugin\Params;

/**
 * "GetOrganisations" AJAX handler
 *
 * This will return the authors form ElasticSearch
 *
 * @category VuFind
 * @package  AJAX
 * @author   Matthias Edel <matthias.edel@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://vufind.org/wiki/development Wiki
 */
class GetOrganisations extends VFAjax implements AjaxHandlerInterface
{
    use \Swissbib\AjaxHandler\AjaxTrait;

    /**
     * GetOrganisations constructor.
     *
     * @param ContainerInterface $sm      Service Manager
     * @param Request            $request Request
     */
    public function __construct(ContainerInterface $sm, Request $request)
    {
        $this->serviceLocator = $sm;
        $this->request = $request;
        $this->renderer = $sm->get('ViewRenderer');
    }

    /**
     * Handle a request.
     *
     * @param Params $params Parameter helper from controller
     *
     * @return array [response data, HTTP status code]
     */
    public function handleRequest(Params $params)
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
        return $this->formatResponse($response->getContent());
    }
}
