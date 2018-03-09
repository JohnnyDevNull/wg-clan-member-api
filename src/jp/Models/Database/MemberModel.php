<?php
namespace jp\Models\Database;

use \jp\Misc\BaseModel as BaseModel;

class MemberModel extends BaseModel
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     */
    private $clanId;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $dateJoined;

    /**
     * @var string
     */
    private $dateLastBattle;

    /**
     * @return int
     */
    public function getId() { return $this->id; }

    /**
     * @return int
     */
    public function getClanId() { return $this->clanId; }

    /**
     * @return string
     */
    public function getName() { return $this->name; }

    /**
     * @return string
     */
    public function getDateJoined() { return $this->dateJoined; }

    /**
     * @return string
     */
    public function getDateLastBattle() { return $this->dateLastBattle; }

    /**
     * @param int $id
     */
    public function setId($id) { $this->id = (int)$id; }

    /**
     * @param int $clanId
     */
    public function setClanId($clanId) { $this->clanId = (int)$clanId; }

    /**
     * @param string $name
     */
    public function setName($name) { $this->name = (string)$name; }

    /**
     * @param string $dateJoined
     */
    public function setDateJoined($dateJoined) { $this->dateJoined = (string)$dateJoined; }

    /**
     * @param string $dateLastBattle
     */
    public function setDateLastBattle($dateLastBattle) { $this->dateLastBattle = (string)$dateLastBattle; }
}
