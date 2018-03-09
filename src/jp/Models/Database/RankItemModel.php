<?php
namespace jp\Models\Database;

use \jp\Misc\BaseModel as BaseModel;

class RankItemModel extends BaseModel
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     */
    private $typeId;

    /**
     * @var int
     */
    private $memberId;

    /**
     * @var mixed
     */
    private $value;

    /**
     * @return int
     */
    public function getId() { return $this->id; }

    /**
     * @return int
     */
    public function getTypeId() { return $this->typeId; }

    /**
     * @return int
     */
    public function getMemberId() { return $this->memberId; }

    /**
     * @return mixed
     */
    public function getValue() { return $this->value; }

    /**
     * @param int $id
     */
    public function setId($id) { $this->id = (int)$id; }

    /**
     * @param int $typeId
     */
    public function setTypeId($typeId) { $this->typeId = (int)$typeId; }

    /**
     * @param int $memberId
     */
    public function setMemberId($memberId) { $this->memberId = (int)$memberId; }

    /**
     * @param mixed $value
     */
    public function setValue($value) { $this->value = $value; }
}