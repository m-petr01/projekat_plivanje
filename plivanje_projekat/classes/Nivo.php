<?php

require_once __DIR__ . '/BaseModel.php';

class Nivo extends BaseModel
{
    public function read(): array
    {
        $sql = "SELECT * FROM nivoi ORDER BY id ASC";

        $stmt = $this->connection->query($sql);

        return $stmt->fetchAll();
    }
}