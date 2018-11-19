<?php
/**
 * Swissbib Authentication view helper
 *
 * PHP version 7
 *
 * Copyright (C) project swissbib, University Library Basel, Switzerland
 * http://www.swissbib.org  / http://www.swissbib.ch / http://www.ub.unibas.ch
 *
 * Date: 7/22/14
 * Time: 4:49 PM
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
 * @package  VuFind_View_Helper_Root
 * @author   Guenter Hipler  <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.swissbib.org
 */
namespace Swissbib\VuFind\View\Helper\Root;

use VuFind\View\Helper\Root\Auth as VFAuthHelper;

/**
 * Authentication view helper
 *
 * @category Swissbib_VuFind
 * @package  VuFind_View_Helper_Root
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.swissbib.org  Main Page
 */
class Auth extends VFAuthHelper
{
    /**
     * Authentication classes where Ajax Login is not possible
     *
     * @var array
     */
    protected $noAjaxConfig;

    /**
     * Constructor
     *
     * @param \VuFind\Auth\Manager          $manager          Manager
     * @param \VuFind\Auth\ILSAuthenticator $ilsAuthenticator IlsAuthenticator
     * @param array                         $noAjaxConfig     NoAjaxConfig
     */
    public function __construct(\VuFind\Auth\Manager $manager,
        \VuFind\Auth\ILSAuthenticator $ilsAuthenticator,
        array $noAjaxConfig
    ) {
        parent::__construct($manager, $ilsAuthenticator);
        $this->noAjaxConfig = $noAjaxConfig;
    }

    /**
     * GetLoginTargets
     *
     * @return \VuFind\Auth\AbstractBase
     */
    public function getLoginTargets()
    {
        return $this->getManager()->getLoginTargets();
    }

    /**
     * IsAjaxLoginAllowed
     *
     * @return bool
     */
    public function isAjaxLoginAllowed()
    {
        return !in_array(
            $this->getManager()->getAuthClassForTemplateRendering(),
            $this->noAjaxConfig
        );
    }
}
