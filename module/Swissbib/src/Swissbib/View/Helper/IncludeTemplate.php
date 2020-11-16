<?php
/**
 * IncludeTemplate
 *
 * PHP version 7
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
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category Swissbib_VuFind
 * @package  View_Helper
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
namespace Swissbib\View\Helper;

use Laminas\View\Helper\AbstractHelper;
use Laminas\View\Resolver\AggregateResolver;
use Laminas\View\Resolver\TemplateMapResolver;

/**
 * IncludeTemplate
 *
 * @category Swissbib_VuFind
 * @package  View_Helper
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
class IncludeTemplate extends AbstractHelper
{
    /**
     * Invoke include Template
     *
     * @param string $templateFile TemplateFile
     * @param string $theme        Theme
     *
     * @return string
     */
    public function __invoke($templateFile = '', $theme = '')
    {
        $filePath =  APPLICATION_PATH . '/themes/' . $theme .
            '/templates/' . $templateFile . '.phtml';

        if (!file_exists($filePath)) {
            return '';
        }

        $phpRenderer    = $this->getView();
        $resolverBackup = $phpRenderer->resolver();
        $resolver       = new AggregateResolver();
        $stack          = new TemplateMapResolver([$templateFile => $filePath]);

        $phpRenderer->setResolver($resolver);
        $resolver->attach($stack)->attach($resolverBackup);

        $renderedTemplate = $phpRenderer->render($templateFile);

        $phpRenderer->setResolver($resolverBackup);

        return $renderedTemplate;
    }
}
