<?php


namespace SwissCollections\RecordDriver;

use Swissbib\RecordDriver\SolrMarc as SwissbibSolrMarc;

/**
 * Class SolrMarc
 * @package SwissCollections\RecordDriver
 */
class SolrMarc extends SwissbibSolrMarc {
  public function getShortTitle() {
    // TODO Remove (proof of concept)
    return "SwissCollections: " . parent::getShortTitle();
  }
}