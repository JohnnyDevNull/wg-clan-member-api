<?php
namespace jp\Controllers;

use jp\Misc\BaseController;
use jp\Mappers\EntityMapper;
use jp\Mappers\ApiMapper;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\RequestInterface as Request;

class ClansController extends BaseController
{
    /**
     * @param array $args
     * @return int
     */
    private function getClanIdFromArgs(array $args)
    {
        return (int)$args['clanId'];
    }

     /**
     * @param array $args
     * @return int
     */
    private function getMemberIdFromArgs(array $args)
    {
        return (int)$args['memberId'];
    }

    public function getList(Request $request, Response $response)
    {
        $clanList = json_encode
        (
            [ 'items' => $this->container->get('settings')['clans'] ],
            JSON_PRETTY_PRINT
        );

        $response->getBody()->write($clanList);

        return $response;
    }

    public function getClan(Request $request, Response $response, $args)
    {
        $clanId = $this->getClanIdFromArgs($args);
        $clans = $this->container->get('settings')['clans'];
        $res = [ 'items' => [] ];

        foreach($clans as $clan)
        {
            if ((int)$clan['id'] == $clanId)
            {
                $res['items'][] = $clan;
            }
        }

        $response->getBody()->write(json_encode($res, JSON_PRETTY_PRINT));

        return $response;
    }

    public function getClanInfo(Request $request, Response $response, $args)
    {
        $clanId = $this->getClanIdFromArgs($args);
        $apiMapper = new ApiMapper($this->container);
        return $apiMapper->getClan($clanId);
    }

    public function getMemberList(Request $request, Response $response, $args)
    {
        $clanId = $this->getClanIdFromArgs($args);
        $entityMapper = new EntityMapper($this->container);
        return $entityMapper->getMembersByClanId($clanId);
    }

    public function getMember(Request $request, Response $response, $args)
    {
        $clanId = $this->getClanIdFromArgs($args);
        $memberId = $this->getMemberIdFromArgs($args);
        $entityMapper = new EntityMapper($this->container);
        return $entityMapper->getMemberModelById($clanId, $memberId);
    }

    public function getRanks(Request $request, Response $response, $args)
    {
        $clanId = $this->getClanIdFromArgs($args);
        $entityMapper = new EntityMapper($this->container);
        return $entityMapper->getRankItemsByClanId($clanId);
    }
}
