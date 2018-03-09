<?php
namespace jp\Models\Database;

use \jp\Misc\BaseModel as BaseModel;

class ClanModel extends BaseModel
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $tag;

    /**
     * @return int
     */
    public function getId() { return $this->id; }

    /**
     * @return string
     */
    public function getName() { return $this->name; }

    /**
     * @return string
     */
    public function getTag() { return $this->tag; }

    /**
     * @param int $id
     */
    public function setId($id) { $this->id = $id; }

    /**
     * @param string $name
     */
    public function setName($name) { $this->name = $name; }

    /**
     * @param string $tag
     */
    public function setTag($tag) { $this->tag = $tag; }
}
