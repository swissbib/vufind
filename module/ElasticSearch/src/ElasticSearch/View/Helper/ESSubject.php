<?php
/**
 * Created by IntelliJ IDEA.
 * User: edmundmaruhn
 * Date: 09.01.18
 * Time: 23:27
 */

namespace ElasticSearch\View\Helper;

use \Zend\View\Helper\AbstractHelper;

/**
 * Class ESSubject
 * @package ElasticSearch\View\Helper
 */
class ESSubject extends AbstractHelper
{

    /**
     * @var \ElasticSearch\VuFind\RecordDriver\ESSubject
     */
    private $subject;

    public function getSubject(): \ElasticSearch\VuFind\RecordDriver\ESSubject
    {
        return $this->subject;
    }

    public function setSubject(\ElasticSearch\VuFind\RecordDriver\ESSubject $subject)
    {
        $this->subject = $subject;
    }


    public function getSubjectLink(string $template): string
    {
        $subject = $this->getSubject();
        $identifier = $subject->getGndIdentifier();

        if (is_array($identifier) && count($identifier) > 0) {
            $identifier = $identifier[0];
        }

        $url = $this->getView()->url('card-knowledge-subject', ['id' => $identifier]);

        return sprintf($template, $url, $subject->getName());
    }


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
        $helper = new ESSubject();
        $helper->setView($this->getView());

        $subjects = [];

        foreach ($this->collection as $subject) {
            $helper->setSubject($subject);
            $subjects[] = $helper->getSubjectLink($template);
        }

        return implode($separator, $subjects);
    }

}