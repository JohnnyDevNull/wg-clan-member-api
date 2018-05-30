<?php
namespace jp\Controllers;

use jp\Misc\BaseController;
use jp\Mappers\EntityMapper;
use jp\Mappers\ApiMapper;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\RequestInterface as Request;

class PlayersController extends BaseController
{
    /**
     * @param array $args
     * @return int
     */
    private function getPlayerIdFromArgs(array $args)
    {
        return (int)$args['playerId'];
    }

    /**
     * @param \Psr\Http\Message\RequestInterface  $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param array                               $args
     *
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Exception
     */
    public function getPlayerInfo(Request $request, Response $response, array $args)
    {
        $playerId = $this->getPlayerIdFromArgs($args);
        $apiMapper = new ApiMapper($this->container);
        $response->getBody()->write($apiMapper->getPlayer($playerId));
        return $response;
    }
}
