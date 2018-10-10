<?php

use Project\ConfigurationInterface;

return [
    'database' => [
        'host' => ConfigurationInterface::DEFAULT_SERVER,
        ConfigurationInterface::USER => 'root',
        ConfigurationInterface::PASS => '',
        'database_name' => 'heidelaufserie',
    ],
    'environment' => 'develop'
];