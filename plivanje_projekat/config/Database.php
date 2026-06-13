<?php

class Database
{
    private string $host = 'localhost';
    private string $databaseName = 'plivanje_db';
    private string $username = 'root';
    private string $password = '';

    private ?PDO $connection = null;

    public function connect(): PDO
    {
        if ($this->connection === null) {
            try {
                $dsn = "mysql:host={$this->host};dbname={$this->databaseName};charset=utf8mb4";

                $this->connection = new PDO(
                    $dsn,
                    $this->username,
                    $this->password
                );

                $this->connection->setAttribute(
                    PDO::ATTR_ERRMODE,
                    PDO::ERRMODE_EXCEPTION
                );

                $this->connection->setAttribute(
                    PDO::ATTR_DEFAULT_FETCH_MODE,
                    PDO::FETCH_ASSOC
                );
            } catch (PDOException $exception) {
                die('Greška pri povezivanju sa bazom: ' . $exception->getMessage());
            }
        }

        return $this->connection;
    }
}