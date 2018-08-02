<?php

use Project\ConfigurationInterface;

return [
    'project' => [
        'name' => 'Testproject',
        'namespace' => 'Project',
    ],
    'template' => [
        'name' => 'default',
        'dir' => '/default',
        'main_css_path' => '/css/main.css',
    ],
    'database' => [
        'host' => ConfigurationInterface::DEFAULT_SERVER,
        ConfigurationInterface::USER => 'root',
        ConfigurationInterface::PASS => '',
        'database_name' => 'heidelaufserie',
    ],
    'controller' => [
        'namespace' => 'Controller',
    ],
    'mailer' => [
        'server' => ConfigurationInterface::DEFAULT_SERVER,
        'port' => 25,
        ConfigurationInterface::USER => 'web1061p1',
        ConfigurationInterface::PASS => 'j5q9hCZp',
        'standard_from_mail' => 'test@boilerplate.ms2002.alfahosting.org',
        'standard_from_name' => 'John Doe',
    ],
    'ageTable' => [
        'K' => ['min' => 1, 'max' => 11],
        'J U16' => ['min' => 12, 'max' => 15],
        'J U20' => ['min' => 16, 'max' => 19],
        '20' => ['min' => 20, 'max' => 29],
        '30' => ['min' => 30, 'max' => 34],
        '35' => ['min' => 35, 'max' => 39],
        '40' => ['min' => 40, 'max' => 44],
        '45' => ['min' => 45, 'max' => 49],
        '50' => ['min' => 50, 'max' => 54],
        '55' => ['min' => 55, 'max' => 59],
        '60' => ['min' => 60, 'max' => 64],
        '65' => ['min' => 65, 'max' => 69],
        '70' => ['min' => 70, 'max' => 74],
        '75' => ['min' => 75, 'max' => 79],
        '80' => ['min' => 80, 'max' => 84],
        '85' => ['min' => 85, 'max' => 89],
        '90' => ['min' => 90, 'max' => 94],
    ],
    'startingTime' => '2018-08-02 10:00:00',
    'ranking' => [
        'woman' => [
            'gender' => 'w',
            'competitionTypeId' => 2,
            'amount' => 3,
        ],
        'man' => [
            'gender' => 'm',
            'competitionTypeId' => 3,
            'amount' => 6,
        ],
    ],
];