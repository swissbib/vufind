<?php

namespace ElasticSearch\Module\Configuration;

$config = [
  'service_manager' => [
//    'abstract_factories' => ['ElasticSearch\VuFind\RecordDriver\PluginFactory'],
    'factories' => [
      'ElasticSearchRecordDriver' => 'ElasticSearch\VuFind\RecordDriver\Factory::getElasticSearchRecord',
      'ElasticSearch\RecordDriverPluginManager' => 'ElasticSearch\VuFind\RecordDriver\Factory::getElasticSearchRecord',
    ],
  ],
  'vufind' => [
    'plugin_managers' => [
      'search_backend' => [
        'factories' => [
          'ElasticSearch' => 'ElasticSearch\VuFind\Search\Factory\ElasticSearchBackendFactory',
        ]
      ],

//      'search_options' => 'ElasticSearch\VuFind\Search\ElasticSearch\Options',
//      'search_params' => 'ElasticSearch\VuFind\Search\ElasticSearch\Params',
      'search_results' => 'ElasticSearch\VuFind\Search\Results\Factory::getElasticSearch',
      'recorddriver' => [
        'factories' => [
          'elasticsearch' => 'ElasticSearch\VuFind\RecordDriver\Factory::getElasticSearchRecord',
        ],
      ],
    ],

  ]
];

return $config;
