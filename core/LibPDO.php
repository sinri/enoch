<?php
/**
 * Created by PhpStorm.
 * User: Sinri
 * Date: 2017/7/14
 * Time: 11:36
 */

namespace sinri\enoch\core;


use sinri\enoch\helper\CommonHelper;

class LibPDO
{
    /**
     * @var \PDO
     */
    protected $pdo;

    /**
     * @var LibLog
     */
    protected $logger;

    /**
     * LibMySQL constructor.
     * @param null|array $params
     */
    public function __construct($params = null)
    {
        $this->logger = null;
        // debug
        // $this->logger=new LibLog();

        if (empty($params)) {
            return;
        }

        $host = CommonHelper::safeReadArray($params, 'host');
        $port = CommonHelper::safeReadArray($params, 'port');
        $username = CommonHelper::safeReadArray($params, 'username');
        $password = CommonHelper::safeReadArray($params, 'password');
        $database = CommonHelper::safeReadArray($params, 'database');
        $charset = CommonHelper::safeReadArray($params, "charset", "utf8");

        $engine = CommonHelper::safeReadArray($params, "engine", "mysql");

        $options = CommonHelper::safeReadArray($params, "options", null);

        $this->setConnection($engine, $host, $port, $username, $password, $database, $charset, $options);
    }

    /**
     * @param $engine
     * @param $host
     * @param $port
     * @param $username
     * @param $password
     * @param $database
     * @param string $charset
     * @param null $options @since 2.1.19
     * @return bool
     */
    public function setConnection($engine, $host, $port, $username, $password, $database, $charset = 'utf8', $options = null)
    {
        $engine = strtolower($engine);
        if ($engine === 'mysql') {
            if ($options === null) {
                $options = [
                    \PDO::ATTR_EMULATE_PREPARES => false
                ];
            }
            $this->pdo = new \PDO(
                'mysql:host=' . $host . ';port=' . $port . ';dbname=' . $database . ';charset=' . $charset,
                $username,
                $password,
                $options
            );
            if (!empty($charset)) {
                $this->pdo->query("set names " . $charset);
            }
            return true;
        } else {
            //throw new BaseCodedException("Engine [{$engine}] is not supported yet.", BaseCodedException::NOT_IMPLEMENT_ERROR);
            return false;
        }
    }

    /**
     * @since 1.4.6
     * @return \PDO
     */
    public function getPdo()
    {
        return $this->pdo;
    }

    /**
     * @param null|LibLog $logger
     */
    public function setLogger($logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param $message
     * @param string $object
     */
    protected function debug($message, $object = '')
    {
        if (!$this->logger) return;
        $this->logger->log(LibLog::LOG_DEBUG, $message, $object);
    }

    /**
     * @param $sql
     * @return array
     */
    public function getAll($sql)
    {
        $stmt = $this->pdo->query($sql);
        $this->logSql($sql, $stmt);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return $rows;
    }

    /**
     * @param $sql
     * @param $stmt
     */
    private function logSql($sql, $stmt)
    {
        if (!$stmt) {
            if ($this->logger) {
                $this->logger->log(LibLog::LOG_ERROR, "Failed to prepare SQL", $sql);
            }
            //throw new \Exception("Failed to prepare SQL: " . $sql);
        }
    }

    /**
     * @param $sql
     * @return array
     */
    public function getCol($sql)
    {
        $stmt = $this->pdo->query($sql);
        $this->logSql($sql, $stmt);
        $rows = $stmt->fetchAll(\PDO::FETCH_BOTH);
        $col = array();
        if ($rows) {
            foreach ($rows as $row) {
                $col[] = $row[0];
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
        $rows = $this->pdo->exec($sql);
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
     * @param int $parameterType \PDO::PARAM_STR or \PDO::PARAM_INT
     * @return string
     */
    public function quote($string, $parameterType = \PDO::PARAM_STR)
    {
        if (!$this->pdo) {
            if ($parameterType == \PDO::PARAM_INT) {
                return intval($string);
            }
            return self::dryQuote($string);
        }
        return $this->pdo->quote($string, $parameterType);
    }

    /**
     * @since 2.1.11
     * @param $inp
     * @return array|mixed
     */
    public static function dryQuote($inp)
    {
        if (is_array($inp))
            return array_map([__CLASS__, __METHOD__], $inp);

        if (!empty($inp) && is_string($inp)) {
            $x = str_replace(array('\\', "\0", "\n", "\r", "'", '"', "\x1a"), array('\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'), $inp);
            return "'{$x}'";
        }

        return $inp;
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
        $this->logSql($sql, $sth);
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
        $this->logSql($sql, $sth);
        $sth->execute($values);
        $row = $sth->fetch(\PDO::FETCH_ASSOC);
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
        $this->logSql($sql, $sth);
        $sth->execute($values);
        $col = $sth->fetchColumn(0);
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
        $this->logSql($sql, $sth);
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
        $this->logSql($sql, $sth);
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

    /**
     * 比PDO更加丧心病狂的SQL模板
     * @since 2.1.11
     *  Sample SQL:
     * select key_field,value,`?`
     * from `?`.`?`
     * where key_field in (?)
     * and status = ?
     * limit [?] , [?]
     *  RULE:
     * (1) `?` => $p
     * (2)  ?  => quote($p)
     * (3) (?) => (quote($p[]),...)
     * (4) [?] => integer_value($p)
     * (5) {?} => float_value($p)
     * @param $template
     * @param array $parameters
     * @return string
     * @throws \Exception
     */
    public function safeBuildSQL($template, $parameters = [])
    {
        $this->debug($template, $parameters);
        $count = preg_match_all('/\?|`\?`|\(\?\)|\[\?\]|\{\?\}/', $template, $matches, PREG_OFFSET_CAPTURE);
        $this->debug("preg_match_all count=" . json_encode($count), $matches);
        if ($count === 0) {
            return $template;
        }
        if (!$count) {
            throw new \Exception("The sql template is not correct.");
        }
        if ($count != count($parameters)) {
            throw new \Exception("The sql template has not correct number of parameters.");
        }

        $parts = [];
        $currentIndex = 0;
        for ($x = 0; $x < $count; $x++) {
            $sought = $matches[0][$x];
            $keyword = $sought[0];
            $index = $sought[1];

            if ($index != $currentIndex) {
                $piece = substr($template, $currentIndex, $index - $currentIndex);
                //$this->debug(__METHOD__.'@'.__LINE__." piece: ".$piece,[$currentIndex,($index - $currentIndex)]);
                $parts[] = $piece;
                $currentIndex = $index;
                //$this->debug(__METHOD__.'@'.__LINE__." current index -> ".$currentIndex);
            }
            $parts[] = $keyword;
            $currentIndex = $currentIndex + strlen($keyword);
            //$this->debug(__METHOD__.'@'.__LINE__." piece: ",$keyword);
            //$this->debug(__METHOD__.'@'.__LINE__." current index -> ",$currentIndex);
        }
        if ($currentIndex < strlen($template)) {
            $piece = substr($template, $currentIndex);
            $parts[] = $piece;
            //$this->debug(__METHOD__ . '@' . __LINE__ . " piece: ", $piece);
        }

        $this->debug("parts", $parts);

        $sql = "";
        $ptr = 0;
        foreach ($parts as $part) {
            switch ($part) {
                // RULE:
                // (1) `?` => $p
                case '`?`': {
                    $sql .= '`' . $parameters[$ptr] . '`';
                    $ptr++;
                }
                    break;
                // (2)  ?  => quote($p)
                case '?': {
                    $sql .= $this->quote($parameters[$ptr]);
                    $ptr++;
                }
                    break;
                // (3) (?) => (quote($p[]),...)
                case '(?)': {
                    if (is_array($parameters[$ptr])) {
                        $group = [];
                        foreach ($parameters[$ptr] as $object) {
                            $group[] = $this->quote($object);
                        }
                        $sql .= '(' . implode(",", $group) . ')';
                    } else {
                        $sql .= '(' . $parameters[$ptr] . ')';
                    }
                    $ptr++;
                }
                    break;
                // (4) [?] => intval($p)
                case '[?]': {
                    $sql .= intval($parameters[$ptr], 10);
                    $ptr++;
                }
                    break;
                // (5) {?} => floatval($p)
                case '{?}': {
                    $sql .= floatval($parameters[$ptr]);
                    $ptr++;
                }
                    break;
                default:
                    $sql .= $part;
            }
        }

        return $sql;
    }
}
