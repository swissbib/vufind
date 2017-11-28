<?php
/**
 * Controller of the National Licence page on the Swissbib account.
 *
 * PHP version 5
 * Copyright (C) project swissbib, University Library Basel, Switzerland
 * http://www.swissbib.org  / http://www.swissbib.ch / http://www.ub.unibas.ch
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License version 2,
 * as published by the Free Software Foundation.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @category Swissbib_VuFind2
 * @package  Controller
 * @author   Simone Cogno <scogno@snowflake.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
namespace Swissbib\Controller;

use Zend\Mvc\MvcEvent;
use Zend\View\Model\ViewModel;
use Swissbib\Services\Pura;
use Swissbib\VuFind\Db\Row\PuraUser;

/**
 * Class NationalLicencesController.
 *
 * @category Swissbib_VuFind2
 * @package  Controller
 * @author   Simone Cogno <scogno@snowflake.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
class PuraController extends BaseController
{
    /**
     * Pura.
     *
     * @var Pura
     */
    protected $puraService;

    /**
     * Constructor.
     * NationalLicencesController constructor.
     *
     * @param Pura $puraService Pura.
     */
    public function __construct(Pura $puraService)
    {
        $this->puraService = $puraService;
    }

    /**
     * Show the form to become compliant with the Swiss National Licences.
     *
     * @return mixed|ViewModel
     * @throws \Exception
     */
    public function indexAction()
    {
        $publishers = $this->puraService->getPublishersForALibrary("Z01");
        $institution= $this->puraService->getInstitutionInfo("Z01");

        //$publishers = $this->puraService->getPublishersForALibrary("RE01001");
        //$institution= $this->puraService->getInstitutionInfo("RE01001");

        $user = null;
        $user = $this->puraService->getPuraUser("1");

        $view = new ViewModel(
            [
                'publishers' => $publishers,
                'user' => $user,
                'institution' => $institution
            ]
        );

        return $view;
    }
}
