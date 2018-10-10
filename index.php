<?php

namespace Project;

use Project\Controller\IndexController;
use Project\Utilities\Tools;
use Tracy\Debugger;

ini_set('memory_limit', '-1');

\define('ROOT_PATH', getcwd());
date_default_timezone_set('Europe/Berlin');

//session_start();

require ROOT_PATH . '/vendor/autoload.php';

$route = 'index';

if (Tools::getValue('route') !== false) {
    $route = Tools::getValue('route');
}

$configuration = new Configuration();

if ($configuration->getEntryByName('environment') === 'develop') {
    Debugger::enable();
}

try {
    $routing = new Routing($configuration);
    $routing->startRoute($route);
} catch (\InvalidArgumentException $error) {
    $indexController = new IndexController($configuration, $route);
    try {
        $indexController->errorPageAction();
    } catch (\Twig_Error_Loader | \Twig_Error_Runtime | \Twig_Error_Syntax $e) {
        echo 'There is something wrong!';
        exit;
    }
}