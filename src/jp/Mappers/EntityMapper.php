<?php

namespace \jp\Mappers;

use \jp\Misc\BaseMapper as BaseMapper;
use \jp\Misc\Database as Database;

class EntityMapper extends BaseMapper
{
    /**
     * @var \jp\Misc\Database 
     */
    private $db;

    /**
     * @param Interop\Container\ContainerInterface $container
     * @param \jp\Misc\Database
     */
    public function __constructor(Container $container, Database $db = null)
    {
        parent::__construct($container);

        if ($db !== null)
        {
            $this->db = $db;
        }
        else
        {
            $dbConf = $container->get('db');
            $this->db = Database::getInstance(
                $dbConf['host'],
                $dbConf['user'],
                $dbConf['pass'],
                $dbConf['db'],
                $dbConf['port']
            );
        }
    }

    public function getClanModelById($clanId)
    {
        $query = 'SELECT * FROM clans WHERE clan_id = ?';
        $stmt = $this->db->prepare($query);
        $stmt->bind('i', (int)$clanId);
        $stmt->execute();

        if ( ($res = $stmt->get_result()) != false )
        {
            $entity = $res->fetch_object();

            $model = new \jp\Models\CLanModel($this->container);
            $model->setId($entity->id);
            $model->setName($entity->name);
            $model->setTag($entity->tag);

            return $model;
        }

        return false;
    }

    public function getMembersModelById($memberId)
    {
        $query = 'SELECT * FROM members WHERE id = ?';
        $stmt = $this->db->prepare($query);
        $stmt->bind('i', (int)$memberId);
        $stmt->execute();

        if ( ($res = $stmt->get_result()) != false )
        {
            $entity = $res->fetch_object();

            $model = new \jp\Models\MemberModel($this->container);
            $model->setId($entity->id);
            $model->setClanId($entity->clan_id);
            $model->setName($entity->name);
            $model->setDateJoined($entity->date_joined);
            $model->setDateLastBattle($entity->date_last_battle);

            return $model;
        }

        return false;
    }

    /**
     * @param int $rankTypeId
     * @return \jp\Models\MemberModel[]
     */
    public function getRankItemModelArrayByTypeId($rankTypeId)
    {
        $query = 'SELECT * FROM rank_items WHERE rank_type_id = ?';
        $stmt = $this->db->prepare($query);
        $stmt->bind('i', (int)$rankTypeId);
        $stmt->execute();
        $res = $stmt->get_result();

        $modelArr = [];

        while ( $res != false && ($entity = $res->fetch_object()) != false )
        {
            $model = new \jp\Models\RankItemModel($this->container);
            $model->setId($entity->id);
            $model->setMemberId($entity->member_id);
            $model->setTypeId($entity->rank_type_id);
            $model->setValue($entity->value);
            $modelArr[] = $model;
        }

        return $modelArr;
    }

    public function getRankTypeModelById($rankTypeId)
    {
        $query = 'SELECT * FROM rank_type WHERE id = ?';
        $stmt = $this->db->prepare($query);
        $stmt->bind('i', (int)$rankTypeId);
        $stmt->execute();

        if ( ($res = $stmt->get_result()) != false )
        {
            $entity = $res->fetch_object();

            $model = new \jp\Models\RankTypeModel($this->container);
            $model->setId($entity->id);
            $model->setName($entity->name);

            return $model;
        }

        return false;
    }
}
