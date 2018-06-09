<?php
namespace jp\Controllers;

use jp\Misc\BaseController;
use jp\Mappers\EntityMapper;
use jp\Mappers\ApiMapper;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\RequestInterface as Request;
use jp\Mappers\ProgressMapper;

class ProgressController extends BaseController
{
    public function generatePlayerProgress($accountId)
    {
        $mapper = new ProgressMapper($this->container);
        $progress = $mapper->generatePlayerProgress($accountId);
        $mapper->upsertProgress($accountId, json_encode($progress));
        return $progress;
    }

    public function refreshPlayerProgress($accountId)
    {
        return false;
    }
}
