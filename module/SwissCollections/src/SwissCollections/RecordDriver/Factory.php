<?php

namespace SwissCollections\RecordDriver;

use Laminas\ServiceManager\ServiceManager;
use Swissbib\RecordDriver\Factory as SwissbibFactory;

/**
 * Class Factory
 * @package SwissCollections\RecordDriver
 */
class Factory extends SwissbibFactory {

  /**
   * Get SolrMarcRecordDriver
   *
   * @param ServiceManager $sm ServiceManager
   *
   * @return SolrMarc
   */
  public static function getSolrMarcRecordDriver(ServiceManager $sm) {
    $driver = new SolrMarc(
      $sm->get('VuFind\Config\PluginManager')->get('config'),
      NULL,
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

    return $driver;
  }
}
