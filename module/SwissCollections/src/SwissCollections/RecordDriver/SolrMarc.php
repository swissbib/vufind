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
        $titleGroup = new GroupEntry("title", 245);
        $titleGroup->addElement("title", "a");
        $titleGroup->addElement("subtitle", "b");
        $titleGroup->addElement("author", "c");
        $this->renderConfig->addGroup($titleGroup);
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
     * @param Callable $renderer
     */
    public function applyRenderer($marcIndex, $renderer) {
        $rc = $this->renderConfig->getEntry($marcIndex);
        $fieldList = $this->getMarcFields($marcIndex);
        if ($rc instanceof SingleEntry) {
            // TODO
        } else if ($rc instanceof GroupEntry) {
            foreach ($fieldList as $field) {
                foreach ($rc->elements as $elem) {
                    $map = $elem->buildMap();
                    $subMap = $this->getMappedFieldData(
                        $field, $map, true
                    );
                    $renderer($subMap);
                }
            }
        }
    }
}