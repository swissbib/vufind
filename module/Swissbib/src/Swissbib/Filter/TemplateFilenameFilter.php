<?php
/**
 * Prefix rendered output with HTML comment stating filename of the rendered template
 *
 * PHP version 5
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
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @category Swissbib_VuFind2
 * @package  Filter
 * @author   Guenter Hipler  <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.swissbib.org
 */
namespace Swissbib\Filter;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\Filter\AbstractFilter;

/**
 * Class SbTemplateFilenameFilter
 *
 * Content filter to wrap rendered template content with HTML comments,
 * indicating which template file implemented a section of output.
 * Every template section is wrapped by comments to mark the 1) beginning and 2)
 * ending of a template file.
 *
 * Activating the filter: call SbTemplateFilenameFilter::onBootstrap() from within
 * the onBootstap() method of the module class (\path\to\module\Namespace\Module.php)
 *
 * @category Swissbib_Filter
 * @package  Filter
 * @author   Guenter Hipler  <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org
 */
class TemplateFilenameFilter extends AbstractFilter
    implements ServiceLocatorAwareInterface
{
    /**
     * ServiceLocator
     *
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * Filter
     *
     * @param Mixed $content Content
     *
     * @return Mixed|String
     */
    public function filter($content)
    {
        $sm = $this->getServiceLocator();
        /**
         * PhpRenderer
         *
         * @var $phpRenderer \Zend\View\Renderer\PhpRenderer
         */
        $phpRenderer = $sm->get('Zend\View\Renderer\PhpRenderer');

        // Fetch private property PhpRenderer::__file via reflection
        $rendererReflection = new \ReflectionObject($phpRenderer);

        $fileProperty = $rendererReflection->getProperty('__file');
        $fileProperty->setAccessible(true);
        $templateFilename = $fileProperty->getValue($phpRenderer);

        // Don't wrap export stuff
        if ((stristr($templateFilename, 'export-') !== false)
            || (stristr($templateFilename, '/email/') !== false)
            || (stristr($templateFilename, '/link') !== false)
        ) {
            return $content;
        }

        // Remove possibly confidential server details from path
        $directoryDelimiter = 'themes' . DIRECTORY_SEPARATOR;
        $templateFilename   = substr(
            $templateFilename, strpos($templateFilename, $directoryDelimiter) + 7
        );

        return $this->_wrapContentWithComment($content, $templateFilename);
    }

    /**
     * Wraps contents with comments
     *
     * @param String $content          Content
     * @param String $templateFilename Template file
     *
     * @return String
     */
    private function _wrapContentWithComment($content, $templateFilename)
    {
        $templateFilename = str_replace('\\', '/', $templateFilename);
        $isStartOfHtml = strstr($content, '<html') !== false
            || strstr($content, '<xml') !== false;

        return $isStartOfHtml ? $content :
                "\n" . '<!-- Begin' . (!empty($type) ? ' ' . $type : '') . ': '
                . $templateFilename . ' -->'
                . "\n" . $content
                . "\n" . '<!-- End: ' . $templateFilename . ' -->'
                . "\n";
    }

    /**
     * Set ServiceLocator
     *
     * @param ServiceLocatorInterface $serviceLocator ServiceLocator
     *
     * @return void
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    /**
     * Get ServiceLocator
     *
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }
}
