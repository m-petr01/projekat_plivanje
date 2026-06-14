<?php

require_once __DIR__ . '/BaseModel.php';

class Nagrada extends BaseModel
{
    public function create(array $data): bool
    {
        $sql = "INSERT INTO nagrade
                (
                    polaznik_id,
                    naziv,
                    opis,
                    datum
                )
                VALUES
                (
                    :polaznik_id,
                    :naziv,
                    :opis,
                    :datum
                )";

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

    public function findFirstByPolaznik(
        int $polaznikId
    ): array|false {
        $sql = "SELECT *
                FROM nagrade
                WHERE polaznik_id = :polaznik_id
                ORDER BY datum DESC, id DESC
                LIMIT 1";

        $stmt = $this->connection->prepare($sql);

        $stmt->execute([
            ':polaznik_id' => $polaznikId
        ]);

        return $stmt->fetch();
    }

    public function update(
        int $id,
        array $data
    ): bool {
        $sql = "UPDATE nagrade
                SET
                    naziv = :naziv,
                    opis = :opis,
                    datum = :datum
                WHERE id = :id";

        $stmt = $this->connection->prepare($sql);

        return $stmt->execute([
            ':id' => $id,
            ':naziv' => $data['naziv'],
            ':opis' => $data['opis'],
            ':datum' => $data['datum']
        ]);
    }

    public function delete(int $id): bool
    {
        $sql = "DELETE FROM nagrade
                WHERE id = :id";

        $stmt = $this->connection->prepare($sql);

        return $stmt->execute([
            ':id' => $id
        ]);
    }

    public function deleteByPolaznik(
        int $polaznikId
    ): bool {
        $sql = "DELETE FROM nagrade
                WHERE polaznik_id = :polaznik_id";

        $stmt = $this->connection->prepare($sql);

        return $stmt->execute([
            ':polaznik_id' => $polaznikId
        ]);
    }
}
