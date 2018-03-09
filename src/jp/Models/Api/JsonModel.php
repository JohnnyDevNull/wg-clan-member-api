<?php
namespace jp\Models\Api;

use \jp\Misc\BaseModel as BaseModel;

class JsonModel extends BaseModel
{
    /**
     * @var string
     */
    private $json;

    /**
     * @param string $json
     */
    public function setJson($json)
    {
        $this->json = $json;
    }

    /**
     * @return string
     */
    public function getJson()
    {
        if($this->container->get('settings')['json_pretty_print'])
        {
            $stdClass = $this->getStdClass();
            return json_encode($stdClass, JSON_PRETTY_PRINT);
        }
        else
        {
            return $this->json;
        }
    }

    /**
     * @return \stdClass
     */
    public function getStdClass()
    {
        return json_decode($this->json);
    }

    /**
     * @return array
     */
    public function getAssoc()
    {
        return json_decode($this->json, true);
    }
}
