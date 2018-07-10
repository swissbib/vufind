<?php
namespace ElasticSearch\Module\Configuration;

$config = [
  'service_manager' => [
//    'abstract_factories' => ['ElasticSearch\VuFind\RecordDriver\PluginFactory'],
    'factories' => [
//      'ElasticSearchRecord' => 'ElasticSearch\VuFind\RecordDriver\Factory::getElasticSearchRecord',
      'ElasticSearch\RecordDriverPluginManager' => 'ElasticSearch\VuFind\Service\Factory::getRecordDriverPluginManager',
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
      'search_results' => ['ElasticSearch\VuFind\Search\Results\Factory::getElasticSearch'],
      'recorddriver' => [
//        'abstract_factories' => ['ElasticSearch\VuFind\RecordDriver\PluginFactory'],
        'factories' => [
          'elasticsearchRecordDriver' => 'ElasticSearch\VuFind\RecordDriver\Factory::getElasticSearchRecord',
        ],
      ],
    ],
  ],
  'elasticsearch' => [
    'plugin_managers' => [
      'recorddriver' => [
//        'abstract_factories' => ['ElasticSearch\VuFind\RecordDriver\PluginFactory'],
        'factories' => [
          'elasticsearch' => 'ElasticSearch\VuFind\RecordDriver\Factory::getElasticSearchRecord',
          'esperson' => 'ElasticSearch\VuFind\RecordDriver\Factory::getESPersonRecord',
          'esdefault' => 'ElasticSearch\VuFind\RecordDriver\Factory::getESSubjectRecord',
          'esbibliographicresource' => 'ElasticSearch\VuFind\RecordDriver\Factory::getESBibliographicResourceRecord',
          'esorganisation' => 'ElasticSearch\VuFind\RecordDriver\Factory::getESOrganisationRecord',
        ],
      ],
    ],
  ],
  'view_helpers' => [
    'invokables' => [
      'esperson' => 'ElasticSearch\View\Helper\ESPerson',
      'essubject' => 'ElasticSearch\View\Helper\ESSubject',
      'essubjectcollection' => 'ElasticSearch\View\Helper\ESSubjectCollection',
    ]
  ]
];

return $config;
