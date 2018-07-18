<?php
return [
    'route' => [
        'index' => [
            'controller' => 'IndexController',
            'action' => 'indexAction'
        ],
        'admin' => [
            'controller' => 'AdminController',
            'action' => 'adminAction'
        ],
        'createCompetition' => [
            'controller' => 'AdminController',
            'action' => 'createCompetitionAction'
        ],
        'importCsv' => [
            'controller' => 'AdminController',
            'action' => 'importCsvAction'
        ],
        'uploadRunnerFile' => [
            'controller' => 'AdminController',
            'action' => 'uploadRunnerFileAction'
        ],
        'sendmail' => [
            'controller' => 'MailerController',
            'action' => 'sendMailAction'
        ],
        'findDuplicateNames' => [
            'controller' => 'AdminController',
            'action' => 'findDuplicateNamesAction'
        ],
        'findDuplicatesByLevenshtein' => [
            'controller' => 'AdminController',
            'action' => 'findDuplicatesByLevenshteinAction'
        ]
    ]
];