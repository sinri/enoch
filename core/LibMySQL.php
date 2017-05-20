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

    /**
     * LibMySQL constructor.
     * @param null|array $params
     */
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

    /**
     * @param $host
     * @param $port
     * @param $username
     * @param $password
     * @param $database
     */
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

    /**
     * @param $sql
     * @return array
     */
    public function getAll($sql)
    {
        $stmt=$this->pdo->query($sql);
        $this->logSql($sql, $stmt);
        $rows=$stmt->fetchAll(\PDO::FETCH_ASSOC);
        return $rows;
    }

    /**
     * @param $sql
     * @return array
     */
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

    /**
     * @param $sql
     * @return array|bool
     */
    public function getRow($sql)
    {
        $stmt = $this->pdo->query($sql);
        $this->logSql($sql, $stmt);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        if ($rows) {
            return $rows[0];
        }
        return false;
    }

    /**
     * @param $sql
     * @return mixed|bool
     */
    public function getOne($sql)
    {
        //FETCH_BOTH
        $stmt = $this->pdo->query($sql);
        $this->logSql($sql, $stmt);
        // $rows=$stmt->fetchAll(\PDO::FETCH_BOTH);//var_dump($rows);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);//var_dump($rows);
        if ($rows) {
            $row = $rows[0];
            if ($row) {
                $row = array_values($row);
                return $row[0];
            }
        }
        return false;
    }

    /**
     * @param $sql
     * @return int affected row count
     */
    public function exec($sql)
    {
        $this->logSql($sql, true);
        $rows=$this->pdo->exec($sql);
        return $rows;
    }

    /**
     * @param $sql
     * @param null $pk @since 1.3.6
     * @return bool|string
     */
    public function insert($sql, $pk = null)
    {
        $this->logSql($sql, true);
        $rows = $this->pdo->exec($sql);
        if ($rows) {
            return $this->pdo->lastInsertId($pk);
        }
        return false;
    }

    /**
     * @return bool
     */
    public function beginTransaction()
    {
        return $this->pdo->beginTransaction();
    }

    /**
     * @return bool
     */
    public function commit()
    {
        return $this->pdo->commit();
    }

    /**
     * @return bool
     */
    public function rollBack()
    {
        return $this->pdo->rollBack();
    }

    /**
     * @return bool
     */
    public function inTransaction()
    {
        return $this->pdo->inTransaction();
    }

    /**
     * @return mixed
     */
    public function errorCode()
    {
        return $this->pdo->errorCode();
    }

    /**
     * @return array
     */
    public function errorInfo()
    {
        return $this->pdo->errorInfo();
    }

    /**
     * @param $string
     * @param int $parameterType
     * @return string
     */
    public function quote($string, $parameterType = \PDO::PARAM_STR)
    {
        return $this->pdo->quote($string, $parameterType);
    }

    /**
     * @param $sql
     * @param $stmt
     * @throws \Exception
     */
    private function logSql($sql, $stmt)
    {
        if (!$stmt) {
            throw new \Exception("Failed to prepare SQL: ".$sql, 1);
        }
    }

    /**
     * @param $sql
     * @param array $values
     * @param int $fetchStyle
     * @return array
     */
    public function safeQueryAll($sql, $values = array(), $fetchStyle = \PDO::FETCH_ASSOC)
    {
        $sth = $this->pdo->prepare($sql);
        $sth->execute($values);
        $rows = $sth->fetchAll($fetchStyle);
        return $rows;
    }

    /**
     * @param $sql
     * @param array $values
     * @return mixed
     */
    public function safeQueryRow($sql, $values = array())
    {
        $sth = $this->pdo->prepare($sql);
        $sth->execute($values);
        $row=$sth->fetch(\PDO::FETCH_ASSOC);
        return $row;
    }

    /**
     * @param $sql
     * @param array $values
     * @return string
     */
    public function safeQueryOne($sql, $values = array())
    {
        $sth = $this->pdo->prepare($sql);
        $sth->execute($values);
        $col=$sth->fetchColumn(0);
        return $col;
    }

    /**
     * @param $sql
     * @param array $values
     * @param int $insertedId
     * @param null $pk
     * @return bool
     */
    public function safeInsertOne($sql, $values = array(), &$insertedId = 0, $pk = null)
    {
        $sth = $this->pdo->prepare($sql);
        $done = $sth->execute($values);
        $insertedId = $this->pdo->lastInsertId($pk);
        return $done;
    }

    /**
     * @param $sql
     * @param array $values
     * @param null $sth @since 1.3.3
     * @return bool
     */
    public function safeExecute($sql, $values = array(), &$sth = null)
    {
        $sth = $this->pdo->prepare($sql);
        $done = $sth->execute($values);
        return $done;
    }

    /**
     * @since 1.3.3
     * @param null|string $pk
     * @return string
     */
    public function getLastInsertID($pk = null)
    {
        return $this->pdo->lastInsertId($pk);
    }

    /**
     * @since 1.3.3
     * PDOStatement::rowCount() 返回上一个由对应的 PDOStatement 对象执行DELETE、 INSERT、或 UPDATE 语句受影响的行数。
     * 如果上一条由相关 PDOStatement 执行的 SQL 语句是一条 SELECT 语句，有些数据可能返回由此语句返回的行数。
     * 但这种方式不能保证对所有数据有效，且对于可移植的应用不应依赖于此方式。
     * @param \PDOStatement $statement
     * @return int
     */
    public function getAffectedRowCount($statement)
    {
        return $statement->rowCount();
    }
}
