<?php

namespace App\Models;

use \PDO;

class Db
{
    private $host = 'localhost';
    private $user = 'u256988789_cadersindia';
    private $pass = 'C@dersIndi@!!2023';
    private $dbname = 'u256988789_cadersindia';

    public function connect()
    {
        $conn_str = "mysql:host=$this->host;dbname=$this->dbname";
        $conn = new PDO($conn_str, $this->user, $this->pass);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        return $conn;
    }
}
