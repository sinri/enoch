<?php
/**
 * Created by PhpStorm.
 * User: Sinri
 * Date: 2017/10/1
 * Time: 19:27
 */

namespace sinri\enoch\core;


use SQLite3;

class LibSqlite3 extends SQLite3
{
    /**
     * LibSqlite3 constructor.
     * @param $filename
     * @param null $flags
     * @param null $encryption_key
     */
    public function __construct($filename, $flags = null, $encryption_key = null)
    {
        // SQLITE3_OPEN_READONLY: Open the database for reading only.
        // SQLITE3_OPEN_READWRITE: Open the database for reading and writing.
        // SQLITE3_OPEN_CREATE: Create the database if it does not exist.
        if ($flags === null) {
            $flags = SQLITE3_OPEN_READWRITE | SQLITE3_OPEN_CREATE;
        }
        $this->open($filename, $flags, $encryption_key);
    }

    /**
     * Run `exec` and append the affected row count
     * @param $query
     * @param null $affectedRowCount
     * @return bool
     */
    public function execWithAffectedRowCount($query, &$affectedRowCount = null)
    {
        $done = $this->exec($query);
        if ($done) {
            $affectedRowCount = $this->changes();
        }
        return $done;
    }

    /**
     * Run `exec` and append the last inserted row id
     * @param $query
     * @param null $lastInsertedId
     * @return bool
     */
    public function insertWithLastInsertId($query, &$lastInsertedId = null)
    {
        $done = $this->exec($query);
        if ($done) {
            $lastInsertedId = $this->lastInsertRowID();
        }
        return $done;
    }

    /**
     * @param $query
     * @param int $mode
     * @return array
     */
    public function getAll($query, $mode = SQLITE3_ASSOC)
    {
        $result = $this->query($query);
        $rows = [];
        while ($array = $result->fetchArray($mode)) {
            $rows[] = $array;
        }
        $result->finalize();
        return $rows;
    }

    /**
     * @param $query
     * @return array
     */
    public function getCol($query)
    {
        $result = $this->query($query);
        $cols = [];
        while ($array = $result->fetchArray(SQLITE3_NUM)) {
            $cols[] = $array[0];
        }
        $result->finalize();
        return $cols;
    }

    /**
     * @param $query
     * @return array
     */
    public function getRow($query)
    {
        $result = $this->query($query);
        $array = $result->fetchArray(SQLITE3_ASSOC);
        $result->finalize();
        return $array;
    }

    /**
     * @param $query
     * @return mixed
     */
    public function getOne($query)
    {
        $result = $this->query($query);
        $array = $result->fetchArray(SQLITE3_NUM);
        $result->finalize();
        return $array[0];
    }
}