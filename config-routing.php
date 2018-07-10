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
        'sendmail' => [
            'controller' => 'MailerController',
            'action' => 'sendMailAction'
        ]
    ]
];