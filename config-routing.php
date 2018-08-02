<?php
return [
    'route' => [
        'index' => [
            'controller' => 'IndexController',
            'action' => 'indexAction',
        ],
        'speaker' => [
            'controller' => 'IndexController',
            'action' => 'speakerAction',
        ],
        'admin' => [
            'controller' => 'AdminController',
            'action' => 'adminAction',
        ],
        'createCompetition' => [
            'controller' => 'AdminController',
            'action' => 'createCompetitionAction',
        ],
        'uploadRunnerFile' => [
            'controller' => 'AdminController',
            'action' => 'uploadRunnerFileAction',
        ],
        'findDuplicateNames' => [
            'controller' => 'AdminController',
            'action' => 'findDuplicateNamesAction',
        ],
        'noDuplicate' => [
            'controller' => 'JsonController',
            'action' => 'noDuplicateAction',
        ],
        'refreshSpeakerData' => [
            'controller' => 'JsonController',
            'action' => 'refreshSpeakerDataAction',
        ],
        'generateTimeMeasureData' => [
            'controller' => 'JsonController',
            'action' => 'generateTimeMeasureDataAction',
        ],
        'generateTimeMeasurePage' => [
            'controller' => 'IndexController',
            'action' => 'generateTimeMeasurePageAction',
        ],
    ],
];