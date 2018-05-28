<?php
namespace jp\Controllers;

use jp\Misc\BaseController;
use jp\Mappers\EntityMapper;
use jp\Models\Api\JsonModel;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\RequestInterface as Request;

class IndexController extends BaseController
{
    /**
     * @param \Psr\Http\Message\RequestInterface  $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param                                     $args
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function index(Request $request, Response $response, $args)
    {
        $response->getBody()->write(
            '<div style="margin: auto; position: absolute; top: 0; left: 0; right: 0; bottom: 0; text-align: center;">'
                .'<h1>Online</h1>'
            .'</div>'
        );

        return $response;
    }

    /**
     * @param \Psr\Http\Message\RequestInterface  $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param                                     $args
     *
     * @return string
     * @throws \Exception
     */
    public function getRanks(Request $request, Response $response, $args)
    {
        $entityMapper = new EntityMapper($this->container);
        $members = $entityMapper->getRankTypes();
        $apiJsonModel = new JsonModel($this->container);
        $apiJsonModel->setJson(json_encode($members));

        return $apiJsonModel->getJson();
    }
}
