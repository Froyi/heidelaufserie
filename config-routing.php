<?php
return [
    'route' => [
        'index' => [
            'controller' => 'IndexController',
            'action' => 'indexAction',
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
        'sendmail' => [
            'controller' => 'MailerController',
            'action' => 'sendMailAction',
        ],
        'findDuplicateNames' => [
            'controller' => 'AdminController',
            'action' => 'findDuplicateNamesAction',
        ],
        'noDuplicate' => [
            'controller' => 'JsonController',
            'action' => 'noDuplicateAction',
        ],
    ],
];