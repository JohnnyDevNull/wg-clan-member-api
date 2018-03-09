<?php

namespace jp\Misc;

final class Database
{
    /**
     * @var \jp\Misc\Database
     */
    private static $instance;

    /**
     * @var string
     */
    private $host;

    /**
     * @var string
     */
    private $user;

    /**
     * @var string
     */
    private $pass;

    /**
     * @var string
     */
    private $db;

    /**
     * @var int
     */
    private $port;

    /**
     * @var string
     */
    private $sql;

    /**
     * @var \mysqli
     */
    private $mysqli;

    private function __constructor($host, $user, $pass, $db, $port)
    {
        $this->host = (string)$host;
        $this->user = (string)$user;
        $this->pass = (string)$pass;
        $this->db   = (string)$db;
        $this->port = (int)$port;
    }

    public function __destructor()
    {
        $this->disconnect();
    }

    /**
     * @param string $host
     * @param string $user
     * @param string $pass
     * @param string $db
     * @param string $port
     * @return \jp\Misc\Database
     */
    public static function getInstance($host, $user, $pass, $db, $port)
    {
        if (!empty(self::$instance))
        {
            return $instance;
        }

        self::$instance = new Database($host, $user, $pass, $db, $port);
    }

    private function connect()
    {
        if(empty($this->mysqli))
        {
            $this->mysqli = new \mysqli();
            $this->mysqli->connect($this->host, $this->user, $this->pass, $this->db, $this->port);
        }
    }

    private function disconnect()
    {
        if(!empty($this->mysqli))
        {
            $this->mysqli->close();
            $this->mysqli = null;
        }
    }

    /**
     * @param string $sql
     * @return \jp\DB
     */
    public function setSQL(string $sql): \jp\DB
    {
        $this->sql = $sql;
        return $this;
    }

    /**
     * @param string $query
     * @return \mysqli_stmt
     * @throws \Exception
     */
    public function prepare($query)
    {
        $this->connect();

        $this->setSQL($query);

        if (!($stmt = $this->mysqli->prepare($this->sql)))
        {
            throw new Exception("Prepare failed: (" . $this->mysqli->prepare->errno . ") " . $this->mysqli->error);
        }

        return $stmt;
    }

    /**
     * @param string $sql
     * @param bool $fetch [optional] default: false
     * @return false|\mysqli_result|string[]
     * @throws DB\Exception
     */
    public function query(string $sql, bool $fetch = false)
    {
        if ($this->mysqli === false)
        {
            $this->connect();
        }

        $this->setSQL($sql);

        $result = $this->mysqli->query($this->sql);

        if ($this->mysqli->errno)
        {
            throw new Exception('MySQLi Query Error: '.$this->mysqli->error.'; Query: '.$this->getLastQuery());
        }

        if ($fetch)
        {
            $ret = array();

            while ($result && ($row = $result->fetch_assoc()))
            {
                $ret[] = $row;
            }
        }
        else
        {
            $ret = $result;
        }

        return $ret;
    }

    /**
     * @param string $sql
     * @return mixed
     */
    public function querySingle(string $sql)
    {
        $ret = $this->query($sql, true);

        if(is_array($ret))
        {
            $ret = reset($ret);
        }

        return $ret;
    }

    /**
     * @param array $array
     * @return string
     */
    public function getWhereSqlFromArray(array $array): string
    {
        $where = array();

        foreach ($array as $key => $value)
        {
            if (is_array($value))
            {
                foreach ($value as $key => $value)
                {
                    $where[] = '`'.$key.'` = "'.$this->escape($value).'"';
                }
            }
            else
            {
                $where[] = '`'.$key.'` = "'.$this->escape($value).'"';
            }
        }

        return implode(' AND ', $where);
    }

    /**
     * @param string $string
     * @param bool $escape [optional] default: false
     * @return string
     */
    public function quote(string $string, bool $escape = false): string
    {
        if (empty($this->mysqli))
        {
            $this->connect();
        }

        if ($escape)
        {
            $string = $this->escape($string);
        }

        return '\''.$string.'\'';
    }

    /**
     * @param string $string
     * @return string
     */
    public function escape(string $string): string
    {
        if (empty($this->mysqli))
        {
            $this->connect();
        }

        return $this->mysqli->real_escape_string($string);
    }

    /**
     * @param string $col
     * @return string
     */
    public function escapeCol(string $col): string
    {
        return '`'.trim(str_replace('`', '', trim($col)), '´').'`';
    }

    /**
     * @param string $table
     * @return string
     */
    public function escapeTable(string $table): string
    {
        return '`'.trim(str_replace('`', '', trim($table)), '´').'`';
    }

    /**
     * @return string
     */
    public function getLastQuery(): string
    {
        return $this->sql;
    }

    /**
     * @return int
     */
    public function getAffectedRows(): int
    {
        return $this->mysqli->affected_rows;
    }

    /**
     * @return int
     */
    public function getErrorNo(): int
    {
        return $this->mysqli->errno;
    }

    /**
     * @return string
     */
    public function getError(): string
    {
        return $this->mysqli->error;
    }

    /**
     * @return int
     */
    public function getInsertID(): int
    {
        return $this->mysqli->insert_id;
    }
}
