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

    public function getPlayerInfo(Request $request, Response $response, $args)
    {
        $playerId = $this->getPlayerIdFromArgs($args);
        $apiMapper = new ApiMapper($this->container);
        return $apiMapper->getPlayer($playerId);
    }
}
