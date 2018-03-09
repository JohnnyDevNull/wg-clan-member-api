<?php
namespace jp\Controllers;

use jp\Misc\BaseController as BaseController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\RequestInterface as Request;

class SearchController extends BaseController
{
    public function getPlayer(Request $request, Response $response, $args)
    {
        return 'getPlayer';
    }

    public function getClan(Request $request, Response $response, $args)
    {
        return 'getClan';
    }
}
