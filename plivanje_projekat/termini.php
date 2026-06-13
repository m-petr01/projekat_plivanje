<?php

require_once __DIR__ . '/classes/Session.php';
require_once __DIR__ . '/classes/Termin.php';

Session::requireLogin();

$poruka = '';

if (isset($_GET['rezervisano'])) {
    $poruka = 'Rezervacija je uspešno sačuvana.';
} elseif (isset($_GET['izmenjeno'])) {
    $poruka = 'Termin je uspešno izmenjen.';
} elseif (isset($_GET['obrisano'])) {
    $poruka = 'Termin je uspešno obrisan.';
}

$terminModel = new Termin();

$trenutnaGodina = (int) date('Y');
$trenutniMesec = (int) date('m');

$godina = filter_input(
    INPUT_GET,
    'godina',
    FILTER_VALIDATE_INT
) ?: $trenutnaGodina;

$mesec = filter_input(
    INPUT_GET,
    'mesec',
    FILTER_VALIDATE_INT
) ?: $trenutniMesec;

if ($mesec < 1 || $mesec > 12) {
    $mesec = $trenutniMesec;
}

$izabraniDatum = $_GET['datum'] ?? date('Y-m-d');

$validanDatum = DateTime::createFromFormat(
    'Y-m-d',
    $izabraniDatum
);

if (
    !$validanDatum
    || $validanDatum->format('Y-m-d') !== $izabraniDatum
) {
    $izabraniDatum = date('Y-m-d');
}

$terminiZaDatum = $terminModel->readByDate(
    $izabraniDatum
);

$datumiSaTerminima = $terminModel->readDatesForMonth(
    $godina,
    $mesec
);

$prviDanMeseca = new DateTime(
    sprintf('%04d-%02d-01', $godina, $mesec)
);

$brojDanaUMesecu = (int) $prviDanMeseca->format('t');

$danUNedeljiPrvogDana = (int) $prviDanMeseca->format('N');

$naziviMeseci = [
    1 => 'Januar',
    2 => 'Februar',
    3 => 'Mart',
    4 => 'April',
    5 => 'Maj',
    6 => 'Jun',
    7 => 'Jul',
    8 => 'Avgust',
    9 => 'Septembar',
    10 => 'Oktobar',
    11 => 'Novembar',
    12 => 'Decembar'
];

$prethodniMesec = $mesec - 1;
$prethodnaGodina = $godina;

if ($prethodniMesec < 1) {
    $prethodniMesec = 12;
    $prethodnaGodina--;
}

$sledeciMesec = $mesec + 1;
$sledecaGodina = $godina;

if ($sledeciMesec > 12) {
    $sledeciMesec = 1;
    $sledecaGodina++;
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

    <title>Termini časova plivanja</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <style>
        .calendar-table {
            table-layout: fixed;
        }

        .calendar-day {
            min-height: 90px;
            display: block;
            padding: 10px;
            text-decoration: none;
            color: inherit;
            border-radius: 6px;
        }

        .calendar-day:hover {
            background-color: #e9ecef;
        }

        .selected-day {
            background-color: #cfe2ff;
            border: 2px solid #0d6efd;
        }

        .day-with-event {
            font-weight: bold;
        }

        .event-dot {
            display: inline-block;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background-color: #198754;
            margin-left: 5px;
        }
    </style>
</head>

<body class="bg-light">

<div class="container py-5">

<?php if ($poruka !== ''): ?>

    <div class="alert alert-success">
        <?= htmlspecialchars($poruka) ?>
    </div>

<?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h1 class="h3 mb-1">
                Termini časova plivanja
            </h1>

            <p class="text-muted mb-0">
                Kliknite na dan da biste videli termine.
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
                href="dodaj_termin.php?datum=<?= urlencode($izabraniDatum) ?>"
                class="btn btn-success"
            >
                Dodaj termin
            </a>

        </div>

    </div>

    <div class="card shadow-sm mb-4">

        <div class="card-header bg-primary text-white">

            <h2 class="h5 mb-0">
                Termini za
                <?= htmlspecialchars(
                    date(
                        'd.m.Y.',
                        strtotime($izabraniDatum)
                    )
                ) ?>
            </h2>

        </div>

        <div class="card-body">

            <?php if (empty($terminiZaDatum)): ?>

                <div class="alert alert-info mb-0">
                    Za izabrani datum nema zakazanih termina.
                </div>

            <?php else: ?>

                <div class="row g-3">

                    <?php foreach ($terminiZaDatum as $termin): ?>

                        <?php
                        $vremePocetka = new DateTime(
                            $termin['vreme']
                        );

                        $vremeZavrsetka = clone $vremePocetka;

                        $vremeZavrsetka->modify(
                            '+' . (int) $termin['trajanje_minuta'] . ' minutes'
                        );

                        $brojPrijavljenih = (int) (
                            $termin['broj_prijavljenih'] ?? 0
                        );

                        $kapacitet = (int) (
                            $termin['kapacitet'] ?? 0
                        );

                        $slobodnaMesta = max(
                            0,
                            $kapacitet - $brojPrijavljenih
                        );

                        $rezervacijaOtvorena =
                            (int) $termin['rezervacija_dostupna'] === 1;

                        $rezervacijaDostupna =
                            $rezervacijaOtvorena
                            && $slobodnaMesta > 0;
                        ?>

                        <div class="col-md-6">

                            <div class="card h-100 border-primary">

                                <div class="card-body">

                                    <div class="d-flex justify-content-between align-items-start mb-3">

                                        <h3 class="h5 mb-0">
                                            <?= htmlspecialchars(
                                                $termin['tip_treninga']
                                            ) ?>
                                        </h3>

                                        <?php if ($rezervacijaDostupna): ?>

                                            <span class="badge bg-success">
                                                Rezervacija dostupna
                                            </span>

                                        <?php elseif ($slobodnaMesta <= 0): ?>

                                            <span class="badge bg-danger">
                                                Termin je popunjen
                                            </span>

                                        <?php else: ?>

                                            <span class="badge bg-secondary">
                                                Rezervacija nije dostupna
                                            </span>

                                        <?php endif; ?>

                                    </div>

                                    <p class="mb-2">
                                        <strong>Vreme:</strong>

                                        <?= $vremePocetka->format('H:i') ?>
                                        –
                                        <?= $vremeZavrsetka->format('H:i') ?>
                                    </p>

                                    <p class="mb-2">
                                        <strong>Instruktor:</strong>

                                        <a
                                            href="profil_instruktora.php?id=<?= (int) $termin['instruktor_id'] ?>"
                                            class="fw-bold text-decoration-none"
                                        >
                                            <?= htmlspecialchars(
                                                $termin['instruktor_ime']
                                            ) ?>
                                        </a>
                                    </p>

                                    <p class="mb-2">
                                        <strong>Bazen:</strong>

                                        <?= htmlspecialchars(
                                            $termin['bazen']
                                            ?? 'Nije određen'
                                        ) ?>
                                    </p>

                                    <p class="mb-2">
                                        <strong>Prijavljeno:</strong>

                                        <?= $brojPrijavljenih ?>
                                        /
                                        <?= $kapacitet ?>
                                    </p>

                                    <p class="mb-2">
                                        <strong>Slobodna mesta:</strong>

                                        <?php if ($slobodnaMesta > 0): ?>

                                            <span class="text-success fw-bold">
                                                <?= $slobodnaMesta ?>
                                            </span>

                                        <?php else: ?>

                                            <span class="text-danger fw-bold">
                                                Nema slobodnih mesta
                                            </span>

                                        <?php endif; ?>
                                    </p>

                                    <p class="mb-3">
                                        <strong>Opis treninga:</strong><br>

                                        <?= nl2br(
                                            htmlspecialchars(
                                                $termin['opis']
                                                ?? 'Opis nije unet.'
                                            )
                                        ) ?>
                                    </p>

                                    <div class="d-flex flex-wrap gap-2">

                                        <?php if ($rezervacijaDostupna): ?>

                                            <a
                                                href="rezervisi_termin.php?id=<?= (int) $termin['id'] ?>"
                                                class="btn btn-success btn-sm"
                                            >
                                                Rezerviši
                                            </a>

                                        <?php elseif ($slobodnaMesta <= 0): ?>

                                            <button
                                                type="button"
                                                class="btn btn-secondary btn-sm"
                                                disabled
                                            >
                                                Termin je popunjen
                                            </button>

                                        <?php else: ?>

                                            <button
                                                type="button"
                                                class="btn btn-secondary btn-sm"
                                                disabled
                                            >
                                                Rezervacija zatvorena
                                            </button>

                                        <?php endif; ?>

                                        <a
                                            href="izmeni_termin.php?id=<?= (int) $termin['id'] ?>"
                                            class="btn btn-warning btn-sm"
                                        >
                                            Izmeni
                                        </a>

                                        <a
                                            href="obrisi_termin.php?id=<?= (int) $termin['id'] ?>"
                                            class="btn btn-danger btn-sm"
                                            onclick="return confirm('Da li sigurno želite da obrišete termin?')"
                                        >
                                            Obriši
                                        </a>

                                    </div>

                                </div>

                            </div>

                        </div>

                    <?php endforeach; ?>

                </div>

            <?php endif; ?>

        </div>

    </div>

    <div class="card shadow-sm">

        <div class="card-header">

            <div class="d-flex justify-content-between align-items-center">

                <a
                    href="termini.php?godina=<?= $prethodnaGodina ?>&mesec=<?= $prethodniMesec ?>"
                    class="btn btn-outline-primary btn-sm"
                >
                    Prethodni mesec
                </a>

                <h2 class="h4 mb-0">
                    <?= $naziviMeseci[$mesec] ?>
                    <?= $godina ?>
                </h2>

                <a
                    href="termini.php?godina=<?= $sledecaGodina ?>&mesec=<?= $sledeciMesec ?>"
                    class="btn btn-outline-primary btn-sm"
                >
                    Sledeći mesec
                </a>

            </div>

        </div>

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-bordered calendar-table text-center">

                    <thead class="table-dark">

                    <tr>
                        <th>Ponedeljak</th>
                        <th>Utorak</th>
                        <th>Sreda</th>
                        <th>Četvrtak</th>
                        <th>Petak</th>
                        <th>Subota</th>
                        <th>Nedelja</th>
                    </tr>

                    </thead>

                    <tbody>

                    <tr>

                        <?php
                        for (
                            $praznoPolje = 1;
                            $praznoPolje < $danUNedeljiPrvogDana;
                            $praznoPolje++
                        ):
                        ?>

                            <td class="bg-light"></td>

                        <?php endfor; ?>

                        <?php
                        $pozicijaUNedelji = $danUNedeljiPrvogDana;

                        for (
                            $dan = 1;
                            $dan <= $brojDanaUMesecu;
                            $dan++
                        ):
                            $datumDana = sprintf(
                                '%04d-%02d-%02d',
                                $godina,
                                $mesec,
                                $dan
                            );

                            $imaTermin = in_array(
                                $datumDana,
                                $datumiSaTerminima,
                                true
                            );

                            $izabran = $datumDana === $izabraniDatum;
                        ?>

                            <td class="p-1">

                                <a
                                    href="termini.php?godina=<?= $godina ?>&mesec=<?= $mesec ?>&datum=<?= $datumDana ?>"
                                    class="calendar-day
                                        <?= $imaTermin ? 'day-with-event' : '' ?>
                                        <?= $izabran ? 'selected-day' : '' ?>"
                                >
                                    <?= $dan ?>

                                    <?php if ($imaTermin): ?>

                                        <span
                                            class="event-dot"
                                            title="Postoji zakazan termin"
                                        ></span>

                                        <div class="small text-success mt-2">
                                            Termin
                                        </div>

                                    <?php endif; ?>

                                </a>

                            </td>

                            <?php if (
                                $pozicijaUNedelji % 7 === 0
                                && $dan !== $brojDanaUMesecu
                            ): ?>

                                </tr>
                                <tr>

                            <?php endif; ?>

                            <?php $pozicijaUNedelji++; ?>

                        <?php endfor; ?>

                        <?php
                        while (($pozicijaUNedelji - 1) % 7 !== 0):
                        ?>

                            <td class="bg-light"></td>

                            <?php $pozicijaUNedelji++; ?>

                        <?php endwhile; ?>

                    </tr>

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>

</body>
</html>