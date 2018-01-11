<?php
/**
 * Created by IntelliJ IDEA.
 * User: edmundmaruhn
 * Date: 11.01.18
 * Time: 11:16
 */

namespace ElasticSearch\View\Helper;

use Zend\View\Helper\AbstractHelper;


/**
 * Class ESSubjectCollection
 * @package ElasticSearch\View\Helper
 */
class ESSubjectCollection extends AbstractHelper
{

    /**
     * @var \ElasticSearch\VuFind\RecordDriver\ESSubject[]
     */
    private $collection;

    public function getCollection(): array
    {
        return $this->collection;
    }

    public function setCollection(array $collection)
    {
        $this->collection = $collection;
    }


    public function hasSubjectsInCollection()
    {
        return isset($this->collection) && count($this->collection) > 0;
    }

    public function getSubjectCollectionLinkList(string $template, string $separator = ', '): string
    {
        $helper = $this->getSubjectHelper();
        $subjects = [];

        foreach ($this->collection as $subject) {
            $helper->setSubject($subject);
            $subjects[] = $helper->getSubjectLink($template);
        }

        $helper->setSubject(null);

        return implode($separator, $subjects);
    }



    private $subjectHelper;

    protected function getSubjectHelper(): ESSubject
    {
        $this->subjectHelper = $this->subjectHelper ?? new ESSubject();
        $this->subjectHelper->setView($this->getView());
        return $this->subjectHelper;
    }
}