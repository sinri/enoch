<?php
/**
 * Created by PhpStorm.
 * User: Sinri
 * Date: 2017/3/27
 * Time: 13:59
 */

namespace sinri\enoch\core;

class LibMySQL
{
    private $pdo=null;

    public function __construct($params = null)
    {
        if (empty($params)) {
            return;
        }

        $host=$params['host'];
        $port=$params['port'];
        $username=$params['username'];
        $password=$params['password'];
        $database=$params['database'];

        $this->pdo = new \PDO(
            'mysql:host='.$host.';port='.$port.';dbname='.$database.';charset=utf8',
            $username,
            $password,
            array(\PDO::ATTR_EMULATE_PREPARES => false)
        );
        $this->pdo->query("set names utf8");
    }

    public function setConnection($host, $port, $username, $password, $database)
    {
        $this->pdo = new \PDO(
            'mysql:host=' . $host . ';port=' . $port . ';dbname=' . $database . ';charset=utf8',
            $username,
            $password,
            array(\PDO::ATTR_EMULATE_PREPARES => false)
        );
        $this->pdo->query("set names utf8");
    }

    public function getAll($sql)
    {
        $stmt=$this->pdo->query($sql);
        $this->logSql($sql, $stmt);
        $rows=$stmt->fetchAll(\PDO::FETCH_ASSOC);
        return $rows;
    }

    public function getCol($sql)
    {
        $stmt=$this->pdo->query($sql);
        $this->logSql($sql, $stmt);
        $rows=$stmt->fetchAll(\PDO::FETCH_BOTH);
        $col=array();
        if ($rows) {
            foreach ($rows as $row) {
                $col[]=$row[0];
            }
        }
        return $col;
    }

    public function getRow($sql)
    {
        $stmt=$this->pdo->query($sql);
        $this->logSql($sql, $stmt);
        $rows=$stmt->fetchAll(\PDO::FETCH_ASSOC);
        if ($rows) {
            return $rows[0];
        } else {
            return false;
        }
    }

    public function getOne($sql)
    {
        //FETCH_BOTH
        $stmt=$this->pdo->query($sql);
        $this->logSql($sql, $stmt);
        // $rows=$stmt->fetchAll(\PDO::FETCH_BOTH);//var_dump($rows);
        $rows=$stmt->fetchAll(\PDO::FETCH_ASSOC);//var_dump($rows);
        if ($rows) {
            $row = $rows[0];
            if ($row) {
                $row=array_values($row);
                return $row[0];
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function exec($sql)
    {
        $this->logSql($sql, true);
        $rows=$this->pdo->exec($sql);
        return $rows;
    }

    public function insert($sql)
    {
        $this->logSql($sql, true);
        $rows=$this->pdo->exec($sql);
        if ($rows) {
            return $this->pdo->lastInsertId();
        } else {
            return false;
        }
    }

    public function beginTransaction()
    {
        return $this->pdo->beginTransaction();
    }
    public function commit()
    {
        return $this->pdo->commit();
    }
    public function rollBack()
    {
        return $this->pdo->rollBack();
    }
    public function inTransaction()
    {
        return $this->pdo->inTransaction();
    }

    public function errorCode()
    {
        return $this->pdo->errorCode();
    }
    public function errorInfo()
    {
        return $this->pdo->errorInfo();
    }

    public function quote($string, $parameterType = \PDO::PARAM_STR)
    {
        return $this->pdo->quote($string, $parameterType);
    }

    private function logSql($sql, $stmt)
    {
        if (!$stmt) {
            throw new \Exception("Failed to prepare SQL: ".$sql, 1);
        }
    }

    public function safeQueryAll($sql, $values = array())
    {
        $sth = $this->pdo->prepare($sql);
        $sth->execute($values);
        $rows=$sth->fetchAll();
        return $rows;
    }
    public function safeQueryRow($sql, $values = array())
    {
        $sth = $this->pdo->prepare($sql);
        $sth->execute($values);
        $row=$sth->fetch(\PDO::FETCH_ASSOC);
        return $row;
    }
    public function safeQueryOne($sql, $values = array())
    {
        $sth = $this->pdo->prepare($sql);
        $sth->execute($values);
        $col=$sth->fetchColumn(0);
        return $col;
    }

    public function safeInsertOne($sql, $values = array(), &$insertedId = 0, $pk = null)
    {
        $sth = $this->pdo->prepare($sql);
        $done = $sth->execute($values);
        $insertedId = $this->pdo->lastInsertId($pk);
        return $done;
    }

    public function safeExecute($sql, $values = array())
    {
        $sth = $this->pdo->prepare($sql);
        $done = $sth->execute($values);
        return $done;
    }
}
