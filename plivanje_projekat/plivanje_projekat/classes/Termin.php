<?php

require_once __DIR__ . '/BaseModel.php';
require_once __DIR__ . '/../interfaces/CrudInterface.php';

class Termin extends BaseModel implements CrudInterface
{
    public function create(array $data): bool
    {
        $sql = "INSERT INTO termini (
                    instruktor_id,
                    datum,
                    vreme,
                    trajanje_minuta,
                    bazen,
                    tip_treninga,
                    opis,
                    kapacitet,
                    rezervacija_dostupna
                ) VALUES (
                    :instruktor_id,
                    :datum,
                    :vreme,
                    :trajanje_minuta,
                    :bazen,
                    :tip_treninga,
                    :opis,
                    :kapacitet,
                    :rezervacija_dostupna
                )";

        $stmt = $this->connection->prepare($sql);

        return $stmt->execute([
            ':instruktor_id' => $data['instruktor_id'],
            ':datum' => $data['datum'],
            ':vreme' => $data['vreme'],
            ':trajanje_minuta' => $data['trajanje_minuta'],
            ':bazen' => $data['bazen'],
            ':tip_treninga' => $data['tip_treninga'],
            ':opis' => $data['opis'],
            ':kapacitet' => $data['kapacitet'],
            ':rezervacija_dostupna' => $data['rezervacija_dostupna']
        ]);
    }

    public function read(): array
    {
        $sql = "SELECT
                    termini.*,
                    CONCAT(
                        instruktori.ime,
                        ' ',
                        instruktori.prezime
                    ) AS instruktor_ime,

                    (
                        SELECT COUNT(*)
                        FROM rezervacije
                        WHERE rezervacije.termin_id = termini.id
                        AND rezervacije.status = 'rezervisano'
                    ) AS broj_prijavljenih

                FROM termini

                INNER JOIN instruktori
                    ON termini.instruktor_id = instruktori.id

                ORDER BY termini.datum ASC, termini.vreme ASC";

        $stmt = $this->connection->query($sql);

        return $stmt->fetchAll();
    }

    public function readByDate(string $datum): array
    {
        $sql = "SELECT
                    termini.*,
                    CONCAT(
                        instruktori.ime,
                        ' ',
                        instruktori.prezime
                    ) AS instruktor_ime,

                    (
                        SELECT COUNT(*)
                        FROM rezervacije
                        WHERE rezervacije.termin_id = termini.id
                        AND rezervacije.status = 'rezervisano'
                    ) AS broj_prijavljenih

                FROM termini

                INNER JOIN instruktori
                    ON termini.instruktor_id = instruktori.id

                WHERE termini.datum = :datum

                ORDER BY termini.vreme ASC";

        $stmt = $this->connection->prepare($sql);

        $stmt->execute([
            ':datum' => $datum
        ]);

        return $stmt->fetchAll();
    }

    public function findById(int $id): array|false
    {
        $sql = "SELECT
                    termini.*,
                    CONCAT(
                        instruktori.ime,
                        ' ',
                        instruktori.prezime
                    ) AS instruktor_ime,

                    (
                        SELECT COUNT(*)
                        FROM rezervacije
                        WHERE rezervacije.termin_id = termini.id
                        AND rezervacije.status = 'rezervisano'
                    ) AS broj_prijavljenih

                FROM termini

                INNER JOIN instruktori
                    ON termini.instruktor_id = instruktori.id

                WHERE termini.id = :id";

        $stmt = $this->connection->prepare($sql);

        $stmt->execute([
            ':id' => $id
        ]);

        return $stmt->fetch();
    }

    public function update(int $id, array $data): bool
    {
        $sql = "UPDATE termini
                SET
                    instruktor_id = :instruktor_id,
                    datum = :datum,
                    vreme = :vreme,
                    trajanje_minuta = :trajanje_minuta,
                    bazen = :bazen,
                    tip_treninga = :tip_treninga,
                    opis = :opis,
                    kapacitet = :kapacitet,
                    rezervacija_dostupna = :rezervacija_dostupna
                WHERE id = :id";

        $stmt = $this->connection->prepare($sql);

        return $stmt->execute([
            ':id' => $id,
            ':instruktor_id' => $data['instruktor_id'],
            ':datum' => $data['datum'],
            ':vreme' => $data['vreme'],
            ':trajanje_minuta' => $data['trajanje_minuta'],
            ':bazen' => $data['bazen'],
            ':tip_treninga' => $data['tip_treninga'],
            ':opis' => $data['opis'],
            ':kapacitet' => $data['kapacitet'],
            ':rezervacija_dostupna' => $data['rezervacija_dostupna']
        ]);
    }

    public function delete(int $id): bool
    {
        $sql = "DELETE FROM termini WHERE id = :id";

        $stmt = $this->connection->prepare($sql);

        return $stmt->execute([
            ':id' => $id
        ]);
    }

    public function readDatesForMonth(
        int $godina,
        int $mesec
    ): array {
        $pocetakMeseca = sprintf(
            '%04d-%02d-01',
            $godina,
            $mesec
        );

        $krajMeseca = date(
            'Y-m-t',
            strtotime($pocetakMeseca)
        );

        $sql = "SELECT DISTINCT datum
                FROM termini
                WHERE datum BETWEEN :pocetak AND :kraj";

        $stmt = $this->connection->prepare($sql);

        $stmt->execute([
            ':pocetak' => $pocetakMeseca,
            ':kraj' => $krajMeseca
        ]);

        return array_column(
            $stmt->fetchAll(),
            'datum'
        );
    }
}