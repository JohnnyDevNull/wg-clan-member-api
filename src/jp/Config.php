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
                // database settings
                'db' => [
                    'host' => 'localhost',
                    'user' => 'user',
                    'pass' => 'pass',
                    'dbname' => 'dbname',
                    'port' => '3306'
                ],
                // Monolog settings
                'logger' => [
                    'name' => 'wows-api',
                    'path' => isset($_ENV['docker']) ? 'php://stdout' : BASEPATH.DS.'..'.DS.'logs'.DS.'app.log',
                    'level' => \Monolog\Logger::DEBUG,
                ],
                'clans' =>
                [
                    [
                        'id' => '500137827',
                        'tag' => 'SPVO',
                        'name' => 'Spitze Voraus'
                    ],
                    [
                        'id' => '500146159',
                        'tag' => 'SPVOF',
                        'name' => 'Spitze Voraus Fun'
                    ]
                ],
                'wg' => [
                    'app_id' => '03e3653b14d26e8136d5870a1512e3c4',
                    'region' => 'EU',
                    'lang' => 'de',
                    'api' => 'worldofwarships'
                ],
                'json_pretty_print' => true
            ],
        ];
    }

    public function registerLogger(\Slim\App $app)
    {
        $container = $app->getContainer();
        // monolog
        $container['logger'] = function ($c)
        {
            $settings = $c->get('settings')['logger'];
            $logger = new \Monolog\Logger($settings['name']);
            $logger->pushProcessor(new \Monolog\Processor\UidProcessor());
            $logger->pushHandler(new \Monolog\Handler\StreamHandler($settings['path'], $settings['level']));
            return $logger;
        };
    }

    public function registerCsrf(\Slim\App $app)
    {
        $app->add(new \Slim\Csrf\Guard);
    }
}
