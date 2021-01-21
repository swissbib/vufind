<?php

use SwissCollections\RecordDriver\ResultListViewFieldInfo;
use SwissCollections\View\Helper\Root\Browse;
use SwissCollections\View\Helper\Root\BrowseFactory;
use SwissCollections\View\Helper\Root\ResultListViewConfigFactory;

return [
    'extends' => 'sbvfrdsingle',
    'helpers' => [
        'factories' => [
            Browse::class => BrowseFactory::class,
            ResultListViewFieldInfo::class => ResultListViewConfigFactory::class,
        ],
        'aliases' => [
            'browse' => Browse::class,
            'resultListViewConfig' => ResultListViewFieldInfo::class,
        ]
    ]
];
