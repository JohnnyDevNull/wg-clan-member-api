<?php

namespace jp\Wargaming;

use jp\Wargaming\Region as Region;
use jp\Wargaming\Request as Request;

/**
 * Base class for all "Wargaming.NET" game apis, that are implemented.
 *
 * This class is used to distribute the region and request class to all api
 * reader classes.
 *
 * @package jp-wargaming-api-reader
 * @author Philipp John <info@jplace.de>
 * @copyright (c) 2016, Philipp John
 * @license http://opensource.org/licenses/MIT MIT see LICENSE.md
 */
abstract class Base
{
    /**
     * @var string
     */
    protected $api;

    /**
     * @var jp\Wargaming\Region
     */
    protected $region;

    /**
     * @var jp\Wargaming\Request
     */
    protected $request;

    /**
     * @param string $appId
     * @param string $region
     * @param string $lang
     */
    public function __construct($appId, $region = 'EU', $lang = 'en')
    {
        $this->region = new Region($appId, $region, $lang, $this->api);
        $this->request = new Request($this->region);
    }

    /**
     * @param string|string[] $fields
     * @return string
     */
    protected function toListString($fields)
    {
        if(is_array($fields))
        {
            return  implode(',', $fields);
        }

        return $fields;
    }

    /**
     * @param bool $bool
     */
    public function setSecure($bool)
    {
        $this->request->setSecure($bool);
    }

    /**
     * Sets the request method fix to POST
     */
    public function setPostMethod()
    {
        $this->request->setPostMethod();
    }

    /**
     * Sets the request method fix to GET
     */
    public function setGetMethod()
    {
        $this->request->setGetMethod();
    }

    /**
     * @param bool $bool
     */
    public function setDecode($bool)
    {
        $this->request->setDecode($bool);
    }

    /**
     * @param bool $bool
     */
    public function setPrettyPrint($bool)
    {
        $this->request->setPrettyPrint($bool);
    }

    /**
     * @param bool $bool
     */
    public function setAssoc($bool)
    {
        $this->request->setAssoc($bool);
    }

    /**
     * @return mixed[]
     */
    public function getLastRequest()
    {
        return $this->request->getLastRequest();
    }
}
