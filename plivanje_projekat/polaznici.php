<?php

require_once __DIR__ . '/classes/Polaznik.php';

require_once __DIR__ . '/classes/Session.php';

Session::requireLogin();

$polaznikModel = new Polaznik();
$polaznici = $polaznikModel->read();

?>

<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Polaznici</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
</head>

<body class="bg-light">

<div class="container py-5">

    <div class="card shadow-sm">

        <div class="card-header bg-primary text-white">
            <h1 class="h4 mb-0">Evidencija polaznika</h1>
        </div>

        <div class="card-body">

            <div class="d-flex justify-content-between align-items-center mb-3">

                <p class="mb-0">
                    Ukupan broj polaznika:
                    <strong><?= count($polaznici) ?></strong>
                </p>

                <div class="d-flex gap-2">

                    <a
                        href="dashboard.php"
                        class="btn btn-secondary"
                    >
                        Nazad
                    </a>

                    <a
                        href="dodaj_polaznika.php"
                        class="btn btn-success"
                    >
                        Dodaj polaznika
                    </a>

                </div>

            </div>

            <?php if (empty($polaznici)): ?>

                <div class="alert alert-info">
                    Trenutno nema evidentiranih polaznika.
                </div>

            <?php else: ?>

                <div class="table-responsive">

                    <table class="table table-striped table-bordered align-middle">

                        <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Ime i prezime</th>
                            <th>Datum rođenja</th>
                            <th>Telefon</th>
                            <th>Email</th>
                            <th>Nivo znanja</th>
                            <th>Akcije</th>
                        </tr>
                        </thead>

                        <tbody>

                        <?php foreach ($polaznici as $polaznik): ?>

                            <tr>
                                <td>
                                    <?= htmlspecialchars($polaznik['id']) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars(
                                        $polaznik['ime'] . ' ' . $polaznik['prezime']
                                    ) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($polaznik['datum_rodjenja'] ?? '') ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($polaznik['telefon'] ?? '') ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($polaznik['email'] ?? '') ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars(
                                        $polaznik['naziv_nivoa'] ?? 'Nije određen'
                                    ) ?>
                                </td>

                                <td>
                                    <a
                                        href="izmeni_polaznika.php?id=<?= $polaznik['id'] ?>"
                                        class="btn btn-warning btn-sm"
                                    >
                                        Izmeni
                                    </a>

                                    <a
                                        href="obrisi_polaznika.php?id=<?= $polaznik['id'] ?>"
                                        class="btn btn-danger btn-sm"
                                        onclick="return confirm('Da li sigurno želiš da obrišeš polaznika?')"
                                    >
                                        Obriši
                                    </a>
                                </td>
                            </tr>

                        <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>

            <?php endif; ?>

        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
