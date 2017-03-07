<?php
/**
 * ResourceContainer
 *
 * PHP version 5
 *
 * Copyright (C) project swissbib, University Library Basel, Switzerland
 * http://www.swissbib.org  / http://www.swissbib.ch / http://www.ub.unibas.ch
 *
 * Date: 9/12/13
 * Time: 11:46 AM
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
 * @package  VuFind
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.swissbib.org
 */
namespace Swissbib\VuFind;

use VuFindTheme\ResourceContainer as VfResourceContainer;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Config\Config;

/**
 * ResourceContainer
 *
 * @category Swissbib_VuFind2
 * @package  VuFind
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.swissbib.org  Main Page
 */
class ResourceContainer extends VfResourceContainer
    implements ServiceLocatorAwareInterface
{
    /**
     * ServiceLocator
     *
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * List of ignore patterns
     *
     * @var String[]
     */
    protected $ignoredCssFiles;

    /**
     * List of ignore patterns
     *
     * @var String[]
     */
    protected $ignoredJsFiles;

    /**
     * Inject service locator
     *
     * @param ServiceLocatorInterface $serviceLocator ServiceLocator
     *
     * @return void
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
        $config               = new Config($serviceLocator->get('Config'));

        $this->ignoredCssFiles = $config->swissbib->ignore_css_assets->toArray();
        $this->ignoredJsFiles  = $config->swissbib->ignore_js_assets->toArray();
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

    /**
     * Remove ignored file before they're added to the resources
     *
     * @param Array | String $css Css
     *
     * @return void
     */
    public function addCss($css)
    {
        $css = $this->removeIgnoredFiles($css, $this->ignoredCssFiles);

        parent::addCss($css);
    }

    /**
     * Remove ignored js file before they're added to the resources
     *
     * @param array|string $js Javascript file (or array of files) to add (possibly
     *                         with extra settings from theme config appended to
     *                         each filename string).
     *
     * @return void
     */
    public function addJs($js)
    {
        $js = $this->removeIgnoredFiles($js, $this->ignoredJsFiles);

        parent::addJs($js);
    }

    /**
     * Remove files which are on the ignore list
     *
     * @param String[] $resourcesToInspect ResourceToInspect
     * @param String[] $resourcesToIgnore  ResourceToIgnore
     *
     * @return String[]
     */
    protected function removeIgnoredFiles($resourcesToInspect, $resourcesToIgnore)
    {
        if (!is_array($resourcesToInspect)
            && !is_a($resourcesToInspect, 'Traversable')
        ) {
            $resourcesToInspect = [$resourcesToInspect];
        }

        foreach ($resourcesToIgnore as $ignorePattern) {
            foreach ($resourcesToInspect as $index => $file) {
                if (preg_match($ignorePattern, $file)) {
                    // File matches ignore pattern
                    unset($resourcesToInspect[$index]);
                }
            }
        }

        return $resourcesToInspect;
    }
}
