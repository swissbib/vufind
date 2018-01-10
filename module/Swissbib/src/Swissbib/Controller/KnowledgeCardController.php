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
        } catch (\Exception $e) {
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

        $id = "http://d-nb.info/gnd/" . $this->params()->fromRoute('id', []);

        try {
            $driver = $this->getInformation($id, $subjectIndex, $subjectType);
            $subSubjects = $this->getSubSubjects($id);
            $parentSubjects = $this->getParentSubjects($driver->getParentSubjects());

            return $this->createViewModel(
              [
                "driver" => $driver,
                "subSubjects" => $subSubjects,
                "parentSubjects" => $parentSubjects
              ]
            );
        } catch (\Exception $e) {
            return $this->createErrorView($id);
        }
    }


    /**
     * @param $id
     * @param $index
     * @param $type
     * @return ElasticSearch
     */
    protected function getInformation($id, $index, $type)
    {
        $content = $this->search(
          $id,
          "id",
          $index,
          $type
        );

        if ($content !== null && is_array($content) && count($content) === 1) {
            return $content[0];
        }
        throw new \Exception("Found no data for id " . $id);
    }

    private function getBibliographicResources($id): array
    {
        return $this->search("http://data.swissbib.ch/person/" . $id,
          "bibliographicResources_by_author",
          "lsb",
          "bibliographicResource"
        );
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

        return $this->search(
          $this->arrayToSearchString($ids),
          "id",
          "gnd",
          "DEFAULT"
        );
    }

    private function getSubSubjects($id)
    {
        return $this->search($id, "sub_subjects");
    }

    private function getParentSubjects($ids)
    {
        return $this->search(
          $this->arrayToSearchString($ids),
          "id",
          "gnd",
          "DEFAULT"
        );
    }

    /**
     * @param $q
     * @param $template
     * @param $index
     * @param $type
     * @return array
     */
    protected function search($q, $template, $index = null, $type = null): array
    {
        $manager = $this->serviceLocator->get('VuFind\SearchResultsPluginManager');
        /** @var Results */
        $results = $manager->get("ElasticSearch");

        /** @var Params */
        $params = $results->getParams();

        if (isset($index)) {
            $params->setIndex($index);
        }
        $params->setTemplate($template);

        /** @var Query $query */
        $query = $params->getQuery();
        if (isset($type))
        {
            $query->setHandler($type);
        }
        $query->setString($q);


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

    /**
     * @param $ids
     * @return string
     */
    private function arrayToSearchString($ids): string
    {
        return '[' . implode(",", $ids) . ']';
    }
}
