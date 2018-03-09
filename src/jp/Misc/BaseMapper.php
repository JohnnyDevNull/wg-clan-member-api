<?php

namespace jp\Misc;

use Interop\Container\ContainerInterface as Container;
use jp\Wargaming\Reader\Clans as ClansReader;
use jp\Wargaming\Reader\Wot as WotReader;
use jp\Wargaming\Reader\Wows as WowsReader;

class BaseMapper
{
    /**
     * @var Interop\Container\ContainerInterface
     */
    protected $container;

    /**
     * @var \Monolog\Logger
     */
    protected $logger;

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
     * @param Interop\Container\ContainerInterface $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;

        $logger = $container->get('logger');

        if(!empty($logger))
        {
            $this->logger = $logger;
        }
    }
}
