<?php
/**
 * Factory
 *
 * PHP version 5
 *
 * Copyright (C) project swissbib, University Library Basel, Switzerland
 * http://www.swissbib.org  / http://www.swissbib.ch / http://www.ub.unibas.ch
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
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @category Swissbib_VuFind2
 * @package  VuFind_Auth
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
namespace Swissbib\VuFind\Auth;

use Zend\ServiceManager\ServiceManager;

/**
 * Class Factory
 * factory for authentication (swissbib specific) authentication services
 *
 * @category Swissbib_VuFind2
 * @package  VuFind_Auth
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
class Factory
{
    /**
     * Construct Shibboleth mock object - hand in environments
     * without specific shib service provider installation (e.g. Snowflake)
     *
     * @param ServiceManager $sm ServiceManager
     *
     * @return ShibbolethMock
     */
    public static function getShibMock(ServiceManager $sm)
    {
        return new ShibbolethMock(
            $sm->getServiceLocator()->get('VuFind\ILSConnection')
        );
    }
}
