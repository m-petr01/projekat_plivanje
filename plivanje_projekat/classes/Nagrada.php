<?php

require_once __DIR__ . '/BaseModel.php';

class Nagrada extends BaseModel
{
    public function create(array $data): bool
    {
        $sql = "INSERT INTO nagrade
                (polaznik_id, naziv, opis, datum)
                VALUES
                (:polaznik_id, :naziv, :opis, :datum)";

        $stmt = $this->connection->prepare($sql);

        return $stmt->execute([
            ':polaznik_id' => $data['polaznik_id'],
            ':naziv' => $data['naziv'],
            ':opis' => $data['opis'],
            ':datum' => $data['datum']
        ]);
    }

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
