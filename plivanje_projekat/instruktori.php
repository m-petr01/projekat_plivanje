<?php

require_once __DIR__ . '/classes/Session.php';
require_once __DIR__ . '/classes/Instruktor.php';

Session::requireLogin();

$instruktorModel = new Instruktor();
$instruktori = $instruktorModel->read();

?>

<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Instruktori</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
</head>

<body class="bg-light">

<div class="container py-5">

    <div class="card shadow-sm">

        <div class="card-header bg-primary text-white">
            <h1 class="h4 mb-0">
                Evidencija instruktora
            </h1>
        </div>

        <div class="card-body">

            <div class="d-flex justify-content-between align-items-center mb-3">

                <p class="mb-0">
                    Ukupan broj instruktora:
                    <strong><?= count($instruktori) ?></strong>
                </p>

                <div class="d-flex gap-2">

                    <a href="dashboard.php" class="btn btn-secondary">
                        Nazad
                    </a>

                    <a href="dodaj_instruktora.php" class="btn btn-success">
                        Dodaj instruktora
                    </a>

                </div>

            </div>

            <?php if (empty($instruktori)): ?>

                <div class="alert alert-info">
                    Trenutno nema evidentiranih instruktora.
                </div>

            <?php else: ?>

                <div class="table-responsive">

                    <table class="table table-striped table-bordered align-middle">

                        <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Ime i prezime</th>
                            <th>Telefon</th>
                            <th>Email</th>
                            <th>Specijalnost</th>
                            <th>Akcije</th>
                        </tr>
                        </thead>

                        <tbody>

                        <?php foreach ($instruktori as $instruktor): ?>

                            <tr>
                                <td>
                                    <?= htmlspecialchars($instruktor['id']) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars(
                                        $instruktor['ime'] . ' ' . $instruktor['prezime']
                                    ) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($instruktor['telefon'] ?? '') ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($instruktor['email'] ?? '') ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($instruktor['specijalnost'] ?? '') ?>
                                </td>

                                <td>
                                    <a
                                        href="izmeni_instruktora.php?id=<?= $instruktor['id'] ?>"
                                        class="btn btn-warning btn-sm"
                                    >
                                        Izmeni
                                    </a>

                                    <a
                                        href="obrisi_instruktora.php?id=<?= $instruktor['id'] ?>"
                                        class="btn btn-danger btn-sm"
                                        onclick="return confirm('Da li sigurno želiš da obrišeš instruktora?')"
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

</body>
</html>