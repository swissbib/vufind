<?php
/**
 * Created by IntelliJ IDEA.
 * User: edmundmaruhn
 * Date: 11.01.18
 * Time: 13:26
 */

namespace ElasticSearch\View\Helper;


use Zend\View\Helper\AbstractHelper;

/**
 * Class MetadataHelper
 * @package ElasticSearch\View\Helper
 */
class MetadataHelper extends AbstractHelper
{
    private $source;

    public function getSource()
    {
        return $this->source;
    }

    public function setSource(AbstractHelper $source)
    {
        $this->source = $source;
    }

    private $prefix;

    public function getPrefix()
    {
        return $this->prefix;
    }

    public function setPrefix(string $prefix)
    {
        $this->prefix = $prefix;
    }


    private $metadataMethodMap;

    public function getMetadataMethodMap()
    {
        return $this->metadataMethodMap;
    }

    public function setMetadataMethodMap(array $map)
    {
        $this->metadataMethodMap = $map;
    }

    public function getList(string ...$keys)
    {
        $metadataList = [];

        foreach ($keys as $key) {
            $entry = $this->getMetadataListEntry($key);

            if (!is_null($entry)) {
                $metadataList[] = $entry;
            }
        }

        return $metadataList;
    }


    private function getMetadataListEntry(string $key)
    {
        $entry = null;

        if (isset($this->metadataMethodMap[$key])) {
            $method = $this->metadataMethodMap[$key];
            $value = $this->getSource()->{$method}();

            if (!is_null($value)) {
                $translationKey = sprintf('%s.%s', $this->getPrefix(), $key);
                $entry = [
                    'label' => $this->getView()->translate($translationKey),
                    'value' => $value,
                    'cssClass' => $key
                ];
            }
        }

        return $entry;
    }

}