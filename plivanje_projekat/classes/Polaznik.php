<?php

require_once __DIR__ . '/BaseModel.php';
require_once __DIR__ . '/../interfaces/CrudInterface.php';

class Polaznik extends BaseModel implements CrudInterface
{
    public function create(array $data): bool
    {
        $sql = "INSERT INTO polaznici
                (ime, prezime, datum_rodjenja, telefon, email, nivo_id)
                VALUES
                (:ime, :prezime, :datum_rodjenja, :telefon, :email, :nivo_id)";

        $stmt = $this->connection->prepare($sql);

        return $stmt->execute([
            ':ime' => $data['ime'],
            ':prezime' => $data['prezime'],
            ':datum_rodjenja' => $data['datum_rodjenja'],
            ':telefon' => $data['telefon'],
            ':email' => $data['email'],
            ':nivo_id' => $data['nivo_id']
        ]);
    }

    public function createAndReturnId(array $data): int|false
    {
        $sql = "INSERT INTO polaznici
                (ime, prezime, datum_rodjenja, telefon, email, nivo_id)
                VALUES
                (:ime, :prezime, :datum_rodjenja, :telefon, :email, :nivo_id)";

        $stmt = $this->connection->prepare($sql);

        $uspeh = $stmt->execute([
            ':ime' => $data['ime'],
            ':prezime' => $data['prezime'],
            ':datum_rodjenja' => $data['datum_rodjenja'],
            ':telefon' => $data['telefon'],
            ':email' => $data['email'],
            ':nivo_id' => $data['nivo_id']
        ]);

        if (!$uspeh) {
            return false;
        }

        return (int) $this->connection->lastInsertId();
    }

    public function read(): array
    {
        $sql = "SELECT polaznici.*, nivoi.naziv AS naziv_nivoa
                FROM polaznici
                LEFT JOIN nivoi ON polaznici.nivo_id = nivoi.id
                ORDER BY polaznici.id DESC";

        $stmt = $this->connection->query($sql);

        return $stmt->fetchAll();
    }

    public function findById(int $id): array|false
{
    $sql = "SELECT * FROM polaznici WHERE id = :id";

    $stmt = $this->connection->prepare($sql);

    $stmt->execute([
        ':id' => $id
    ]);

    return $stmt->fetch();
}

    public function update(int $id, array $data): bool
    {
        $sql = "UPDATE polaznici
                SET ime = :ime,
                    prezime = :prezime,
                    datum_rodjenja = :datum_rodjenja,
                    telefon = :telefon,
                    email = :email,
                    nivo_id = :nivo_id
                WHERE id = :id";

        $stmt = $this->connection->prepare($sql);

        return $stmt->execute([
            ':id' => $id,
            ':ime' => $data['ime'],
            ':prezime' => $data['prezime'],
            ':datum_rodjenja' => $data['datum_rodjenja'],
            ':telefon' => $data['telefon'],
            ':email' => $data['email'],
            ':nivo_id' => $data['nivo_id']
        ]);
    }

    public function delete(int $id): bool
    {
        $sql = "DELETE FROM polaznici WHERE id = :id";

        $stmt = $this->connection->prepare($sql);

        return $stmt->execute([
            ':id' => $id
        ]);
    }
}
