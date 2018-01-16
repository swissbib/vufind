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
 *
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
            'variants' => 'getVariantNames',
            'definition' => 'getDefinition'
        ];
    }

    public function getType(): string
    {
        return 'subject';
    }

    private $subject;

    public function getSubject(): \ElasticSearch\VuFind\RecordDriver\ESSubject
    {
        return $this->subject;
    }

    public function setSubject(\ElasticSearch\VuFind\RecordDriver\ESSubject $subject = null)
    {
        parent::setDriver($subject);
        $this->subject = $subject;
    }

    public function getDisplayName()
    {
        $name = $this->getSubject()->getName();
        return strlen($name) > 0 ? $name : null;
    }

    public function getSubjectLink(string $template): string
    {
        $subject = $this->getSubject();

        $url = $this->getView()->url('card-knowledge-subject', ['id' => $subject->getUniqueID()]);

        return sprintf($template, $url, $subject->getName());
    }


    public function getVariantNames(string $delimiter = ', ')
    {
        $variants = $this->getSubject()->getVariantNameForTheSubjectHeading();

        if (is_array($variants)) {
            $variants = implode($delimiter, $variants);
        }

        return strlen($variants) > 0 ? trim($variants) : null;
    }

    public function getDefinition()
    {
        $definition = $this->getSubject()->getDefinitionDisplayField();

        if (is_array($definition)) {
            $definition = count($definition) > 0 ? $definition[0] : null;
        }

        return $definition;
    }

    public function getDetailPageLinkLabel()
    {
        return $this->resolveLabelWithDisplayName('card.knowledge.subject.page.link');
    }

    public function getMoreMediaLinkLabel()
    {
        return $this->resolveLabelWithDisplayName('card.knowledge.subject.medias');
    }

    public function getMoreMediaSearchLink(string $template)
    {
        $label = $this->getMoreMediaLinkLabel();
        $url = $this->getView()->url('search-results');
        $url = sprintf('%s?lookfor=%s&type=Subject', $url, urlencode($this->getSubject()->getName()));

        return sprintf($template, $url, $label);
    }
}