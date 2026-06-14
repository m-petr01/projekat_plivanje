<?php

require_once __DIR__ . '/BaseModel.php';
require_once __DIR__ . '/../interfaces/CrudInterface.php';

class Sertifikat extends BaseModel implements CrudInterface
{
    public function create(array $data): bool
    {
        $sql = "INSERT INTO sertifikati
                (polaznik_id, naziv, opis, datum_izdavanja)
                VALUES
                (:polaznik_id, :naziv, :opis, :datum_izdavanja)";

        $stmt = $this->connection->prepare($sql);

        return $stmt->execute([
            ':polaznik_id' => $data['polaznik_id'],
            ':naziv' => $data['naziv'],
            ':opis' => $data['opis'],
            ':datum_izdavanja' => $data['datum_izdavanja']
        ]);
    }

    public function read(): array
    {
        $sql = "SELECT
                    sertifikati.*,
                    polaznici.ime,
                    polaznici.prezime,
                    polaznici.nivo_id,
                    nivoi.naziv AS naziv_nivoa
                FROM sertifikati
                INNER JOIN polaznici
                    ON sertifikati.polaznik_id = polaznici.id
                LEFT JOIN nivoi
                    ON polaznici.nivo_id = nivoi.id
                ORDER BY
                    sertifikati.naziv ASC,
                    sertifikati.datum_izdavanja DESC,
                    sertifikati.id DESC";

        $stmt = $this->connection->query($sql);

        return $stmt->fetchAll();
    }

    public function findById(int $id): array|false
    {
        $sql = "SELECT
                    sertifikati.*,
                    polaznici.ime,
                    polaznici.prezime,
                    polaznici.nivo_id,
                    nivoi.naziv AS naziv_nivoa
                FROM sertifikati
                INNER JOIN polaznici
                    ON sertifikati.polaznik_id = polaznici.id
                LEFT JOIN nivoi
                    ON polaznici.nivo_id = nivoi.id
                WHERE sertifikati.id = :id";

        $stmt = $this->connection->prepare($sql);

        $stmt->execute([
            ':id' => $id
        ]);

        return $stmt->fetch();
    }

    public function update(int $id, array $data): bool
    {
        $sql = "UPDATE sertifikati
                SET
                    polaznik_id = :polaznik_id,
                    naziv = :naziv,
                    opis = :opis,
                    datum_izdavanja = :datum_izdavanja
                WHERE id = :id";

        $stmt = $this->connection->prepare($sql);

        return $stmt->execute([
            ':id' => $id,
            ':polaznik_id' => $data['polaznik_id'],
            ':naziv' => $data['naziv'],
            ':opis' => $data['opis'],
            ':datum_izdavanja' => $data['datum_izdavanja']
        ]);
    }

    public function delete(int $id): bool
    {
        $sql = "DELETE FROM sertifikati
                WHERE id = :id";

        $stmt = $this->connection->prepare($sql);

        return $stmt->execute([
            ':id' => $id
        ]);
    }

    public function postoji(
        int $polaznikId,
        string $naziv
    ): bool {
        $sql = "SELECT id
                FROM sertifikati
                WHERE polaznik_id = :polaznik_id
                AND naziv = :naziv
                LIMIT 1";

        $stmt = $this->connection->prepare($sql);

        $stmt->execute([
            ':polaznik_id' => $polaznikId,
            ':naziv' => $naziv
        ]);

        return (bool) $stmt->fetch();
    }
}
