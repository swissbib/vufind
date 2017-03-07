<?php
/**
 * Swissbib HelpPageController
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
 * @package  Controller
 * @author   Guenter Hipler  <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.swissbib.org
 */
namespace Swissbib\Controller;

use Zend\View\Model\ViewModel;
use Zend\View\Resolver\ResolverInterface;
use VuFind\Controller\AbstractBase as BaseController;

/**
 * Swissbib HelpPageController
 *
 * @category Swissbib_VuFind2
 * @package  Controller
 * @author   Guenter Hipler  <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org
 */
class HelpPageController extends BaseController
{
    /**
     * The resolver
     *
     * @var ResolverInterface
     */
    protected $resolver;

    /**
     * Main entry for help pages
     *
     * @throws \Exception
     *
     * @return ViewModel
     */
    public function indexAction()
    {
        $topic    = $this->params()->fromRoute('topic');
        $template = $this->getTemplate($topic);

        if (!$template['template']) {
            throw new \Exception('Can\'t find matching help page');
        }

        $helpContent = $this->createViewModel();
        $helpContent->setTemplate($template['template']);

        $helpLayout = $this->createViewModel(
            [
                 'pages' => $this->getPages(),
                'first' => !!$template['first'],
                'topic' => strtolower($template['topic'])
            ]
        );
        $helpLayout->setTemplate('HelpPage/layout');
        $helpLayout->addChild($helpContent, 'helpContent');

            // Set Solr search for help pages
        $this->layout()->setVariable('searchClassId', 'Solr');
        $this->layout()->setVariable('pageClass', 'template_page');

        return $helpLayout;
    }

    /**
     * Find matching template
     * Fall back to search topic and english if not available
     *
     * @param String|null $topic Topic
     *
     * @return Array [template,first]
     */
    protected function getTemplate($topic)
    {
        /**
         * The resolver
         *
         * @var ResolverInterface $resolver
         */
        $resolver    = $this->serviceLocator->get('Zend\View\Renderer\PhpRenderer')
            ->resolver();
        $language    = $this->serviceLocator->get('VuFind\Translator')->getLocale();
        $template    = null;
        $activeTopic = null;
        $firstMatch  = true;
        $topic         = $topic ? strtolower($topic) : $this->getDefaultTopic();

        $languages = [$language, 'en'];
        $topics    = [$topic, 'search'];

        foreach ($languages as $language) {
            foreach ($topics as $topic) {
                $path = 'HelpPage/' . $language . '/' . $topic;

                if ($resolver->resolve($path) !== false) {
                    $template    = $path;
                    $activeTopic = $topic;
                    break 2;
                }

                $firstMatch = false;
            }
        }

        return [
            'template' => $template,
            'first'    => $firstMatch,
            'topic'    => $activeTopic
        ];
    }

    /**
     * Get default topic
     * Get first item of pages
     *
     * @return String
     */
    protected function getDefaultTopic()
    {
        $pages    = $this->getPages();

        return isset($pages[0]) ? $pages[0] : 'about';
    }

    /**
     * Get current active language
     *
     * @return String
     */
    protected function getLanguage()
    {
        return $this->serviceLocator->get('VuFind\Translator')->getLocale();
    }

    /**
     * Get available pages
     *
     * @return String[]
     */
    protected function getPages()
    {
        $config = $this->serviceLocator->get('VuFind/Config')->get('config');
        $pages    = [];

        if ($config) {
            if ($config->HelpPages && $config->HelpPages->pages) {
                $pages = $config->HelpPages->pages->toArray();
            }
        }

        return $pages;
    }
}
