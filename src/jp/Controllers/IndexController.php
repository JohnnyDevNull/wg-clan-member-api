<?php
namespace jp\Controllers;

use jp\Misc\BaseController as BaseController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\RequestInterface as Request;

class IndexController extends BaseController
{
    public function index(Request $request, Response $response, $args)
    {
        $response->getBody()->write (
            '<div style="margin: auto; position: absolute; top: 0; left: 0; right: 0; bottom: 0; text-align: center;">'
                .'<h1>Online</h1>'
            .'</div>');

        return $response;
    }
}
