<?php
namespace SwissbibRdfDataApi\Module\Configuration;

$config = [
  'service_manager' => [
//    'abstract_factories' => ['ElasticSearch\VuFind\RecordDriver\PluginFactory'],
    'factories' => [
//      'ElasticSearchRecord' => 'ElasticSearch\VuFind\RecordDriver\Factory::getElasticSearchRecord',
        'SwissbibRdfDataApi\VuFind\RecordDriver\PluginManager' => 'SwissbibRdfDataApi\VuFind\Service\Factory::getRecordDriverPluginManager',
    ],
  ],
  'vufind' => [
    'plugin_managers' => [
      'search_backend' => [
        'factories' => [
          'SwissbibRdfDataApi' => 'SwissbibRdfDataApi\VuFind\Search\Factory\RdfDataApiBackendFactory',
        ]
      ],

//      'search_options' => 'ElasticSearch\VuFind\Search\ElasticSearch\Options',
//      'search_params' => 'ElasticSearch\VuFind\Search\ElasticSearch\Params',
      'search_results' => ['SwissbibRdfDataApi\VuFind\Search\Results\Factory::getElasticSearch'],
      'recorddriver' => [
//        'abstract_factories' => ['ElasticSearch\VuFind\RecordDriver\PluginFactory'],
        'factories' => [
          'SwissbibRdfDataApiRecordDriver' => 'SwissbibRdfDataApi\VuFind\RecordDriver\Factory::getRestApiSearchRecord',
        ],
      ],
    ],
  ],
  'restapi' => [
    'plugin_managers' => [
      'recorddriver' => [
//        'abstract_factories' => ['ElasticSearch\VuFind\RecordDriver\PluginFactory'],
        'factories' => [
          'restapi' => 'SwissbibRdfDataApi\VuFind\RecordDriver\Factory::getElasticSearchRecord',
          'APIPerson' => 'SwissbibRdfDataApi\VuFind\RecordDriver\Factory::getAPIPersonRecord',
          'apidefault' => 'SwissbibRdfDataApi\VuFind\RecordDriver\Factory::getAPISubjectRecord',
          'APIBibliographicResource' => 'SwissbibRdfDataApi\VuFind\RecordDriver\Factory::getAPIBibliographicResourceRecord',
          'APISubject' => 'SwissbibRdfDataApi\VuFind\RecordDriver\Factory::getAPISubjectRecord',
          'apiorganisation' => 'SwissbibRdfDataApi\VuFind\RecordDriver\Factory::getAPIOrganisationRecord',
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
