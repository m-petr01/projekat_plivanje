<?php

require_once __DIR__ . '/classes/Session.php';
require_once __DIR__ . '/classes/Polaznik.php';
require_once __DIR__ . '/classes/Sertifikat.php';

Session::requireLogin();

$polaznikModel = new Polaznik();
$sertifikatModel = new Sertifikat();

$polaznici = $polaznikModel->read();

$tipoviSertifikata = [
    'Osnovna plivačka osposobljenost' => [
        'napredni',
        'takmičarski'
    ],
    'Napredna plivačka tehnika' => [
        'napredni',
        'takmičarski'
    ],
    'Takmičarska osposobljenost' => [
        'takmičarski'
    ]
];

$greska = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $polaznikId = filter_input(
        INPUT_POST,
        'polaznik_id',
        FILTER_VALIDATE_INT
    );

    $naziv = trim($_POST['naziv'] ?? '');
    $opis = trim($_POST['opis'] ?? '');
    $datumIzdavanja = $_POST['datum_izdavanja'] ?? '';

    $izabraniPolaznik = null;

    foreach ($polaznici as $polaznik) {
        if ((int) $polaznik['id'] === (int) $polaznikId) {
            $izabraniPolaznik = $polaznik;
            break;
        }
    }

    if (!$polaznikId || !$izabraniPolaznik) {
        $greska = 'Izaberite polaznika.';
    } elseif (!array_key_exists($naziv, $tipoviSertifikata)) {
        $greska = 'Izaberite važeći sertifikat.';
    } elseif ($datumIzdavanja === '') {
        $greska = 'Datum izdavanja je obavezan.';
    } else {
        $nivoPolaznika = mb_strtolower(
            trim($izabraniPolaznik['naziv_nivoa'] ?? ''),
            'UTF-8'
        );

        $dozvoljeniNivoi = $tipoviSertifikata[$naziv];

        if (!in_array($nivoPolaznika, $dozvoljeniNivoi, true)) {
            $greska =
                'Izabrani polaznik ne ispunjava minimalni nivo '
                . 'za ovaj sertifikat.';
        } elseif (
            $sertifikatModel->postoji(
                (int) $polaznikId,
                $naziv
            )
        ) {
            $greska =
                'Ovaj polaznik već poseduje izabrani sertifikat.';
        } else {
            $uspeh = $sertifikatModel->create([
                'polaznik_id' => (int) $polaznikId,
                'naziv' => $naziv,
                'opis' => $opis !== '' ? $opis : null,
                'datum_izdavanja' => $datumIzdavanja
            ]);

            if ($uspeh) {
                header('Location: sertifikati.php?dodato=1');
                exit;
            }

            $greska =
                'Došlo je do greške prilikom dodele sertifikata.';
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

    <title>Dodela sertifikata</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
</head>

<body class="bg-light">

<div class="container py-5">

    <div class="row justify-content-center">

        <div class="col-lg-7">

            <div class="card shadow-sm">

                <div class="card-header bg-success text-white">

                    <h1 class="h4 mb-0">
                        Dodeli sertifikat
                    </h1>

                </div>

                <div class="card-body">

                    <?php if ($greska !== ''): ?>

                        <div class="alert alert-danger">
                            <?= htmlspecialchars($greska) ?>
                        </div>

                    <?php endif; ?>

                    <div class="alert alert-info">
                        Sertifikate mogu dobiti samo polaznici naprednog
                        i takmičarskog nivoa. Takmičarski sertifikat mogu
                        dobiti isključivo takmičarski polaznici.
                    </div>

                    <form method="POST">

                        <div class="mb-3">

                            <label
                                for="naziv"
                                class="form-label"
                            >
                                Sertifikat
                            </label>

                            <select
                                id="naziv"
                                name="naziv"
                                class="form-select"
                                required
                            >
                                <option value="">
                                    Izaberite sertifikat
                                </option>

                                <?php foreach (
                                    array_keys($tipoviSertifikata) as $naziv
                                ): ?>

                                    <option
                                        value="<?= htmlspecialchars($naziv) ?>"
                                        <?= ($_POST['naziv'] ?? '') === $naziv
                                            ? 'selected'
                                            : '' ?>
                                    >
                                        <?= htmlspecialchars($naziv) ?>
                                    </option>

                                <?php endforeach; ?>

                            </select>

                        </div>

                        <div class="mb-3">

                            <label
                                for="polaznik_id"
                                class="form-label"
                            >
                                Polaznik
                            </label>

                            <select
                                id="polaznik_id"
                                name="polaznik_id"
                                class="form-select"
                                required
                            >
                                <option value="">
                                    Izaberite polaznika
                                </option>

                                <?php foreach ($polaznici as $polaznik): ?>

                                    <?php
                                    $nivo = mb_strtolower(
                                        trim(
                                            $polaznik['naziv_nivoa']
                                            ?? ''
                                        ),
                                        'UTF-8'
                                    );

                                    if (!in_array(
                                        $nivo,
                                        ['napredni', 'takmičarski'],
                                        true
                                    )) {
                                        continue;
                                    }
                                    ?>

                                    <option
                                        value="<?= (int) $polaznik['id'] ?>"
                                        <?= ($_POST['polaznik_id'] ?? '') == $polaznik['id']
                                            ? 'selected'
                                            : '' ?>
                                    >
                                        <?= htmlspecialchars(
                                            $polaznik['ime']
                                            . ' '
                                            . $polaznik['prezime']
                                            . ' — '
                                            . $polaznik['naziv_nivoa']
                                        ) ?>
                                    </option>

                                <?php endforeach; ?>

                            </select>

                        </div>

                        <div class="mb-3">

                            <label
                                for="datum_izdavanja"
                                class="form-label"
                            >
                                Datum izdavanja
                            </label>

                            <input
                                type="date"
                                id="datum_izdavanja"
                                name="datum_izdavanja"
                                class="form-control"
                                value="<?= htmlspecialchars(
                                    $_POST['datum_izdavanja']
                                    ?? date('Y-m-d')
                                ) ?>"
                                required
                            >

                        </div>

                        <div class="mb-4">

                            <label
                                for="opis"
                                class="form-label"
                            >
                                Opis i napomena
                            </label>

                            <textarea
                                id="opis"
                                name="opis"
                                class="form-control"
                                rows="4"
                                placeholder="Praktična provera, rezultat ili dodatna napomena."
                            ><?= htmlspecialchars(
                                $_POST['opis'] ?? ''
                            ) ?></textarea>

                        </div>

                        <div class="d-flex gap-2">

                            <button
                                type="submit"
                                class="btn btn-success"
                            >
                                Dodeli sertifikat
                            </button>

                            <a
                                href="sertifikati.php"
                                class="btn btn-secondary"
                            >
                                Otkaži
                            </a>

                        </div>

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>

</body>
</html>
