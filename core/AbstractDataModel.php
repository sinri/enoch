<?php
/**
 * Created by PhpStorm.
 * User: Sinri
 * Date: 2017/8/8
 * Time: 17:46
 */

namespace sinri\enoch\core;


use sinri\enoch\helper\CommonHelper;

/**
 * @since 2.1.7
 * Class AbstractDataModel
 * @package sinri\enoch\core
 */
abstract class AbstractDataModel
{
    protected $scheme;
    protected $table;

    public function __construct()
    {
        $this->scheme = $this->mappingSchemeName();
        $this->table = $this->mappingTableName();
    }

    /**
     * @return null|string
     */
    protected function mappingSchemeName()
    {
        return null;
    }

    /**
     * @return string
     */
    abstract protected function mappingTableName();

    /**
     * @return string
     */
    protected function getTableExpressForSQL()
    {
        $e = ($this->scheme === null ? "" : '`' . $this->scheme . "`.");
        $e .= "`{$this->table}`";
        return $e;
    }

    /**
     * @return LibPDO
     */
    abstract public function db();

    /**
     * @return false|string
     */
    public static function now()
    {
        return date('Y-m-d H:i:s');
    }

    /**
     * @param $conditions
     * @param string $glue
     * @return string
     */
    protected final function buildCondition($conditions, $glue = 'AND')
    {
        $condition_sql = "";
        if (is_string($conditions)) {
            $condition_sql = $conditions;
        } elseif (is_array($conditions)) {
            $c = [];
            foreach ($conditions as $key => $value) {
                if (is_array($value)) {
                    // here @since 2.1.14
                    $x = [];
                    foreach ($value as $value_piece) {
                        $x[] = $this->db()->quote($value_piece);
                    }
                    $x = implode(",", $x);
                    $c[] = " `{$key}` in (" . $x . ") ";
                } else {
                    $c[] = " `{$key}`=" . $this->db()->quote($value) . " ";
                }
            }
            $condition_sql = implode($glue, $c);
        }
        return trim($condition_sql);
    }

    /**
     * @param array|string $conditions
     * @return array|bool
     */
    public function selectRow($conditions)
    {
        $condition_sql = $this->buildCondition($conditions, 'AND');
        if ($condition_sql === '') {
            $condition_sql = "1";
        }

        $table = $this->getTableExpressForSQL();
        $sql = "SELECT * FROM {$table} WHERE {$condition_sql} LIMIT 1";
        return $this->db()->getRow($sql);
    }


    /**
     * @param array|string $conditions
     * @param int $limit
     * @param int $offset
     * @return array|bool
     */
    public function selectRows($conditions, $limit = 0, $offset = 0)
    {
        $condition_sql = $this->buildCondition($conditions);
        if ($condition_sql === '') {
            $condition_sql = "1";
        }
        $table = $this->getTableExpressForSQL();
        $sql = "SELECT * FROM {$table} WHERE {$condition_sql} ";
        $limit = intval($limit, 10);
        $offset = intval($offset, 10);
        if ($limit > 0) {
            $sql .= " limit {$limit} ";
            if ($offset > 0) {
                $sql .= " offset {$offset} ";
            }
        }
        return $this->db()->getAll($sql);
    }

    /**
     * @param array|string $conditions
     * @return int
     */
    public function selectRowsForCount($conditions)
    {
        $condition_sql = $this->buildCondition($conditions);
        if ($condition_sql === '') {
            $condition_sql = "1";
        }
        $table = $this->getTableExpressForSQL();
        $sql = "SELECT count(*) FROM {$table} WHERE {$condition_sql} ";

        $count = $this->db()->getOne($sql);
        return intval($count, 10);
    }

    /**
     * @param $conditions
     * @param null|string $sort "field","field desc"," f1 asc, f2 desc"
     * @param int $limit
     * @param int $offset
     * @param null|string $refKey normally PK or UK if you want to get map rather than list
     * @return array
     */
    public function selectRowsWithSort($conditions, $sort = null, $limit = 0, $offset = 0, $refKey = null)
    {
        $condition_sql = $this->buildCondition($conditions);
        if ($condition_sql === '') {
            $condition_sql = "1";
        }
        $table = $this->getTableExpressForSQL();
        $sql = "SELECT * FROM {$table} WHERE {$condition_sql} ";

        if ($sort) {
            $sql .= "order by " . $sort;
        }

        $limit = intval($limit, 10);
        $offset = intval($offset, 10);
        if ($limit > 0) {
            $sql .= " limit {$limit} ";
            if ($offset > 0) {
                $sql .= " offset {$offset} ";
            }
        }
        $all = $this->db()->getAll($sql);
        if ($refKey) {
            $all = CommonHelper::turnListToMapping($all, $refKey);
        }
        return $all;
    }

    /**
     * @param array $data
     * @param null $pk
     * @return bool|string
     */
    public function insert($data, $pk = null)
    {
        $fields = [];
        $values = [];
        foreach ($data as $key => $value) {
            $fields[] = "`{$key}`";
            $values[] = $this->db()->quote($value);
        }
        $fields = implode(",", $fields);
        $values = implode(",", $values);
        $table = $this->getTableExpressForSQL();
        $sql = "INSERT INTO {$table} ({$fields}) VALUES ({$values})";
        return $this->db()->insert($sql, $pk);
    }

    /**
     * @param array $data
     * @return bool|string
     */
    public function replace($data)
    {
        $fields = [];
        $values = [];
        foreach ($data as $key => $value) {
            $fields[] = "`{$key}`";
            $values[] = $this->db()->quote($value);
        }
        $fields = implode(",", $fields);
        $values = implode(",", $values);
        $table = $this->getTableExpressForSQL();
        $sql = "replace INTO {$table} ({$fields}) VALUES ({$values})";
        return $this->db()->insert($sql);
    }

    /**
     * @param $conditions
     * @param $data
     * @return int
     */
    public function update($conditions, $data)
    {
        $condition_sql = $this->buildCondition($conditions, "AND");
        if ($condition_sql === '') {
            $condition_sql = "1";
        }
        $data_sql = $this->buildCondition($data, ",");
        $table = $this->getTableExpressForSQL();
        $sql = "UPDATE {$table} SET {$data_sql} WHERE {$condition_sql}";
        return $this->db()->exec($sql);
    }

    /**
     * @param $conditions
     * @return int
     */
    public function delete($conditions)
    {
        $condition_sql = $this->buildCondition($conditions, "AND");
        if ($condition_sql === '') {
            $condition_sql = "1";
        }
        $table = $this->getTableExpressForSQL();
        $sql = "DELETE FROM {$table} WHERE {$condition_sql}";
        return $this->db()->exec($sql);
    }
}