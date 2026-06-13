<?php

require_once __DIR__ . '/../config/Database.php';

class BaseModel
{
    protected PDO $connection;

    public function __construct()
    {
        $database = new Database();
        $this->connection = $database->connect();
    }
}