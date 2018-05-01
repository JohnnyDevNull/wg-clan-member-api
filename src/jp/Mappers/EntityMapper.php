<?php

namespace jp\Mappers;

use jp\Misc\BaseMapper;
use jp\Misc\Database;
use jp\Models\Database\EntityModel;
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
     *
     * @return array
     * @throws \Exception
     */
    public function getClanModelById($clanId)
    {
        $sql = 'SELECT * '
               . 'FROM clans '
               . 'WHERE clan_id = ?';

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('i', $clanId);
        $this->db->execute($stmt);

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
     *
     * @return string
     * @throws \Exception
     */
    public function getMembersByClanId($clanId)
    {
        $sql = 'SELECT * '
             . 'FROM members '
             . 'WHERE clan_id = ?';

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('i', $clanId);
        $this->db->execute($stmt);
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
     *
     * @return string
     * @throws \Exception
     */
    public function getMemberModelById($clanId, $memberId)
    {
        $sql = 'SELECT * '
             . 'FROM members '
             . 'WHERE id = ? '
             . 'AND clan_id = ?';

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('ii', $memberId, $clanId);
        $this->db->execute($stmt);

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
        $sql = 'SELECT * '
             . 'FROM rank_type';

        $stmt = $this->db->prepare($sql);
        $this->db->execute($stmt);
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
     *
     * @return string
     * @throws \Exception
     */
    public function getRankItemsByClanId($clanId)
    {
        $queryTypes = 'SELECT * '
                    . 'FROM rank_type';

        $stmtTypes = $this->db->prepare($queryTypes);
        $this->db->execute($stmtTypes);
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
            $this->db->execute($stmtItems);
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
        $this->db->execute($stmt);
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
     * @param string $data
     * @param bool   $forceUpdate [optional] default: false
     *
     * @throws \Exception
     */
    public function saveResult($id, $context, $data, $forceUpdate = false)
    {
        $doUpdate = false;

        $sql = 'SELECT result_time '
             . 'FROM results '
             . 'WHERE id = ? '
             . 'AND type = ? '
             . 'ORDER BY result_time DESC '
             . 'LIMIT 1';

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('is', $id, $context);
        $this->db->execute($stmt);
        $res = $stmt->get_result();

        if($res !== false && ($row = $res->fetch_object()) !== null)
        {
            $date = date('d.m.Y 00:00:00');
            $startOfDay = strtotime($date);
            $lastResult = (int)$row->result_time;

            if ($lastResult > $startOfDay || $forceUpdate)
            {
                $doUpdate = true;
            }
        }

        $timestamp = time();

        if ($doUpdate)
        {
            $sql = 'UPDATE results '
                 . 'SET result_time = ?, data = ? '
                 . 'WHERE id = ? '
                 . 'AND type = ? ';

            $stmt = $this->db->prepare($sql);
            $stmt->bind_param('isis', $timestamp, $data, $id, $context);
            $this->db->execute($stmt);
        }
        else
        {
            $sql = 'INSERT INTO results (id, type, result_time, data ) '
                 . 'VALUES (?,?,?,? )';

            $stmt = $this->db->prepare($sql);
            $stmt->bind_param('isis', $id, $context, $timestamp, $data);
            $this->db->execute($stmt);
        }
    }

    /**
     * @param int    $clanId
     * @param string $data should be a json string
     *
     * @throws \Exception
     */
    public function saveMembers($clanId, $data)
    {
        $jsonData = json_decode($data);
        $clanInfo = $jsonData->data->{$clanId};

        $sql = 'UPDATE members '
             . 'SET is_member = 0 '
             . 'WHERE clan_id = ?';
        $isMemberStmt = $this->db->prepare($sql);

        $sql = 'INSERT INTO members (id, clan_id, name, role, date_joined, is_member) '
             . 'VALUES(?,?,?,?,?,?)';
        $insertMemberStmt = $this->db->prepare($sql);

        $sql = 'UPDATE members '
             . 'SET is_member = ? '
             . '  , name = ? '
             . '  , role = ? '
             . '  , date_joined = ? '
             . 'WHERE clan_id = ? '
             . 'AND id = ?';
        $updateMemberStmt = $this->db->prepare($sql);


        $sql = 'SELECT COUNT(1) AS count '
             . 'FROM members '
             . 'WHERE EXISTS ( SELECT 1 '
                            . 'FROM members '
                            . 'WHERE clan_id = ? '
                            . 'AND id = ? )';
        $memberExistsStmt = $this->db->prepare($sql);

        try
        {
            $this->db->autocommit(false);
            $this->db->begin(0, 'insert_members');

            $isMemberStmt->bind_param('i', $clanId);
            $this->db->execute($isMemberStmt);

            foreach ($clanInfo->members as $member)
            {
                $memberId = (int)$member->account_id;
                $memberExistsStmt->bind_param('ii',$clanId, $memberId);
                $this->db->execute($memberExistsStmt);
                $res = $memberExistsStmt->get_result()->fetch_object();

                $isMember = 1;
                $name = (string)$member->account_name;
                $role = (string)$member->role;
                $dateJoined = (int)$member->joined_at;

                if((int)$res->count > 0)
                {
                    $updateMemberStmt->bind_param('issiii', $isMember, $name, $role, $dateJoined, $clanId, $memberId);
                    $this->db->execute($updateMemberStmt);
                }
                else
                {
                    $insertMemberStmt->bind_param('iissii', $memberId, $clanId, $name, $role, $dateJoined, $isMember);
                    $this->db->execute($insertMemberStmt);
                }
            }

            $this->db->commit();
        }
        catch(\Exception $e)
        {
            $this->db->rollback();
            throw $e;
        }
        finally
        {
            $this->db->autocommit(true);
        }
    }
}
