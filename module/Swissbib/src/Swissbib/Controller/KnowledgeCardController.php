<?php
/**
 * Created by IntelliJ IDEA.
 * User: edmundmaruhn
 * Date: 18.12.17
 * Time: 11:42
 */

namespace Swissbib\Controller;

use ElasticSearch\VuFind\Search\ElasticSearch\Params;
use ElasticSearch\VuFind\Search\ElasticSearch\Results;
use VuFind\Controller\AbstractBase;
use VuFindSearch\Query\Query;
use Zend\View\Model\ViewModel;

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
    public function authorAction()
    {
        $index = "lsb";
        $type = "person";

        return $this->getKnowledgeCard($index, $type);
    }

    /**
     * @return \Zend\View\Model\ViewModel
     */
    public function topicAction()
    {
        $index = "gnd";
        $type = "DEFAULT";

        return $this->getKnowledgeCard($index, $type);
    }

    /**
     * @param $index
     * @param $type
     * @return \Zend\View\Model\ViewModel
     */
    protected function getKnowledgeCard($index, $type): \Zend\View\Model\ViewModel
    {
        $id = $this->params()->fromRoute('id', []);

        $manager = $this->serviceLocator->get('VuFind\SearchResultsPluginManager');
        /** @var Results */
        $results = $manager->get("ElasticSearch");

        /** @var Params */
        $params = $results->getParams();

        $params->setIndex($index);
        $params->setTemplate("id");

        /** @var Query $query */
        $query = $params->getQuery();
        $query->setHandler($type);
        $query->setString($id);

        $results->performAndProcessSearch();

        /** @var $content array */
        $content = $results->getResults();
        if ($content !== null && is_array($content) && count($content) === 1) {
            return $this->createViewModel(["driver" => $content[0]]);
        }

        $model = new ViewModel([
          'message' => 'Can not find a Knowledge Card for id: ' . $id,
        ]);
        $model->setTemplate('error/index');
        return $model;
    }
}
