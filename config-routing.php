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
        'showResults' => [
            'controller' => 'IndexController',
            'action' => 'showResultsAction',
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
        'duplicate' => [
            'controller' => 'JsonController',
            'action' => 'duplicateAction',
        ],
        'refreshSpeakerData' => [
            'controller' => 'JsonController',
            'action' => 'refreshSpeakerDataAction',
        ],
        'refreshRankingData' => [
            'controller' => 'JsonController',
            'action' => 'refreshRankingDataAction',
        ],
        'generateTimeMeasureData' => [
            'controller' => 'JsonController',
            'action' => 'generateTimeMeasureDataAction',
        ],
        'refreshFinishedRunner' => [
            'controller' => 'JsonController',
            'action' => 'refreshFinishedRunnerAction',
        ],
        'generateTimeMeasurePage' => [
            'controller' => 'IndexController',
            'action' => 'generateTimeMeasurePageAction',
        ],
        'uploadCompetitionResultsFile' => [
            'controller' => 'AdminController',
            'action' => 'uploadCompetitionResultsFileAction',
        ],
        'generateStatisticsByYear' => [
            'controller' => 'AdminController',
            'action' => 'generateStatisticsByYearAction',
        ],
        'competitionDay' => [
            'controller' => 'AdminController',
            'action' => 'competitionDayAction',
        ],
        'competitions' => [
            'controller' => 'AdminController',
            'action' => 'competitionsAction',
        ],
        'setStartTime' => [
            'controller' => 'AdminController',
            'action' => 'setStartTimeAction',
        ],
        'readFinishMeasureFile' => [
            'controller' => 'AdminController',
            'action' => 'readFinishMeasureFileAction',
        ],
        'generateCompetitionResultsAfterCompetitionEnd' => [
            'controller' => 'AdminController',
            'action' => 'generateCompetitionResultsAfterCompetitionEndAction',
        ],
        'deleteCompetitionData' => [
            'controller' => 'AdminController',
            'action' => 'deleteCompetitionDataAction',
        ],
        'deleteMeasureData' => [
            'controller' => 'AdminController',
            'action' => 'deleteMeasureDataAction',
        ],
        'migrateShortCode' => [
            'controller' => 'MigrateController',
            'action' => 'migrateShortCodeAction',
        ],
    ],
];