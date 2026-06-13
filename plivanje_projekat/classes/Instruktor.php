<?php

require_once __DIR__ . '/BaseModel.php';
require_once __DIR__ . '/../interfaces/CrudInterface.php';

class Instruktor extends BaseModel implements CrudInterface
{
    public function create(array $data): bool
    {
        $sql = "INSERT INTO instruktori
                (ime, prezime, telefon, email, specijalnost)
                VALUES
                (:ime, :prezime, :telefon, :email, :specijalnost)";

        $stmt = $this->connection->prepare($sql);

        return $stmt->execute([
            ':ime' => $data['ime'],
            ':prezime' => $data['prezime'],
            ':telefon' => $data['telefon'],
            ':email' => $data['email'],
            ':specijalnost' => $data['specijalnost']
        ]);
    }

    public function read(): array
    {
        $sql = "SELECT * FROM instruktori ORDER BY id DESC";

        $stmt = $this->connection->query($sql);

        return $stmt->fetchAll();
    }

    public function findById(int $id): array|false
    {
        $sql = "SELECT * FROM instruktori WHERE id = :id";

        $stmt = $this->connection->prepare($sql);

        $stmt->execute([
            ':id' => $id
        ]);

        return $stmt->fetch();
    }

   public function update(int $id, array $data): bool
{
    $sql = "UPDATE instruktori
            SET
                ime = :ime,
                prezime = :prezime,
                telefon = :telefon,
                email = :email,
                specijalnost = :specijalnost,
                godine_iskustva = :godine_iskustva,
                obrazovanje = :obrazovanje,
                biografija = :biografija,
                sertifikati_opis = :sertifikati_opis
            WHERE id = :id";

    $stmt = $this->connection->prepare($sql);

    return $stmt->execute([
        ':id' => $id,
        ':ime' => $data['ime'],
        ':prezime' => $data['prezime'],
        ':telefon' => $data['telefon'],
        ':email' => $data['email'],
        ':specijalnost' => $data['specijalnost'],
        ':godine_iskustva' => $data['godine_iskustva'],
        ':obrazovanje' => $data['obrazovanje'],
        ':biografija' => $data['biografija'],
        ':sertifikati_opis' => $data['sertifikati_opis']
    ]);
}
    public function delete(int $id): bool
    {
        $sql = "DELETE FROM instruktori WHERE id = :id";

        $stmt = $this->connection->prepare($sql);

        return $stmt->execute([
            ':id' => $id
        ]);
    }
}