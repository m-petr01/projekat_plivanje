<?php

require_once __DIR__ . '/classes/Session.php';
require_once __DIR__ . '/classes/Instruktor.php';

Session::requireLogin();

$instruktorModel = new Instruktor();

$id = filter_input(
    INPUT_GET,
    'id',
    FILTER_VALIDATE_INT
);

if (!$id) {
    die('Neispravan ID instruktora.');
}

$instruktor = $instruktorModel->findById($id);

if (!$instruktor) {
    die('Instruktor nije pronađen.');
}
?>

<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Profil instruktora
    </title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
</head>

<body class="bg-light">

<div class="container py-5">

    <div class="row justify-content-center">

        <div class="col-lg-8">

            <div class="card shadow-sm border-0">

                <div class="card-header bg-primary text-white">

                    <h1 class="h4 mb-0">
                        Profil instruktora
                    </h1>

                </div>

                <div class="card-body p-4">

                    <div class="text-center mb-4">

                        <div
                            class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center mb-3"
                            style="width: 100px; height: 100px; font-size: 2rem;"
                        >
                            <?= htmlspecialchars(
                                strtoupper(
                                    substr($instruktor['ime'], 0, 1)
                                    . substr($instruktor['prezime'], 0, 1)
                                )
                            ) ?>
                        </div>

                        <h2 class="h3 mb-1">
                            <?= htmlspecialchars(
                                $instruktor['ime']
                                . ' '
                                . $instruktor['prezime']
                            ) ?>
                        </h2>

                        <p class="text-primary fw-bold mb-0">
                            <?= htmlspecialchars(
                                $instruktor['specijalnost']
                                ?? 'Instruktor plivanja'
                            ) ?>
                        </p>

                    </div>

                    <hr>

                    <div class="row g-4">

                        <div class="col-md-6">

                            <div class="card h-100 border-0 bg-light">

                                <div class="card-body">

                                    <h3 class="h5">
                                        Kontakt
                                    </h3>

                                    <p class="mb-2">
                                        <strong>Email:</strong><br>

                                        <?= htmlspecialchars(
                                            $instruktor['email']
                                            ?? 'Nije unet'
                                        ) ?>
                                    </p>

                                    <p class="mb-0">
                                        <strong>Telefon:</strong><br>

                                        <?= htmlspecialchars(
                                            $instruktor['telefon']
                                            ?? 'Nije unet'
                                        ) ?>
                                    </p>

                                </div>

                            </div>

                        </div>

                        <div class="col-md-6">

                            <div class="card h-100 border-0 bg-light">

                                <div class="card-body">

                                    <h3 class="h5">
                                        Stručno iskustvo
                                    </h3>

                                    <p class="mb-2">
                                        <strong>Godine iskustva:</strong><br>

                                        <?= isset($instruktor['godine_iskustva'])
                                            && $instruktor['godine_iskustva'] !== null
                                            ? (int) $instruktor['godine_iskustva']
                                            : 'Nije uneto' ?>
                                    </p>

                                    <p class="mb-0">
                                        <strong>Obrazovanje:</strong><br>

                                        <?= htmlspecialchars(
                                            $instruktor['obrazovanje']
                                            ?? 'Nije uneto'
                                        ) ?>
                                    </p>

                                </div>

                            </div>

                        </div>

                    </div>

                    <div class="mt-4">

                        <h3 class="h5">
                            Biografija
                        </h3>

                        <div class="p-3 bg-light rounded">

                            <?= nl2br(
                                htmlspecialchars(
                                    $instruktor['biografija']
                                    ?? 'Biografija instruktora još nije uneta.'
                                )
                            ) ?>

                        </div>

                    </div>

                    <div class="mt-4">

                        <h3 class="h5">
                            Sertifikati i stručne kvalifikacije
                        </h3>

                        <div class="p-3 bg-light rounded">

                            <?= nl2br(
                                htmlspecialchars(
                                    $instruktor['sertifikati_opis']
                                    ?? 'Podaci o sertifikatima još nisu uneti.'
                                )
                            ) ?>

                        </div>

                    </div>

                    <div class="d-flex gap-2 mt-4">

                        <a
                            href="javascript:history.back()"
                            class="btn btn-secondary"
                        >
                            Nazad
                        </a>

                        <a
                            href="izmeni_instruktora.php?id=<?= (int) $instruktor['id'] ?>"
                            class="btn btn-warning"
                        >
                            Izmeni profil
                        </a>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

</body>
</html>