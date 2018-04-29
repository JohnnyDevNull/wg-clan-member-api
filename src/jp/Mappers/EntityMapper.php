<?php

namespace jp\Mappers;

use jp\Misc\BaseMapper;
use jp\Misc\Database;
use Interop\Container\ContainerInterface as Container;

class EntityMapper extends BaseMapper
{
    /**
     * @var \jp\Misc\Database 
     */
    private $db;

    /**
     * @param \Interop\Container\ContainerInterface $container
     * @param \jp\Misc\Database
     */
    public function __construct(Container $container, Database $db = null)
    {
        parent::__construct($container);

        if ($db !== null)
        {
            $this->db = $db;
        }
        else
        {
            $dbConf = $container->get('settings')['db'];
            $this->db = Database::getInstance(
                $dbConf['host'],
                $dbConf['user'],
                $dbConf['pass'],
                $dbConf['dbname'],
                $dbConf['port']
            );
        }
    }

    /**
     * @param int $clanId
     * @return array
     * @throws \Exception
     */
    public function getClanModelById($clanId)
    {
        $query = 'SELECT * '
               . 'FROM clans '
               . 'WHERE clan_id = ?';

        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $clanId);
        $stmt->execute();

        if ( ($res = $stmt->get_result()) != false )
        {
            $entity = $res->fetch_object();
            $clanModel = new EntityModel($this->container);
            $clanModel->setData($entity);

            return (array)$clanModel->getData();
        }

        return [];
    }

    /**
     * @param int $clanId
     * @return string
     * @throws \Exception
     */
    public function getMembersByClanId($clanId)
    {
        $query = 'SELECT * '
               . 'FROM members '
               . 'WHERE clan_id = ?';

        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $clanId);
        $stmt->execute();
        $res = $stmt->get_result();

        $modelArr['items'] = [];

        while ( $res != false && ($entity = $res->fetch_object()) != false )
        {
            $memberModel = new EntityModel($this->container);
            $memberModel->setData($entity);

            $modelArr['items'][] = (array)$memberModel->getData();
        }

        return $this->getJsonFromArray($modelArr);
    }

    /**
     * @param int $clanId
     * @param int $memberId
     * @return string
     * @throws \Exception
     */
    public function getMemberModelById($clanId, $memberId)
    {
        $query = 'SELECT * '
               . 'FROM members '
               . 'WHERE id = ? '
               . 'AND clan_id = ?';

        $stmt = $this->db->prepare($query);
        $stmt->bind_param('ii', $memberId, $clanId);
        $stmt->execute();

        $memberData['items'] = [];

        if ( ($res = $stmt->get_result()) != false )
        {
            $entity = $res->fetch_object();

            $typeModel = new EntityModel($this->container);
            $typeModel->setData($entity);

            $memberData['items'][] = (array)$typeModel->getData();
        }

        return $this->getJsonFromArray($memberData);
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getRankTypes()
    {
        $query = 'SELECT * '
               . 'FROM rank_type';

        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $res = $stmt->get_result();

        $modelArr['items'] = [];

        while ( $res != false && ($entity = $res->fetch_object()) != false )
        {
            $typeModel = new EntityModel($this->container);
            $typeModel->setData($entity);

            $modelArr['items'][] = (array)$typeModel->getData();
        }

        return $modelArr;
    }

    /**
     * @param int $clanId
     * @return string
     * @throws \Exception
     */
    public function getRankItemsByClanId($clanId)
    {
        $queryTypes = 'SELECT * '
                    . 'FROM rank_type';

        $stmtTypes = $this->db->prepare($queryTypes);
        $stmtTypes->execute();
        $resTypes = $stmtTypes->get_result();

        $typeItemsArray['items'] = [];

        while ( $resTypes != false && ($typeEntity = $resTypes->fetch_object()) != false )
        {
            $typeModel = new EntityModel($this->container);
            $typeModel->setData($typeEntity);

            $typeId = (int)$typeEntity->id;
            $typeName = (string)$typeEntity->name;

            $typeData = [
                'id' => $typeId,
                'name' => $typeName,
                'items' => []
            ];

            $queryItems = 'SELECT * '
                        . 'FROM rank_items '
                        . 'WHERE rank_type_id = ? '
                        . 'AND clan_id = ?';

            $stmtItems = $this->db->prepare($queryItems);
            $stmtItems->bind_param('ii', $typeId, $clanId);
            $stmtItems->execute();
            $resItems = $stmtItems->get_result();

            $modelArr = [];

            while ( $resItems != false && ($rankItemEntity = $resItems->fetch_object()) != false )
            {
                $itemModel = new EntityModel($this->container);
                $itemModel->setData($rankItemEntity);
                $modelArr[] = (array)$itemModel->getData();
            }

            $typeData['items'] = $modelArr;
            $typeItemsArray['items'][] = $typeData;
        }

        return $this->getJsonFromArray($typeItemsArray);
    }

    /**
     * @param  int      $id
     * @param  string   $context
     * @param \DateTime $dateTime
     *
     * @return array|false
     * @throws \Exception
     */
    public function getLastResult($id, $context, \DateTime $dateTime)
    {
        $sql = 'SELECT * '
             . 'FROM results '
             . 'WHERE id = ? '
             . 'AND type = ? '
             . 'AND result_time > ? '
             . 'ORDER BY result_time desc '
             . 'LIMIT 1';

        $offset = $this->container->get('settings')['wg']['request_offset'];
        $tstamp = $dateTime->getTimestamp() - $offset;

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('isi', $id, $context, $tstamp);
        $stmt->execute();
        $result = $stmt->get_result();

        if($result === false || $result->num_rows === 0)
        {
            return false;
        }

        return $result->fetch_assoc();
    }

    /**
     * @param int    $id
     * @param string $context
     * @param array  $data
     *
     * @throws \Exception
     */
    public function saveResult($id, $context, $data)
    {
        // @todo result halbstündig updaten, sodass nur 1 Eintrag pro Spieler pro Tag entsteht. Datenbank schonen...
        // @todo für clans nur 1 Result speichern, dieses halbstündig updaten

        $sql = 'INSERT INTO results (id, type, result_time, data) '
             . 'VALUES (?,?,?,?) ';
        $dateTime = new \DateTime();
        $tstamp = $dateTime->getTimestamp();

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('isis', $id, $context, $tstamp, $data);
        $stmt->execute();
    }
}
