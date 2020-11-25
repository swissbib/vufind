<?php


namespace SwissCollections\RecordDriver;

use Laminas\Log\Logger;
use Swissbib\RecordDriver\SolrMarc as SwissbibSolrMarc;
use SwissCollections\RenderConfig\CompoundEntry;
use SwissCollections\RenderConfig\RenderConfig;
use SwissCollections\RenderConfig\RenderGroupConfig;
use SwissCollections\RenderConfig\SingleEntry;


/**
 * Class SolrMarc
 * @package SwissCollections\RecordDriver
 */
class SolrMarc extends SwissbibSolrMarc {
    /**
     * @var RenderGroupConfig
     */
    protected $renderConfig;

    public function __construct($mainConfig = null, $recordConfig = null,
                                $searchSettings = null, $holdingsHelper = null, $solrDefaultAdapter = null,
                                $availabilityHelper = null, $libraryNetworkLookup = null, $logger = null
    ) {
        parent::__construct($mainConfig, $recordConfig, $searchSettings, $holdingsHelper, $solrDefaultAdapter,
            $availabilityHelper, $libraryNetworkLookup, $logger);
        $this->renderConfig = new RenderConfig();

        $renderGroup = new RenderGroupConfig("Basisinformationen");

        $titleGroup = new CompoundEntry("title", 245);
        $titleGroup->addElement("title", "a");
        $titleGroup->addElement("subtitle", "b");
        $titleGroup->addElement("author", "c");
        $renderGroup->addCompound($titleGroup);

        $signatureGroup = new CompoundEntry("signature", 949);
        $signatureGroup->setInlineRenderMode();
        $signatureGroup->addElement("institution-code", "b");
        $signatureGroup->addElement("standort code", "c");
        $signatureGroup->addElement("signature1", "j");
        $signatureGroup->addElement("signature2", "h");
        $renderGroup->addCompound($signatureGroup);

        $renderGroup->addSingle(new SingleEntry("resourcetype", 7));

        $this->renderConfig->add($renderGroup);

        $renderGroup = new RenderGroupConfig("Sucheinstiege");

        $renderGroup->addSingle(new SingleEntry("Person1", 100, "a"));
        $renderGroup->addSingle(new SingleEntry("Person2", 700, "a"));
        $renderGroup->addSingle(new SingleEntry("Ort", 264, "a"));

        $this->renderConfig->add($renderGroup);

        $renderGroup = new RenderGroupConfig("Thema");

        $renderGroup->addSingle(new SingleEntry("Formschlagwort", 655, "a"));
        $renderGroup->addSingle(new SingleEntry("Alternativtitel", 246, "a"));

        $this->renderConfig->add($renderGroup);

        // $this->logger->log(Logger::ERR, "RC: " . print_r($this->renderConfig, true));
    }

    public function getShortTitle() {
        // $this->logger->log(Logger::ERR, "Hallo1!");
        // TODO Remove (proof of concept)
        return parent::getShortTitle();
    }

    /**
     * @return RenderGroupConfig[]
     */
    public function getRenderConfig() {
        return $this->renderConfig->entries();
    }

    /**
     * @param int $marcIndex
     * @param CompoundEntry|SingleEntry $rc
     * @param Callable $renderer called with $subMap, $marcField, CompoundEntry|SingleEntry, $isFirst, $isLast
     */
    public function applyRenderer($marcIndex, $rc, $renderer) {
        $field = $this->getMarcField($marcIndex);
        echo "<!-- AR:\n " . $marcIndex . "\n" . print_r($field, true) . " -->\n";
        if (!empty($field)) {
            if ($rc instanceof SingleEntry) {
                $map = $rc->buildMap();
                $subMap = $this->getMappedFieldData($field, $map, true);
                if (!empty($subMap['value'])) {
                    $renderer($subMap, $field, $rc, true, true);
                }
            } else if ($rc instanceof CompoundEntry) {
                $array = $rc->elements;
                $values = [];
                // filter out empty values
                foreach ($array as $elem) {
                    $map = $elem->buildMap();
                    $subMap = $this->getMappedFieldData($field, $map, true);
                    echo "<!-- MIV: $marcIndex: " . print_r($subMap, true) . " -->\n";
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