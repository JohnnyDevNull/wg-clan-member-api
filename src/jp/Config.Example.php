<?php
namespace jp;

class Config
{
    public function getSettings()
    {
        return [
            'settings' => [
                // set to false in production
                'displayErrorDetails' => true,
                // Allow the web server to send the content-length header
                'addContentLengthHeader' => false,
                // determine middelware to work
                "determineRouteBeforeAppMiddleware" => true,
                // database settings
                'db' => [
                    'host' => 'localhost',
                    'user' => 'user',
                    'pass' => 'password',
                    'dbname' => 'database',
                    'port' => '3306'
                ],
                // Monolog settings
                'logger' => [
                    'name' => 'wows-api',
                    'path' => isset($_ENV['docker']) ? 'php://stdout' : BASEPATH.DS.'..'.DS.'logs'.DS.'app.log',
                    'level' => \Monolog\Logger::DEBUG,
                ],
                'wg' => [
                    'app_id' => 'your_api_key',
                    'region' => 'EU',
                    'lang' => 'de',
                    'api' => 'worldofwarships',
                    'request_offset' => 1800
                ],
                'json_pretty_print' => false
            ],
        ];
    }
}
