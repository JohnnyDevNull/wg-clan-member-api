<?php
namespace jp\Controllers;

use jp\Misc\BaseController as BaseController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\RequestInterface as Request;

class SearchController extends BaseController
{
    /**
     * @param array $args
     * @return int
     */
    private function getNameFromArgs(array $args)
    {
        return filter_var($args['name'], FILTER_SANITIZE_STRING);
    }

    /**
     * @param \Psr\Http\Message\RequestInterface  $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param array                               $args
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getPlayer(Request $request, Response $response, $args)
    {
        $name = $this->getNameFromArgs($args);
        $apiMapper = new \jp\Mappers\ApiMapper($this->container);
        $response->getBody()->write($apiMapper->searchPlayer($name));
        return $response;
    }

    /**
     * @param \Psr\Http\Message\RequestInterface  $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param array                               $args
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getClan(Request $request, Response $response, $args)
    {
        $name = $this->getNameFromArgs($args);
        $apiMapper = new \jp\Mappers\ApiMapper($this->container);
        $response->getBody()->write($apiMapper->searchClan($name));
        return $response;
    }
}
