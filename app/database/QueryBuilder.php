<?php

namespace App\database;

class QueryBuilder
{
    private $query;

    public function from($table)
    {
        $this->query .= ' FROM ' . $table;

        return $this;
    }

    public function select($fields = null)
    {
        if ($fields === null || $fields === '') {
            $fields = '*';
        }

        $this->query .= 'SELECT ' . $fields;

        return $this;
    }

    public function truncate($tableName)
    {
        $this->query .= 'TRUNCATE TABLE ' . $tableName;

        return $this;
    }

    public function insert($tableName, $fields)
    {
        $this->query .= 'INSERT INTO';
        $this->query .= ' ' . $tableName;
        $this->query .= ' (' . $fields . ')';

        return $this;
    }

    public function insertIgnore($tableName, $fields)
    {
        $this->query .= 'INSERT IGNORE INTO';
        $this->query .= ' ' . $tableName;
        $this->query .= ' (' . $fields . ')';

        return $this;
    }

    public function values($values)
    {
        $this->query .= ' VALUES ';
        $this->query .= '(' . $values . ')';

        return $this;
    }

    public function delete()
    {
        $this->query .= 'DELETE';

        return $this;
    }

    public function on($tableField, $statement, $joinTableField)
    {
        $this->query .= ' ON';
        $this->query .= ' ' . $tableField;
        $this->query .= ' ' . $statement;
        $this->query .= ' ' . $joinTableField;

        return $this;
    }

    public function ignore()
    {
        $this->query .= ' IGNORE ';

        return $this;
    }

    public function distinct()
    {
        $this->query .= ' DISTINCT ';

        return $this;
    }

    public function where($comparedColumn, $condition, $comparedTo)
    {
        $this->query .= ' ' . $comparedColumn;
        $this->query .= ' ' . $condition;
        $this->query .= ' ' . $comparedTo;

        return $this;
    }

    public function innerJoin($joinTableName)
    {

        $this->query .= ' JOIN ' . $joinTableName;

        return $this;
    }

    public function orWhere($field, $statement, $compareTo)
    {
        $this->query .= ' OR';
        $this->query .= ' ' . $field;
        $this->query .= ' ' . $statement;
        $this->query .= ' ' . $compareTo;

        return $this;
    }

    public function andWhere($field, $statement, $compareTo)
    {
        $this->query .= ' AND';
        $this->query .= ' ' . $field;
        $this->query .= ' ' . $statement;
        $this->query .= ' ' . $compareTo;

        return $this;
    }

    public function __toString()
    {
        return $this->query;
    }

}
