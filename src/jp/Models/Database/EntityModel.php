<?php
namespace jp\Models\Database;

use \jp\Misc\BaseModel as BaseModel;

class EntityModel extends BaseModel
{
    /**
     * @var \stdClass
     */
    private $data;

    /**
     * @param \stdClass $data
     */
    public function setData(\stdClass $data)
    {
        $this->data = $data;
    }

    /**
     * @return \stdClass
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return string
     */
    public function toJson()
    {
        return json_encode($this->toArray());
    }

    /**
     * @param string $name
     * @return null|string
     */
    public function __get($name)
    {
        if(isset($this->data->{$name}))
        {
            return (string)$this->data->{$name};
        }

        return null;
    }

    public function toArray()
    {

    }
}
