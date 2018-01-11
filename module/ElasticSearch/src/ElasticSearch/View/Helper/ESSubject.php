<?php
/**
 * Created by IntelliJ IDEA.
 * User: edmundmaruhn
 * Date: 09.01.18
 * Time: 23:27
 */

namespace ElasticSearch\View\Helper;

/**
 * Class ESSubject
 * @package ElasticSearch\View\Helper
 */
class ESSubject extends AbstractHelper
{

    protected function getMetadataPrefix(): string
    {
        return 'card.knowledge.subject.metadata';
    }

    protected function getMetadataMethodMap(): array
    {
        return [
            'variants' => 'getVariantNameForTheSubjectHeading'
        ];
    }

    private $subject;

    public function getSubject(): \ElasticSearch\VuFind\RecordDriver\ESSubject
    {
        return $this->subject;
    }

    public function setSubject(\ElasticSearch\VuFind\RecordDriver\ESSubject $subject = null)
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


    public function getVariantNameForTheSubjectHeading(string $delimiter = ', ')
    {
        $variants = $this->getSubject()->getVariantNameForTheSubjectHeading();

        if (is_array($variants)) {
            $variants = implode($delimiter, $variants);
        }

        return $variants;
    }
}