<?php
return [
    'route' => [
        'index' => [
            'controller' => 'IndexController',
            'action' => 'indexAction'
        ],
        'admin' => [
            'controller' => 'IndexController',
            'action' => 'adminAction'
        ],
        'importCsv' => [
            'controller' => 'IndexController',
            'action' => 'importCsvAction'
        ],
        'createCompetition' => [
            'controller' => 'IndexController',
            'action' => 'createCompetitionAction'
        ],
        'createCompetitionDay' => [
            'controller' => 'IndexController',
            'action' => 'createCompetitionDayAction'
        ],
        'uploadRunnerFile' => [
            'controller' => 'IndexController',
            'action' => 'uploadRunnerFileAction'
        ],
        'sendmail' => [
            'controller' => 'MailerController',
            'action' => 'sendMailAction'
        ]
    ]
];