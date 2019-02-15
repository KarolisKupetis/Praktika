<?php

namespace App\Helper;

use PDO;

class DBConnector
{
    private $host;
    private $user;
    private $password;
    private $dbname;
    private $source;
    private $pdoConnection;

    public function __construct()
    {
        $this->host = 'localhost';
        $this->user = 'root';
        $this->password = 'qwer';
        $this->dbname = 'hyphenate';
        $this->source = 'mysql:host=' . $this->host . ';dbname=' . $this->dbname;
        $this->pdoConnection = new \PDO($this->source, $this->user, $this->password);
    }

    public function uploadData($inputArray, $option)
    {
        $sql ='';
        $dataArray=array();

        if($option===1) {
            $truncSql = 'TRUNCATE TABLE patterns';
            $sql = 'INSERT INTO patterns(pattern) VALUES(:pattern)';
            $dataArray=array();

            foreach ($inputArray as $item) {
                $dataArray[]=['pattern'=>$item];
            }
        }
        elseif($option===2) {
            $truncSql = 'TRUNCATE TABLE words';
            $sql = 'INSERT INTO words(word) VALUES(:word)';
            $dataArray=array();

            foreach ($inputArray as $item) {
                $dataArray[]=['word'=>$item];
            }
        }

        if($sql!=='')
        {
            $insertStatement = $this->pdoConnection->prepare($sql);
            $this->pdoConnection->beginTransaction();
            $this->pdoConnection->exec($truncSql);

            foreach ($dataArray as $row) {
                $insertStatement->execute($row);
            }
            $this->pdoConnection->commit();
        }
        else{
            echo "Something's wrong, check files";
        }
    }

    public function getData()
    {
        $rows=array();
        $stmt = $this->pdoConnection->query('SELECT pattern FROM patterns');

        while ($row = $stmt->fetch(PDO::FETCH_COLUMN,0)){
            $rows[] = $row;
        }

        return $rows;
    }
}