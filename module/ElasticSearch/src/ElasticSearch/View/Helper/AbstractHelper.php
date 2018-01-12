<?php
/**
 * Created by IntelliJ IDEA.
 * User: edmundmaruhn
 * Date: 11.01.18
 * Time: 15:04
 */

namespace ElasticSearch\View\Helper;

/**
 * Abstract view helper that implements some utilities commonly required for several views.
 *
 * @package ElasticSearch\View\Helper
 */
abstract class AbstractHelper extends \Zend\View\Helper\AbstractHelper
{
    /**
     * The name of the underlying record driver to be rendered.
     * @return string|null
     */
    abstract public function getDisplayName();



    /**
     * @param string $translationKeyBase
     * @return string
     */
    protected function resolveLabelWithDisplayName(string $translationKeyBase)
    {
        $displayName = $this->getDisplayName();
        $label = null;

        if (is_null($displayName)) {
            $label = $this->getView()->translate(sprintf('%s.no.name', $translationKeyBase));
        } else {
            $label = $this->getView()->translate($translationKeyBase);
            $label = sprintf($label, $displayName);
        }

        return $label;
    }


    private $metadataHelper;

    /**
     * @return MetadataHelper
     */
    public function getMetadata(): MetadataHelper
    {
        if (is_null($this->metadataHelper)) {
            $this->metadataHelper = new MetadataHelper();
            $this->metadataHelper->setSource($this);
            $this->metadataHelper->setView($this->getView());
            $this->metadataHelper->setPrefix($this->getMetadataPrefix());
            $this->metadataHelper->setMetadataMethodMap($this->getMetadataMethodMap());
        }

        return $this->metadataHelper;
    }

    /**
     * Template method subclasses may override to provide a prefix for localized labels for a specific purpose. It will
     * be set on the metadata view helper.
     *
     * @return string
     */
    protected function getMetadataPrefix(): string
    {
        return '';
    }

    /**
     * Template method subclasses may override to provide an array that maps metadata keys on methods on this helper. It
     * will be set on the metadata view helper. Then you can call the MetadataViewHelper#getMetadataList() method with
     * the keys of this array to retrieve these metadata information.
     *
     * @return array
     */
    protected function getMetadataMethodMap(): array
    {
        return [];
    }
}