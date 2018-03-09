<?php

namespace jp\Mappers;

use Interop\Container\ContainerInterface as Container;
use \jp\Misc\BaseMapper as BaseMapper;
use jp\Wargaming\Reader\Clans as ClansReader;
use jp\Wargaming\Reader\Wot as WotReader;
use jp\Wargaming\Reader\Wows as WowsReader;
use jp\Models\Api\JsonModel as JsonModel;

class ApiMapper extends BaseMapper
{
    /**
     * @var jp\Wargaming\Reader\Clans
     */
    protected $clanReader;

    /**
     * @var jp\Wargaming\Reader\Wows
     */
    protected $wowsReader;

    /**
     * @var jp\Wargaming\Reader\Wot
     */
    protected $wotReader;

    /**
     * @var string[]
     */
    protected $wgSettings;

    /**
     * @param Interop\Container\ContainerInterface $container
     */
    public function __construct(Container $container)
    {
        parent::__construct($container);

        $this->wgSettings = $container->get('settings')['wg'];

        $this->clanReader = new ClansReader (
            $this->wgSettings['app_id'],
            $this->wgSettings['region'],
            $this->wgSettings['lang']
        );

        $this->wowsReader = new WowsReader (
            $this->wgSettings['app_id'],
            $this->wgSettings['region'],
            $this->wgSettings['lang']
        );

        $this->wotReader = new WotReader (
            $this->wgSettings['app_id'],
            $this->wgSettings['region'],
            $this->wgSettings['lang']
        );
    }

    /**
     * @param int $clanId
     * @return JsonModel
     * @throws \LogicException
     */
    public function getClan($clanId)
    {
        if ($this->wgSettings['api'] != 'worldofwarships')
        {
            throw new \LogicException('not implemented yet');
        }

        $clanInfo = $this->wowsReader->getClanInfo((int)$clanId);

        $jsonModel = New JsonModel($this->container);
        $jsonModel->setJson($clanInfo);

        return $jsonModel;
    }
}
