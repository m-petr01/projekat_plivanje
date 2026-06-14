<?php

require_once __DIR__ . '/classes/Session.php';
require_once __DIR__ . '/classes/Nivo.php';
require_once __DIR__ . '/classes/Polaznik.php';
require_once __DIR__ . '/classes/Nagrada.php';

Session::requireLogin();

$nivoModel = new Nivo();
$polaznikModel = new Polaznik();
$nagradaModel = new Nagrada();

$nivoi = $nivoModel->read();
$polaznici = $polaznikModel->read();

$opisiNivoa = [
    'početni' => [
        'opis' => 'Nivo namenjen neplivačima i polaznicima koji tek stiču sigurnost u vodi. Fokus je na pravilnom disanju, plutanju, osnovnim pokretima nogu i samostalnom kretanju kroz vodu.',
        'zahtevnost' => 'Niska',
        'zvezdice' => '★☆☆☆',
        'uslovi' => [
            'Privikavanje na vodu',
            'Osnovno disanje u vodi',
            'Plutanje i održavanje ravnoteže',
            'Prvi samostalni pokreti'
        ]
    ],
    'srednji' => [
        'opis' => 'Nivo za polaznike koji se samostalno održavaju u vodi i poznaju osnovne elemente plivanja. Radi se na pravilnijoj tehnici, povezivanju pokreta i povećanju izdržljivosti.',
        'zahtevnost' => 'Umerena',
        'zvezdice' => '★★☆☆',
        'uslovi' => [
            'Samostalno plivanje kraćih deonica',
            'Osnovna tehnika kraula i leđnog stila',
            'Kontrolisano disanje',
            'Razvoj osnovne kondicije'
        ]
    ],
    'napredni' => [
        'opis' => 'Nivo za iskusne plivače koji pravilno izvode više stilova i mogu da podnesu složenije treninge. Akcenat je na tehnici, brzini, izdržljivosti i individualnom napretku.',
        'zahtevnost' => 'Visoka',
        'zvezdice' => '★★★☆',
        'uslovi' => [
            'Sigurno izvođenje više stilova',
            'Plivanje dužih deonica bez prekida',
            'Napredna tehnika starta i okreta',
            'Veća fizička i tehnička zahtevnost'
        ]
    ],
    'takmičarski' => [
        'opis' => 'Najviši nivo namenjen plivačima koji se pripremaju za takmičenja. Treninzi obuhvataju precizno planiranje opterećenja, merenje rezultata, taktičku pripremu i usavršavanje tehnike.',
        'zahtevnost' => 'Veoma visoka',
        'zvezdice' => '★★★★',
        'uslovi' => [
            'Takmičarska tehnika plivačkih stilova',
            'Rad na brzini, tempu i prolaznim vremenima',
            'Napredni startovi, okreti i završnice',
            'Priprema za zvanična takmičenja'
        ]
    ]
];

$bojeNivoa = [
    'početni' => 'primary',
    'srednji' => 'info',
    'napredni' => 'warning',
    'takmičarski' => 'danger'
];

$izabraniPolaznik = null;
$izabraniNivoNaziv = '';
$nagrade = [];
$greska = '';

$polaznikId = filter_input(
    INPUT_GET,
    'polaznik_id',
    FILTER_VALIDATE_INT
);

if ($polaznikId) {
    $izabraniPolaznik = $polaznikModel->findById($polaznikId);

    if (!$izabraniPolaznik) {
        $greska = 'Izabrani polaznik nije pronađen.';
    } else {
        foreach ($nivoi as $nivo) {
            if ((int) $nivo['id'] === (int) $izabraniPolaznik['nivo_id']) {
                $izabraniNivoNaziv = $nivo['naziv'];
                break;
            }
        }

        $normalizovanNivo = mb_strtolower(
            trim($izabraniNivoNaziv),
            'UTF-8'
        );

        if (!in_array(
            $normalizovanNivo,
            ['napredni', 'takmičarski'],
            true
        )) {
            $izabraniPolaznik = null;
            $greska =
                'Detalji i nagrade prikazuju se samo za napredni i takmičarski nivo.';
        } else {
            $nagrade = $nagradaModel->readByPolaznik($polaznikId);
        }
    }
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

    <title>Nivoi znanja</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <style>
        .level-card {
            border: 0;
            overflow: hidden;
        }

        .level-header {
            color: white;
        }

        .difficulty-stars {
            font-size: 1.25rem;
            letter-spacing: 2px;
        }

        .participant-list {
            margin-bottom: 0;
        }

        .participant-link {
            font-weight: 700;
            text-decoration: none;
        }

        .participant-link:hover {
            text-decoration: underline;
        }

        .award-card {
            border-left: 5px solid #ffc107;
        }
    </style>
</head>

<body class="bg-light">

<div class="container py-5">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h1 class="h2 mb-1">
                Nivoi znanja polaznika
            </h1>

            <p class="text-muted mb-0">
                Pregled nivoa, zahteva i polaznika škole plivanja.
            </p>
        </div>

        <a
            href="dashboard.php"
            class="btn btn-secondary"
        >
            Nazad
        </a>

    </div>

    <?php if ($greska !== ''): ?>

        <div class="alert alert-warning">
            <?= htmlspecialchars($greska) ?>
        </div>

    <?php endif; ?>

    <div class="d-flex flex-column gap-4">

        <?php foreach ($nivoi as $nivo): ?>

            <?php
            $nazivNivoa = trim($nivo['naziv']);
            $kljucNivoa = mb_strtolower($nazivNivoa, 'UTF-8');

            $podaciNivoa = $opisiNivoa[$kljucNivoa] ?? [
                'opis' => $nivo['opis']
                    ?? 'Opis ovog nivoa još nije unet.',
                'zahtevnost' => 'Nije određena',
                'zvezdice' => '☆☆☆☆',
                'uslovi' => []
            ];

            if (
                !empty($nivo['opis'])
                && trim($nivo['opis']) !== ''
            ) {
                $podaciNivoa['opis'] = $nivo['opis'];
            }

            $boja = $bojeNivoa[$kljucNivoa] ?? 'secondary';

            $polazniciNivoa = array_filter(
                $polaznici,
                static function (array $polaznik) use ($nivo): bool {
                    return (int) ($polaznik['nivo_id'] ?? 0)
                        === (int) $nivo['id'];
                }
            );

            $imeJeKlikabilno = in_array(
                $kljucNivoa,
                ['napredni', 'takmičarski'],
                true
            );
            ?>

            <div class="card shadow-sm level-card">

                <div class="card-header bg-<?= $boja ?> level-header p-3">

                    <div class="d-flex justify-content-between align-items-center">

                        <h2 class="h4 mb-0">
                            <?= htmlspecialchars($nazivNivoa) ?>
                        </h2>

                        <span class="badge bg-light text-dark">
                            <?= count($polazniciNivoa) ?>
                            polaznika
                        </span>

                    </div>

                </div>

                <div class="card-body p-4">

                    <div class="row g-4">

                        <div class="col-lg-7">

                            <h3 class="h5">
                                Opis nivoa
                            </h3>

                            <p>
                                <?= nl2br(
                                    htmlspecialchars(
                                        $podaciNivoa['opis']
                                    )
                                ) ?>
                            </p>

                            <h3 class="h5 mt-4">
                                Zahtevi
                            </h3>

                            <?php if (!empty($podaciNivoa['uslovi'])): ?>

                                <ul class="mb-0">

                                    <?php foreach (
                                        $podaciNivoa['uslovi'] as $uslov
                                    ): ?>

                                        <li>
                                            <?= htmlspecialchars($uslov) ?>
                                        </li>

                                    <?php endforeach; ?>

                                </ul>

                            <?php else: ?>

                                <p class="text-muted mb-0">
                                    Zahtevi za ovaj nivo još nisu uneti.
                                </p>

                            <?php endif; ?>

                        </div>

                        <div class="col-lg-5">

                            <div class="bg-light rounded p-3 h-100">

                                <h3 class="h5">
                                    Zahtevnost
                                </h3>

                                <div class="difficulty-stars text-warning mb-1">
                                    <?= htmlspecialchars(
                                        $podaciNivoa['zvezdice']
                                    ) ?>
                                </div>

                                <p class="fw-bold mb-4">
                                    <?= htmlspecialchars(
                                        $podaciNivoa['zahtevnost']
                                    ) ?>
                                </p>

                                <h3 class="h5">
                                    Polaznici
                                </h3>

                                <?php if (empty($polazniciNivoa)): ?>

                                    <p class="text-muted mb-0">
                                        Trenutno nema polaznika na ovom nivou.
                                    </p>

                                <?php else: ?>

                                    <ul class="list-group participant-list">

                                        <?php foreach (
                                            $polazniciNivoa as $polaznik
                                        ): ?>

                                            <li
                                                class="list-group-item d-flex justify-content-between align-items-center"
                                            >

                                                <?php if ($imeJeKlikabilno): ?>

                                                    <a
                                                        href="nivoi.php?polaznik_id=<?= (int) $polaznik['id'] ?>#nagrade-polaznika"
                                                        class="participant-link text-<?= $boja ?>"
                                                    >
                                                        <?= htmlspecialchars(
                                                            $polaznik['ime']
                                                            . ' '
                                                            . $polaznik['prezime']
                                                        ) ?>
                                                    </a>

                                                    <span class="badge bg-<?= $boja ?>">
                                                        Profil
                                                    </span>

                                                <?php else: ?>

                                                    <span>
                                                        <?= htmlspecialchars(
                                                            $polaznik['ime']
                                                            . ' '
                                                            . $polaznik['prezime']
                                                        ) ?>
                                                    </span>

                                                <?php endif; ?>

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

    <?php if ($izabraniPolaznik): ?>

        <div
            id="nagrade-polaznika"
            class="card shadow-sm mt-5 award-card"
        >

            <div class="card-header bg-dark text-white">

                <div class="d-flex justify-content-between align-items-center">

                    <div>
                        <h2 class="h4 mb-1">
                            <?= htmlspecialchars(
                                $izabraniPolaznik['ime']
                                . ' '
                                . $izabraniPolaznik['prezime']
                            ) ?>
                        </h2>

                        <p class="mb-0">
                            Nivo:
                            <strong>
                                <?= htmlspecialchars(
                                    $izabraniNivoNaziv
                                ) ?>
                            </strong>
                        </p>
                    </div>

                    <a
                        href="nivoi.php"
                        class="btn btn-outline-light btn-sm"
                    >
                        Zatvori
                    </a>

                </div>

            </div>

            <div class="card-body p-4">

                <h3 class="h5 mb-3">
                    Nagrade i rezultati
                </h3>

                <?php if (empty($nagrade)): ?>

                    <div class="alert alert-info mb-0">
                        Za ovog polaznika još nisu evidentirane nagrade.
                    </div>

                <?php else: ?>

                    <div class="row g-3">

                        <?php foreach ($nagrade as $nagrada): ?>

                            <div class="col-md-6">

                                <div class="card h-100 bg-light">

                                    <div class="card-body">

                                        <h4 class="h6 fw-bold">
                                            <?= htmlspecialchars(
                                                $nagrada['naziv']
                                            ) ?>
                                        </h4>

                                        <?php if (
                                            !empty($nagrada['datum'])
                                        ): ?>

                                            <p class="small text-muted mb-2">
                                                Datum:
                                                <?= htmlspecialchars(
                                                    date(
                                                        'd.m.Y.',
                                                        strtotime(
                                                            $nagrada['datum']
                                                        )
                                                    )
                                                ) ?>
                                            </p>

                                        <?php endif; ?>

                                        <p class="mb-0">
                                            <?= nl2br(
                                                htmlspecialchars(
                                                    $nagrada['opis']
                                                    ?? 'Opis nije unet.'
                                                )
                                            ) ?>
                                        </p>

                                    </div>

                                </div>

                            </div>

                        <?php endforeach; ?>

                    </div>

                <?php endif; ?>

            </div>

        </div>

    <?php endif; ?>

</div>

</body>
</html>
