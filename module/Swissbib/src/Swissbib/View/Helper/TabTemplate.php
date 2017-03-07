<?php
/**
 * TabTemplate
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
 * @package  View_Helper
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
namespace Swissbib\View\Helper;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\View\Helper\AbstractHelper;
use Zend\View\Resolver\ResolverInterface;

/**
 * Search for templates with current tab postfix
 *
 * Example:
 * Base:   /path/to/template.phtml
 * Custom: /path/to/template.summon.phtml
 *
 * @category Swissbib_VuFind2
 * @package  View_Helper
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
class TabTemplate extends AbstractHelper implements ServiceLocatorAwareInterface
{
    /**
     * ServiceLocator
     *
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * Resolver
     *
     * @var ResolverInterface
     */
    protected $resolver;

    /**
     * Get tab specific template path if present
     *
     * @param String $baseTemplate BaseTemplate
     * @param String $tab          Tab
     *
     * @return String
     */
    public function __invoke($baseTemplate, $tab = null)
    {
        if ($tab === null) {
            return $baseTemplate;
        }
        $tab = strtolower($tab);
        $customTemplate = str_replace('.phtml', '', $baseTemplate) . '-' . $tab;

        return $this->resolver->resolve($customTemplate) !== false ?
            $customTemplate : $baseTemplate;
    }

    /**
     * Set service locator
     *
     * @param ServiceLocatorInterface $serviceLocator ServiceLocator
     *
     * @return void
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
        $this->resolver = $this->serviceLocator->getServiceLocator()
            ->get('Zend\View\Renderer\PhpRenderer')->resolver();
    }

    /**
     * Get service locator
     *
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }
}
