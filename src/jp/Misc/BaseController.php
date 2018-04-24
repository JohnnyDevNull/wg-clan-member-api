<?php

namespace jp\Misc;

use Interop\Container\ContainerInterface as Container;

class BaseController
{
    /**
     * @var \Interop\Container\ContainerInterface
     */
    protected $container;

    /**
     * @var \Monolog\Logger
     */
    protected $logger;

    /**
     * @param \Interop\Container\ContainerInterface $container
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
