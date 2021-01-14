<?php

namespace SwissCollections\RecordDriver;

use Laminas\ServiceManager\ServiceManager;
use Swissbib\RecordDriver\Factory as SwissbibFactory;
use ParseCsv;

/**
 * Class Factory
 *
 * @package SwissCollections\RecordDriver
 */
class Factory extends SwissbibFactory
{

    /**
     * Get SolrMarcRecordDriver
     *
     * @param ServiceManager $sm ServiceManager
     *
     * @return SolrMarc
     */
    public static function getSolrMarcRecordDriver(ServiceManager $sm)
    {
        // TODO needs own subclass, because of caching or
        // use $yamlReader->get('detail-view-field-structure',true, true) instead of ->get('...')
        $yamlReader = $sm->get('VuFind\Config\YamlReader');
        $detailViewFieldInfo = $yamlReader->get('detail-view-field-structure.yaml', true, true);

        $csvFile = __DIR__ . '/../../../../../local/classic/swisscollections/config/vufind/detail-fields.csv';
        $fieldMarcMapping = new ParseCsv\Csv($csvFile);

        $driver = new SolrMarc(
            $sm->get('VuFind\Config\PluginManager')->get('config'),
            null,
            $sm->get('VuFind\Config\PluginManager')->get('searches'),
            $sm->get('Swissbib\HoldingsHelper'),
            $sm->get('Swissbib\RecordDriver\SolrDefaultAdapter'),
            $sm->get('Swissbib\Availability'),
            $sm->get('VuFind\Config\PluginManager')->get('Holdings')->AlephNetworks
                ->toArray(),
            $logger = $sm->get('VuFind\Log\Logger')
        );
        $driver->attachILS(
            $sm->get('VuFind\ILS\Connection'),
            $sm->get('VuFind\ILS\Logic\Holds'),
            $sm->get('VuFind\ILS\Logic\TitleHolds')
        );
        $driver->setDetailViewFieldInfo($detailViewFieldInfo);
        $driver->setFieldMarcMapping($fieldMarcMapping->data);
        $driver->buildRenderInfo();

        return $driver;
    }
}
