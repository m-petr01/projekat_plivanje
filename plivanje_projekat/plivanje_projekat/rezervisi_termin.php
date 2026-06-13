```php
<?php

date_default_timezone_set('Europe/Belgrade');

require_once __DIR__ . '/classes/Session.php';
require_once __DIR__ . '/classes/Termin.php';
require_once __DIR__ . '/classes/Polaznik.php';
require_once __DIR__ . '/classes/Rezervacija.php';

Session::requireLogin();

$danas = date('Y-m-d');

$terminModel = new Termin();
$polaznikModel = new Polaznik();
$rezervacijaModel = new Rezervacija();

$greska = '';

$terminId = filter_input(
    INPUT_GET,
    'id',
    FILTER_VALIDATE_INT
);

if (!$terminId) {
    die('Neispravan ID termina.');
}

$termin = $terminModel->findById($terminId);

if (!$termin) {
    die('Termin nije pronađen.');
}

if ($termin['datum'] < $danas) {
    die('Nije moguće rezervisati termin koji je već prošao.');
}

$polaznici = $polaznikModel->read();

$brojPrijavljenih =
    $rezervacijaModel->brojAktivnihRezervacija(
        $terminId
    );

$kapacitet = (int) $termin['kapacitet'];

$slobodnaMesta = max(
    0,
    $kapacitet - $brojPrijavljenih
);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $polaznikId = filter_input(
        INPUT_POST,
        'polaznik_id',
        FILTER_VALIDATE_INT
    );

    if ($termin['datum'] < $danas) {
        $greska = 'Nije moguće rezervisati termin koji je već prošao.';
    } elseif ((int) $termin['rezervacija_dostupna'] !== 1) {
        $greska = 'Rezervacija ovog termina nije dostupna.';
    } elseif ($slobodnaMesta <= 0) {
        $greska = 'Termin je popunjen.';
    } elseif (!$polaznikId) {
        $greska = 'Izaberite polaznika.';
    } else {
        $izabraniPolaznik =
            $polaznikModel->findById($polaznikId);

        if (!$izabraniPolaznik) {
            $greska = 'Izabrani polaznik ne postoji.';
        } else {
            $postojecaRezervacija =
                $rezervacijaModel->findByTerminAndPolaznik(
                    $terminId,
                    $polaznikId
                );

            if (
                $postojecaRezervacija
                && $postojecaRezervacija['status'] === 'rezervisano'
            ) {
                $greska =
                    'Ovaj polaznik je već rezervisao termin.';
            } else {
                $uspeh = $rezervacijaModel->create(
                    $terminId,
                    $polaznikId
                );

                if ($uspeh) {
                    header(
                        'Location: termini.php?godina='
                        . date(
                            'Y',
                            strtotime($termin['datum'])
                        )
                        . '&mesec='
                        . date(
                            'n',
                            strtotime($termin['datum'])
                        )
                        . '&datum='
                        . urlencode($termin['datum'])
                        . '&rezervisano=1'
                    );

                    exit;
                }

                $greska =
                    'Došlo je do greške prilikom rezervacije.';
            }
        }
    }
}

$vremePocetka = new DateTime(
    $termin['vreme']
);

$vremeZavrsetka = clone $vremePocetka;

$vremeZavrsetka->modify(
    '+' . (int) $termin['trajanje_minuta'] . ' minutes'
);
?>

<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Rezervacija termina</title>

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
                        Rezerviši termin
                    </h1>

                </div>

                <div class="card-body">

                    <?php if ($greska !== ''): ?>

                        <div class="alert alert-danger">
                            <?= htmlspecialchars($greska) ?>
                        </div>

                    <?php endif; ?>

                    <div class="card bg-light border-0 mb-4">

                        <div class="card-body">

                            <h2 class="h5">
                                <?= htmlspecialchars(
                                    $termin['tip_treninga']
                                ) ?>
                            </h2>

                            <p class="mb-2">
                                <strong>Datum:</strong>

                                <?= htmlspecialchars(
                                    date(
                                        'd.m.Y.',
                                        strtotime($termin['datum'])
                                    )
                                ) ?>
                            </p>

                            <p class="mb-2">
                                <strong>Vreme:</strong>

                                <?= $vremePocetka->format('H:i') ?>
                                –
                                <?= $vremeZavrsetka->format('H:i') ?>
                            </p>

                            <p class="mb-2">
                                <strong>Instruktor:</strong>

                                <?= htmlspecialchars(
                                    $termin['instruktor_ime']
                                ) ?>
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

                            <p class="mb-0">
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

                        </div>

                    </div>

                    <?php if (
                        (int) $termin['rezervacija_dostupna'] === 1
                        && $slobodnaMesta > 0
                    ): ?>

                        <?php if (empty($polaznici)): ?>

                            <div class="alert alert-warning">
                                Nema evidentiranih polaznika koje je
                                moguće prijaviti na termin.
                            </div>

                            <a
                                href="termini.php?datum=<?= urlencode(
                                    $termin['datum']
                                ) ?>"
                                class="btn btn-secondary"
                            >
                                Nazad na termine
                            </a>

                        <?php else: ?>

                            <form method="POST">

                                <div class="mb-4">

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

                                        <?php foreach (
                                            $polaznici as $polaznik
                                        ): ?>

                                            <option
                                                value="<?= (int) $polaznik['id'] ?>"
                                                <?= (
                                                    $_POST['polaznik_id']
                                                    ?? ''
                                                ) == $polaznik['id']
                                                    ? 'selected'
                                                    : '' ?>
                                            >
                                                <?= htmlspecialchars(
                                                    $polaznik['ime']
                                                    . ' '
                                                    . $polaznik['prezime']
                                                ) ?>
                                            </option>

                                        <?php endforeach; ?>

                                    </select>

                                </div>

                                <div class="d-flex gap-2">

                                    <button
                                        type="submit"
                                        class="btn btn-success"
                                    >
                                        Potvrdi rezervaciju
                                    </button>

                                    <a
                                        href="termini.php?godina=<?= date(
                                            'Y',
                                            strtotime($termin['datum'])
                                        ) ?>&mesec=<?= date(
                                            'n',
                                            strtotime($termin['datum'])
                                        ) ?>&datum=<?= urlencode(
                                            $termin['datum']
                                        ) ?>"
                                        class="btn btn-secondary"
                                    >
                                        Otkaži
                                    </a>

                                </div>

                            </form>

                        <?php endif; ?>

                    <?php else: ?>

                        <div class="alert alert-warning">
                            Ovaj termin trenutno nije moguće rezervisati.
                        </div>

                        <a
                            href="termini.php?godina=<?= date(
                                'Y',
                                strtotime($termin['datum'])
                            ) ?>&mesec=<?= date(
                                'n',
                                strtotime($termin['datum'])
                            ) ?>&datum=<?= urlencode(
                                $termin['datum']
                            ) ?>"
                            class="btn btn-secondary"
                        >
                            Nazad na termine
                        </a>

                    <?php endif; ?>

                </div>

            </div>

        </div>

    </div>

</div>

</body>
</html>

