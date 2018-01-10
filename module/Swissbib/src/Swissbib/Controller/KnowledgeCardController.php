<?php
/**
 * Created by IntelliJ IDEA.
 * User: edmundmaruhn
 * Date: 18.12.17
 * Time: 11:42
 */

namespace Swissbib\Controller;

use ElasticSearch\VuFind\RecordDriver\ElasticSearch;
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
    public function authorAction()
    {
        $personIndex = "lsb";
        $personType = "person";
        $id = $this->params()->fromRoute('id', []);

        try {
            $driver = $this->getInformation($id, $personIndex, $personType);

            $bibliographicResources = $this->getBibliographicResources($id);

            $subjects = $this->getSubjectsOf($bibliographicResources);

            return $this->createViewModel([
              "driver" => $driver,
              "subjects" => $subjects,
              "books" => $bibliographicResources
            ]);
        } catch (\Exception $e)
        {
            return $this->createErrorView($id);
        }
    }

    /**
     * @return \Zend\View\Model\ViewModel
     */
    public function topicAction()
    {
        $subjectIndex = "gnd";
        $subjectType = "DEFAULT";

        $id = $this->params()->fromRoute('id', []);

        try
        {
        $driver = $this->getInformation("http://d-nb.info/gnd/" . $id, $subjectIndex, $subjectType);

        return $this->createViewModel(["driver" => $driver]);
        } catch (\Exception $e)
        {
            return $this->createErrorView($id);
        }
    }


    /**
     * @param $id
     * @param $index
     * @param $type
     * @return ElasticSearch
     */
    protected function getInformation($id, $index, $type): ElasticSearch
    {
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
            return $content[0];
        }

        return null;
    }

    private function getBibliographicResources($id): array
    {
        $manager = $this->serviceLocator->get('VuFind\SearchResultsPluginManager');
        /** @var Results */
        $results = $manager->get("ElasticSearch");

        /** @var Params */
        $params = $results->getParams();

        $params->setIndex("lsb");
        $params->setTemplate("bibliographicResources_by_author");

        /** @var Query $query */
        $query = $params->getQuery();
        $query->setHandler("bibliographicResource");
        $query->setString("http://data.swissbib.ch/person/" . $id);

        $results->performAndProcessSearch();

        /** @var $content array */
        $content = $results->getResults();

        return $content;
    }

    private function getSubjectsOf($bibliographicResources)
    {
        $ids = [];
        /** @var ESBibliographicResource $bibliographicResource */
        foreach ($bibliographicResources as $bibliographicResource) {
            $s = $bibliographicResource->getSubjects();
            if (count($s) > 0) {
                $ids = array_merge($ids, $s);
            }
        }

        $ids = array_unique($ids);

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

    protected function createErrorView($id): ViewModel
    {
        $model = new ViewModel([
          'message' => 'Can not find a Knowledge Card for id: ' . $id,
        ]);
        $model->setTemplate('error/index');
        return $model;
    }
}
