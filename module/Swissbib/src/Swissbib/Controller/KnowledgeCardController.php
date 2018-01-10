<?php
/**
 * Created by IntelliJ IDEA.
 * User: edmundmaruhn
 * Date: 18.12.17
 * Time: 11:42
 */

namespace Swissbib\Controller;

use ElasticSearch\VuFind\RecordDriver\ESBibliographicResource;
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
    public function personAction()
    {
        $personIndex = "lsb";
        $personType = "person";

        return $this->getKnowledgeCard($personIndex, $personType);
    }

    /**
     * @return \Zend\View\Model\ViewModel
     */
    public function subjectAction()
    {
        $subjectIndex = "gnd";
        $subjectType = "DEFAULT";

        return $this->getKnowledgeCard($subjectIndex, $subjectType);
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
            $subjects = $this->getRelatedSubjects($id);
            return $this->createViewModel(["driver" => $content[0], "subjects" => $subjects]);
        }

        $model = new ViewModel([
          'message' => 'Can not find a Knowledge Card for id: ' . $id,
        ]);
        $model->setTemplate('error/index');
        return $model;
    }

    private function getRelatedSubjects($id)
    {
        $manager = $this->serviceLocator->get('VuFind\SearchResultsPluginManager');
        /** @var Results */
        $results = $manager->get("ElasticSearch");

        /** @var Params */
        $params = $results->getParams();

        $params->setIndex("lsb");
        $params->setTemplate("subjects_by_author");

        /** @var Query $query */
        $query = $params->getQuery();
        $query->setHandler("bibliographicResource");
        $query->setString("http://data.swissbib.ch/person/" . $id);

        $results->performAndProcessSearch();

        /** @var $content array */
        $content = $results->getResults();

        $subjects = [];
        /** @var ESBibliographicResource $bibliographicResource */
        foreach ($content as $bibliographicResource)
        {
            $s = $bibliographicResource->getSubjects();
            if (count($s) > 0) {
                $subjects = array_merge($subjects, $s);
              }
        }

        $subjects = array_unique($subjects);

        $subjectData = $this->getSubjectData($subjects);
        return $subjectData;

    }

    private function getSubjectData($ids)
    {
        $manager = $this->serviceLocator->get('VuFind\SearchResultsPluginManager');
        /** @var Results */
        $results = $manager->get("ElasticSearch");

        /** @var Params */
        $params = $results->getParams();

        $params->setIndex("gnd");
        $params->setTemplate("id");

        /** @var Query $query */
        $query = $params->getQuery();
        $query->setHandler("DEFAULT");
        $query->setString('[' . implode(",", $ids) . ']');


        $results->performAndProcessSearch();

        /** @var $content array */
        $content = $results->getResults();

        return $content;
    }
}
