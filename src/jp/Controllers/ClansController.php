<?php
namespace jp\Controllers;

use jp\Misc\BaseController as BaseController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\RequestInterface as Request;

class ClansController extends BaseController
{
    private function getClanIdFromArgs(array $args)
    {
        return (int)$args['clanId'];
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
        $apiMapper = new \jp\Mappers\ApiMapper($this->container);
        $jsonModel = $apiMapper->getClan($clanId);
        return $jsonModel->getJson();
    }


    public function getMemberList(Request $request, Response $response, $args)
    {
        return 'getMemberList';
    }

    public function getMember(Request $request, Response $response, $args)
    {
        return 'getMember';
    }

    public function getRanks(Request $request, Response $response, $args)
    {
        return 'getRanks';
    }
}
