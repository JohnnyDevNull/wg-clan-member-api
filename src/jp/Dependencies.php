<?php
namespace jp;

class Dependencies
{
    public function addMiddlerware(\Slim\App $app)
    {
        $this->registerLogger($app);
        $this->registerCORS($app);
    }

    private function registerLogger(\Slim\App $app)
    {
        $container = $app->getContainer();
        // monolog
        $container['logger'] = function ($c) {
            $settings = $c->get('settings')['logger'];
            $logger = new \Monolog\Logger($settings['name']);
            $logger->pushProcessor(new \Monolog\Processor\UidProcessor());
            $logger->pushHandler(new \Monolog\Handler\StreamHandler($settings['path'], $settings['level']));
            return $logger;
        };
    }

    private function registerCsrf(\Slim\App $app)
    {
        $app->add(new \Slim\Csrf\Guard);
    }

    private function registerCORS(\Slim\App $app)
    {
        $app->add(function ($request, $response, $next) {
            $route = $request->getAttribute("route");

            $methods = [];

            if (!empty($route)) {
                $pattern = $route->getPattern();

                foreach ($this->router->getRoutes() as $route) {
                    if ($pattern === $route->getPattern()) {
                        $methods = array_merge_recursive($methods, $route->getMethods());
                    }
                }
                //Methods holds all of the HTTP Verbs that a particular route handles.
            } else {
                $methods[] = $request->getMethod();
            }

            $response = $next($request, $response);

            return $response
                ->withHeader('Access-Control-Allow-Origin', '*')
                ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
                ->withHeader("Access-Control-Allow-Methods", implode(",", $methods));
        });
    }
}
