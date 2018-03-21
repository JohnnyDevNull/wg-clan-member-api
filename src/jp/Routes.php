<?php

namespace jp;

class Routes
{
    public function attach(\Slim\App $app)
    {
        $app->get('/', \jp\Controllers\IndexController::class.':index');
        $app->get('/ranks', \jp\Controllers\IndexController::class.':getRanks');

        $app->get('/clans', \jp\Controllers\ClansController::class.':getList');
        $app->get('/clans/{clanId:[0-9]+}', \jp\Controllers\ClansController::class.':getClan');
        $app->get('/clans/{clanId:[0-9]+}/info', \jp\Controllers\ClansController::class.':getClanInfo');
        $app->get('/clans/{clanId:[0-9]+}/members', \jp\Controllers\ClansController::class.':getMemberList');
        $app->get('/clans/{clanId:[0-9]+}/members/{memberId:[0-9]+}', \jp\Controllers\ClansController::class.':getMember');
        $app->get('/clans/{clanId:[0-9]+}/ranks', \jp\Controllers\ClansController::class.':getRanks');

        $app->get('/players/{playerId:[0-9]+/info}', \jp\Controllers\PlayersController::class.':getPlayerInfo');

        $app->get('/search/player/{name}', \jp\Controllers\SearchController::class.':getPlayer');
        $app->get('/search/clan/{name}', \jp\Controllers\SearchController::class.':getClan');
    }
}
