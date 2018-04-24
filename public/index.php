<?php

if(!defined('BASEPATH'))
{
    define('BASEPATH', __DIR__);
}

if(!defined('DS'))
{
    define('DS', DIRECTORY_SEPARATOR);
}

if (PHP_SAPI == 'cli-server')
{
    // To help the built-in PHP dev server, check if the request was actually for
    // something which should probably be served as a static file
    $url  = parse_url($_SERVER['REQUEST_URI']);
    $file = BASEPATH.DS.trim($url['path'], DS);

    if (is_file($file))
    {
        return false;
    }
}

require BASEPATH.DS.'..'.DS.'vendor'.DS.'autoload.php';

session_start();

$config = new \jp\Config();
$routes = new \jp\Routes();

$app = new \Slim\App($config->getSettings());

$config->registerLogger($app);
$config->registerCORS($app);
$routes->attach($app);

$app->run();
