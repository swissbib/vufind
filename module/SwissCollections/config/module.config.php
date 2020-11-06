<?php

namespace SwissCollections\Module\Configuration;

$config = [
  'vufind' => [
    'recorddriver_tabs' => [
      'SwissCollections\RecordDriver\SolrMarc' => [
        'tabs' => [
          'Holdings' => 'HoldingsILS',
          'Description' => 'Description',
          'TOC' => 'TOC',
          'UserComments' => 'UserComments',
          'Reviews' => 'Reviews',
          'Excerpt' => 'Excerpt',
          'Preview' => 'preview',
          'HierarchyTree' => 'HierarchyTree',
          'HierarchyTreeArchival' => 'HierarchyTreeArchival',
          'Map' => 'Map',
          'Similar' => 'SimilarItemsCarousel',
          'Details' => 'StaffViewMARC',
        ],
        'defaultTab' => NULL,
      ],
    ],
    'plugin_managers' => [
      'recorddriver' => [
        'factories' => [
          'SwissCollections\RecordDriver\SolrMarc' => 'SwissCollections\RecordDriver\Factory::getSolrMarcRecordDriver',
        ],
        'aliases' => [
          'solrmarc' => 'SwissCollections\RecordDriver\SolrMarc',
        ],
      ],
    ],
  ],
];

return $config;
