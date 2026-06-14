<?php

require_once __DIR__ . '/BaseModel.php';

class Nagrada extends BaseModel
{
    public function readByPolaznik(int $polaznikId): array
    {
        $sql = "SELECT *
                FROM nagrade
                WHERE polaznik_id = :polaznik_id
                ORDER BY datum DESC, id DESC";

        $stmt = $this->connection->prepare($sql);

        $stmt->execute([
            ':polaznik_id' => $polaznikId
        ]);

        return $stmt->fetchAll();
    }
}
