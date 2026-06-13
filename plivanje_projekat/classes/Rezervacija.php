<?php

require_once __DIR__ . '/BaseModel.php';

class Rezervacija extends BaseModel
{
    public function brojAktivnihRezervacija(int $terminId): int
    {
        $sql = "SELECT COUNT(*)
                FROM rezervacije
                WHERE termin_id = :termin_id
                AND status = 'rezervisano'";

        $stmt = $this->connection->prepare($sql);

        $stmt->execute([
            ':termin_id' => $terminId
        ]);

        return (int) $stmt->fetchColumn();
    }

    public function findByTerminAndPolaznik(
        int $terminId,
        int $polaznikId
    ): array|false {
        $sql = "SELECT *
                FROM rezervacije
                WHERE termin_id = :termin_id
                AND polaznik_id = :polaznik_id
                LIMIT 1";

        $stmt = $this->connection->prepare($sql);

        $stmt->execute([
            ':termin_id' => $terminId,
            ':polaznik_id' => $polaznikId
        ]);

        return $stmt->fetch();
    }

    public function create(
        int $terminId,
        int $polaznikId
    ): bool {
        $postojecaRezervacija = $this->findByTerminAndPolaznik(
            $terminId,
            $polaznikId
        );

        if ($postojecaRezervacija) {
            if ($postojecaRezervacija['status'] === 'rezervisano') {
                return false;
            }

            $sql = "UPDATE rezervacije
                    SET status = 'rezervisano',
                        datum_rezervacije = CURRENT_TIMESTAMP
                    WHERE id = :id";

            $stmt = $this->connection->prepare($sql);

            return $stmt->execute([
                ':id' => $postojecaRezervacija['id']
            ]);
        }

        $sql = "INSERT INTO rezervacije (
                    termin_id,
                    polaznik_id,
                    status
                ) VALUES (
                    :termin_id,
                    :polaznik_id,
                    'rezervisano'
                )";

        $stmt = $this->connection->prepare($sql);

        return $stmt->execute([
            ':termin_id' => $terminId,
            ':polaznik_id' => $polaznikId
        ]);
    }
}