<?php

namespace App\Helper;

use PDO;

class DBController
{
    private $host;
    private $user;
    private $password;
    private $dbname;

    public function connect()
    {
        $this->host='localhost';
        $this->user='root';
        $this->password='qwer';
        $this->dbname='hyphenate';

        $dsn = 'mysql:host='. $this->host . ';dbname='. $this->dbname;

        $pdo = new \PDO($dsn,$this->user,$this->password);

        $statement = $pdo->query('SELECT * FROM patterns');
        while ($row = $statement->fetch(PDO::FETCH_ASSOC))
        {
            echo $row['pattern'];
        }
    }
}