<?php

namespace jp\Mappers;

use Interop\Container\ContainerInterface as Container;
use jp\Misc\BaseMapper as BaseMapper;
use jp\Mappers\EntityMapper as EntityMapper;
use jp\Wargaming\Reader\Clans as ClansReader;
use jp\Wargaming\Reader\Wot as WotReader;
use jp\Wargaming\Reader\Wows as WowsReader;

class ApiMapper extends BaseMapper
{
    /**
     * @var \jp\Wargaming\Reader\Clans
     */
    protected $clanReader;

    /**
     * @var \jp\Wargaming\Reader\Wows
     */
    protected $wowsReader;

    /**
     * @var \jp\Wargaming\Reader\Wot
     */
    protected $wotReader;

    /**
     * @var string[]
     */
    protected $wgSettings;

    /**
     * @var \jp\Mappers\EntityMapper
     */
    protected $entityMapper;

    /**
     * @param \Interop\Container\ContainerInterface $container
     */
    public function __construct(Container $container)
    {
        parent::__construct($container);

        $this->wgSettings = $container->get('settings')['wg'];

        $this->clanReader = new ClansReader(
            $this->wgSettings['app_id'],
            $this->wgSettings['region'],
            $this->wgSettings['lang']
        );

        $this->wowsReader = new WowsReader(
            $this->wgSettings['app_id'],
            $this->wgSettings['region'],
            $this->wgSettings['lang']
        );

        $this->wotReader = new WotReader(
            $this->wgSettings['app_id'],
            $this->wgSettings['region'],
            $this->wgSettings['lang']
        );

        $this->entityMapper = new EntityMapper($container);
    }

    /**
     * @param int $clanId
     *
     * @return string
     * @throws \Exception
     */
    public function getClan($clanId)
    {
        if ($this->wgSettings['api'] != 'worldofwarships') {
            throw new \LogicException('not implemented yet');
        }

        $data = $this->entityMapper->getLastResult($clanId, 'clan', new \DateTime());

        if ($data === false) {
            $data = $this->wowsReader->getClanInfo((int)$clanId, '', 'members');
            $this->entityMapper->saveMembers($clanId, $data);
            $this->entityMapper->saveResult($clanId, 'clan', $data, +-true);
        } else {
            return $this->getJson($data['data']);
        }

        return $this->getJson($data);
    }

    /**
     * @param int $playerId
     *
     * @return string
     * @throws \Exception
     */
    public function getPlayer($playerId)
    {
        if ($this->wgSettings['api'] != 'worldofwarships') {
            throw new \LogicException('not implemented yet');
        }

        $data = $this->entityMapper->getLastResult($playerId, 'player', new \DateTime());

        if ($data === false) {
            $data = $this->wowsReader->getAccountInfo(
                $playerId,
                '',
                '',
                'statistics.pvp_solo,'
                    .'statistics.pvp_div2,'
                    .'statistics.pvp_div3,'
                    .'statistics.club,'
                    .'statistics.pve,'
                    .'statistics.rank_solo'
            );
            $this->entityMapper->saveResult($playerId, 'player', $data);
        } else {
            return $this->getJson($data['data']);
        }

        return $this->getJson($data);
    }

    /**
     * @param string $name
     * @return string
     * @throws \LogicException
     */
    public function searchClan($name)
    {
        if ($this->wgSettings['api'] != 'worldofwarships') {
            throw new \LogicException('not implemented yet');
        }

        return $this->getJson($this->wowsReader->getClanList($name));
    }

    /**
     * @param string $name
     * @return string
     * @throws \LogicException
     */
    public function searchPlayer($name)
    {
        if ($this->wgSettings['api'] != 'worldofwarships') {
            throw new \LogicException('not implemented yet');
        }

        return $this->getJson($this->wowsReader->getAccountList($name));
    }
}
