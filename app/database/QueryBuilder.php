<?php
/**
 * Created by PhpStorm.
 * User: vismauser2
 * Date: 19.2.22
 * Time: 08.43
 */

namespace App\database;


class QueryBuilder
{
    private $select = [];
    private $tableName;
    private $joinTableName;
    private $where = [];
    private $ANDWhere = [];
    private $ORWhere = [];
    private $ON = [];
    private $query = [];

    public function from($table)
    {
        $this->tableName = $table;
        $this->addFrom();

        return $this;
    }

    public function select(...$fields)
    {
        $this->select = $fields;
        $this->addSelect();

        return $this;
    }

    public function on($tableField, $statement, $joinTableField)
    {
        $condition = $this->tableName . '.' . $tableField . ' ' . $statement . ' 
        ' . $this->joinTableName . '.' . $joinTableField;

        $this->ON[] = $condition;
        $this->addOn();

        return $this;
    }

    public function ignore()
    {
        $this->query[] = 'IGNORE';
        return $this;
    }

    public function distinct()
    {
        $this->query[] = 'DISTINCT';
        return $this;
    }

    public function where($field, $statement, $compareTo)
    {
        $condition = $field . ' ' . $statement . ' :' . $compareTo;
        $this->where[] = $condition;
        $this->addWhere();

        return $this;
    }

    public function innerJoin($joinTableName)
    {
        $this->joinTableName = $joinTableName;
        $this->addInnerJoin();

        return $this;
    }

    public function orWhere($field, $statement, $compareTo)
    {
        $condition = $field . ' ' . $statement . ' :' . $compareTo;
        $this->ORWhere[] = $condition;
        $this->addORWhere();

        return $this;
    }

    public function andWhere($field, $statement, $compareTo)
    {
        $condition = $field . ' ' . $statement . ' :' . $compareTo;
        $this->ANDWhere[] = $condition;
        $this->addAndWhere();

        return $this;
    }

    private function addSelect()
    {
        $this->query[] = 'SELECT';

        if ($this->select) {
            $this->query[] = implode(', ', $this->select);
        } else {
            $this->query[] = '*';
        }
    }

    private function addFrom()
    {
        $this->query[] = 'FROM';
        $this->query[] = $this->tableName;
    }

    private function addWhere()
    {

        $this->query[] = 'WHERE';
        $this->query[] = '' . implode(' ', $this->where) . '';

        $this->where = null;
    }

    private function addAndWhere()
    {
        if ($this->ANDWhere) {
            $this->query[] = 'AND ' . implode(' ', $this->ANDWhere) . '';
        }

        $this->ANDWhere = null;
    }

    private function addORWhere()
    {
        if ($this->ORWhere) {
            $this->query[] = 'OR ' . implode(' ', $this->ORWhere) . '';
        }

        $this->ORWhere = null;
    }

    private function addInnerJoin()
    {
        $this->query[] = 'INNER JOIN';
        $this->query[] = $this->joinTableName;
    }

    private function addOn()
    {
        $this->query[] = 'ON';
        $this->query[] = implode(' ',$this->ON);

        return $this;
    }

    public function getQuery()
    {
        return implode(' ', $this->query);
    }


}
