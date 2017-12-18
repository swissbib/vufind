<?php
/**
 * Created by IntelliJ IDEA.
 * User: edmundmaruhn
 * Date: 18.12.17
 * Time: 11:42
 */

namespace Swissbib\Controller;

use VuFind\Controller\AbstractBase;

/**
 * Swissbib KnowledgeCardController
 *
 * Provides information to be rendered in knowledge cards (light-boxes).
 *
 * @category Swissbib_VuFind2
 * @package  Controller
 * @author   Edmund Maruhn  <ema@outermedia.de>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org
 */
class KnowledgeCardController extends AbstractBase
{

    /**
     * @return \Zend\View\Model\ViewModel
     */
    public function knowledgeCardAuthorAction()
    {
        return $this->createViewModel();
    }

    /**
     * @return \Zend\View\Model\ViewModel
     */
    public function knowledgeCardTopicAction()
    {
        return $this->createViewModel();
    }

}