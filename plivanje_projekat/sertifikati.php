<?php

require_once __DIR__ . '/classes/Session.php';
require_once __DIR__ . '/classes/Sertifikat.php';

Session::requireLogin();

$sertifikatModel = new Sertifikat();
$sertifikati = $sertifikatModel->read();

$tipoviSertifikata = [
    'Osnovna plivačka osposobljenost' => [
        'opis' =>
            'Potvrđuje da polaznik bezbedno i samostalno pliva osnovne stilove i poznaje pravila ponašanja na bazenu.',
        'minimalni_nivo' => 'Napredni',
        'boja' => 'primary',
        'uslovi' => [
            'Samostalno plivanje zadate distance',
            'Pravilno disanje i osnovna tehnika',
            'Poznavanje pravila bezbednosti',
            'Uspešna praktična provera'
        ]
    ],
    'Napredna plivačka tehnika' => [
        'opis' =>
            'Potvrđuje napredno poznavanje plivačkih stilova, startova, okreta i pravilne tehnike disanja.',
        'minimalni_nivo' => 'Napredni',
        'boja' => 'warning',
        'uslovi' => [
            'Sigurno izvođenje više stilova',
            'Pravilni startovi i okreti',
            'Plivanje dužih deonica',
            'Uspešna tehnička provera'
        ]
    ],
    'Takmičarska osposobljenost' => [
        'opis' =>
            'Potvrđuje da je polaznik spreman za učešće na zvaničnim plivačkim takmičenjima.',
        'minimalni_nivo' => 'Takmičarski',
        'boja' => 'danger',
        'uslovi' => [
            'Takmičarski nivo znanja',
            'Ostvarena propisana vremena',
            'Napredni startovi, okreti i završnice',
            'Odobrenje instruktora'
        ]
    ]
];

$izabraniSertifikat = null;

$id = filter_input(
    INPUT_GET,
    'id',
    FILTER_VALIDATE_INT
);

if ($id) {
    $izabraniSertifikat = $sertifikatModel->findById($id);
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

    <title>Sertifikati</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <style>
        .certificate-card {
            border: 0;
            overflow: hidden;
        }

        .certificate-header {
            color: white;
        }

        .holder-link {
            font-weight: 700;
            text-decoration: none;
        }

        .holder-link:hover {
            text-decoration: underline;
        }

        .details-card {
            border-left: 5px solid #0d6efd;
        }
    </style>
</head>

<body class="bg-light">

<div class="container py-5">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h1 class="h2 mb-1">
                Sertifikati polaznika
            </h1>

            <p class="text-muted mb-0">
                Pregled dostupnih sertifikata i polaznika koji su ih stekli.
            </p>
        </div>

        <div class="d-flex gap-2">

            <a
                href="dashboard.php"
                class="btn btn-secondary"
            >
                Nazad
            </a>

            <a
                href="dodaj_sertifikat.php"
                class="btn btn-success"
            >
                Dodeli sertifikat
            </a>

        </div>

    </div>

    <?php if (isset($_GET['dodato'])): ?>

        <div class="alert alert-success">
            Sertifikat je uspešno dodeljen.
        </div>

    <?php endif; ?>

    <div class="d-flex flex-column gap-4">

        <?php foreach ($tipoviSertifikata as $naziv => $podaci): ?>

            <?php
            $polazniciSaSertifikatom = array_filter(
                $sertifikati,
                static function (array $sertifikat) use ($naziv): bool {
                    return $sertifikat['naziv'] === $naziv;
                }
            );
            ?>

            <div class="card shadow-sm certificate-card">

                <div
                    class="card-header bg-<?= htmlspecialchars(
                        $podaci['boja']
                    ) ?> certificate-header p-3"
                >

                    <div class="d-flex justify-content-between align-items-center">

                        <h2 class="h4 mb-0">
                            <?= htmlspecialchars($naziv) ?>
                        </h2>

                        <span class="badge bg-light text-dark">
                            <?= count($polazniciSaSertifikatom) ?>
                            polaznika
                        </span>

                    </div>

                </div>

                <div class="card-body p-4">

                    <div class="row g-4">

                        <div class="col-lg-7">

                            <h3 class="h5">
                                Opis sertifikata
                            </h3>

                            <p>
                                <?= htmlspecialchars($podaci['opis']) ?>
                            </p>

                            <p>
                                <strong>Minimalni nivo:</strong>
                                <?= htmlspecialchars(
                                    $podaci['minimalni_nivo']
                                ) ?>
                            </p>

                            <h3 class="h5 mt-4">
                                Uslovi
                            </h3>

                            <ul class="mb-0">

                                <?php foreach ($podaci['uslovi'] as $uslov): ?>

                                    <li>
                                        <?= htmlspecialchars($uslov) ?>
                                    </li>

                                <?php endforeach; ?>

                            </ul>

                        </div>

                        <div class="col-lg-5">

                            <div class="bg-light rounded p-3 h-100">

                                <h3 class="h5">
                                    Polaznici sa sertifikatom
                                </h3>

                                <?php if (empty($polazniciSaSertifikatom)): ?>

                                    <p class="text-muted mb-0">
                                        Ovaj sertifikat još nije dodeljen nijednom polazniku.
                                    </p>

                                <?php else: ?>

                                    <ul class="list-group">

                                        <?php foreach (
                                            $polazniciSaSertifikatom as $sertifikat
                                        ): ?>

                                            <li
                                                class="list-group-item d-flex justify-content-between align-items-center"
                                            >

                                                <a
                                                    href="sertifikati.php?id=<?= (int) $sertifikat['id'] ?>#detalji-sertifikata"
                                                    class="holder-link text-<?= htmlspecialchars(
                                                        $podaci['boja']
                                                    ) ?>"
                                                >
                                                    <?= htmlspecialchars(
                                                        $sertifikat['ime']
                                                        . ' '
                                                        . $sertifikat['prezime']
                                                    ) ?>
                                                </a>

                                                <span
                                                    class="badge bg-<?= htmlspecialchars(
                                                        $podaci['boja']
                                                    ) ?>"
                                                >
                                                    Detalji
                                                </span>

                                            </li>

                                        <?php endforeach; ?>

                                    </ul>

                                <?php endif; ?>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        <?php endforeach; ?>

    </div>

    <?php if ($izabraniSertifikat): ?>

        <div
            id="detalji-sertifikata"
            class="card shadow-sm mt-5 details-card"
        >

            <div class="card-header bg-dark text-white">

                <div class="d-flex justify-content-between align-items-center">

                    <div>
                        <h2 class="h4 mb-1">
                            <?= htmlspecialchars(
                                $izabraniSertifikat['ime']
                                . ' '
                                . $izabraniSertifikat['prezime']
                            ) ?>
                        </h2>

                        <p class="mb-0">
                            <?= htmlspecialchars(
                                $izabraniSertifikat['naziv']
                            ) ?>
                        </p>
                    </div>

                    <a
                        href="sertifikati.php"
                        class="btn btn-outline-light btn-sm"
                    >
                        Zatvori
                    </a>

                </div>

            </div>

            <div class="card-body p-4">

                <p>
                    <strong>Nivo polaznika:</strong>
                    <?= htmlspecialchars(
                        $izabraniSertifikat['naziv_nivoa']
                        ?? 'Nije određen'
                    ) ?>
                </p>

                <p>
                    <strong>Datum izdavanja:</strong>
                    <?= htmlspecialchars(
                        date(
                            'd.m.Y.',
                            strtotime(
                                $izabraniSertifikat['datum_izdavanja']
                            )
                        )
                    ) ?>
                </p>

                <p class="mb-0">
                    <strong>Opis:</strong><br>
                    <?= nl2br(
                        htmlspecialchars(
                            $izabraniSertifikat['opis']
                            ?? 'Opis nije unet.'
                        )
                    ) ?>
                </p>

            </div>

        </div>

    <?php endif; ?>

</div>

</body>
</html>
