<?php
/**
 * Created by IntelliJ IDEA.
 * User: edmundmaruhn
 * Date: 11.01.18
 * Time: 15:04
 */

namespace ElasticSearch\View\Helper;
use ElasticSearch\VuFind\RecordDriver\ElasticSearch;

/**
 * Abstract view helper that implements some utilities commonly required for several views.
 *
 * @package ElasticSearch\View\Helper
 */
abstract class AbstractHelper extends \Zend\View\Helper\AbstractHelper
{

    private $driver;

    /**
     * @return ElasticSearch
     */
    public function getDriver()
    {
        return $this->driver;
    }

    /**
     * @param ElasticSearch $driver
     */
    protected function setDriver(ElasticSearch $driver = null)
    {
        $this->driver = $driver;
    }


    /**
     * The name of the underlying record driver to be rendered.
     * @return string|null
     */
    abstract public function getDisplayName();

    /**
     * The type of data this helper handles. Used to resolve type specific urls like for the detail page link.
     * @return string
     */
    abstract public function getType(): string;




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

    /**
     * Provides the localized label for the link on the detail page of the underlying managed record driver. The method
     * is used by the getDetailPageLink() method for link generation.
     *
     * @return string
     */
    abstract protected function getDetailPageLinkLabel();

    /**
     * @param string $template
     * @param string|null $label
     * If not null it is treated as the localization key and will be resolved before it is merged into the template.
     * @return string
     */
    public function getDetailPageLink(string $template, string $label = null): string
    {
        $label = is_null($label)
            ? $this->getDetailPageLinkLabel()
            : $this->getView()->translate($label);

        $route = sprintf('page-detail-%s', $this->getType());
        $segments = ['id' => $this->getDriver()->getUniqueID()];
        $url = $this->getView()->url($route, $segments);

        return sprintf($template, $url, $label);
    }
}