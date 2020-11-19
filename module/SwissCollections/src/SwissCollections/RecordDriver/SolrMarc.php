<?php


namespace SwissCollections\RecordDriver;

use Laminas\Log\Logger;
use Swissbib\RecordDriver\SolrMarc as SwissbibSolrMarc;
use SwissCollections\RenderConfig\GroupEntry;
use SwissCollections\RenderConfig\RenderConfig;
use SwissCollections\RenderConfig\SingleEntry;


/**
 * Class SolrMarc
 * @package SwissCollections\RecordDriver
 */
class SolrMarc extends SwissbibSolrMarc {
    /**
     * @var RenderConfig
     */
    protected $renderConfig;

    public function __construct($mainConfig = null, $recordConfig = null,
                                $searchSettings = null, $holdingsHelper = null, $solrDefaultAdapter = null,
                                $availabilityHelper = null, $libraryNetworkLookup = null, $logger = null
    ) {
        parent::__construct($mainConfig, $recordConfig, $searchSettings, $holdingsHelper, $solrDefaultAdapter,
            $availabilityHelper, $libraryNetworkLookup, $logger);
        $this->renderConfig = new RenderConfig();

        // define title
        $titleGroup = new GroupEntry("title", 245);
        $titleGroup->addElement("title", "a");
        $titleGroup->addElement("subtitle", "b");
        $titleGroup->addElement("author", "c");
        $this->renderConfig->addGroup($titleGroup);

        // define signature
        $signatureGroup = new GroupEntry("signature", 949);
        $signatureGroup->setInlineRenderMode();
        $signatureGroup->addElement("institution-code", "b");
        $signatureGroup->addElement("standort code", "c");
        $signatureGroup->addElement("signature1", "j");
        $signatureGroup->addElement("signature2", "h");
        $this->renderConfig->addGroup($signatureGroup);
        // $this->logger->log(Logger::ERR, "RC: " . print_r($this->renderConfig, true));
    }

    public function getShortTitle() {
        // $this->logger->log(Logger::ERR, "Hallo1!");
        // TODO Remove (proof of concept)
        return parent::getShortTitle();
    }

    /**
     * @param int $marcIndex
     * @return GroupEntry|SingleEntry
     */
    public function getRenderConfig($marcIndex) {
        return $this->renderConfig->getEntry($marcIndex);
    }

    /**
     * @param int $marcIndex
     * @param Callable $renderer called with $subMap, $marcField, GroupEntry|SingleEntry, $isFirst, $isLast
     */
    public function applyRenderer($marcIndex, $renderer) {
        $rc = $this->renderConfig->getEntry($marcIndex);
        $field = $this->getMarcField($marcIndex);
        if ($field) {
            if ($rc instanceof SingleEntry) {
                // TODO
            } else if ($rc instanceof GroupEntry) {
                $array = $rc->elements;
                $values = [];
                // filter out empty values
                foreach ($array as $elem) {
                    $map = $elem->buildMap();
                    $subMap = $this->getMappedFieldData($field, $map, true);
                    if (!empty($subMap['value'])) {
                        $values[] = ['subMap' => $subMap, 'renderConfig' => $elem];
                    }
                }
                foreach ($values as $key => $v) {
                    $isFirst = $key === array_key_first($values);
                    $isLast = $key === array_key_last($values);
                    $renderer($v['subMap'], $field, $v['renderConfig'], $isFirst, $isLast);
                }
            }
        }
    }
}