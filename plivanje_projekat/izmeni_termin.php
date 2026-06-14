```php
<?php

date_default_timezone_set('Europe/Belgrade');

require_once __DIR__ . '/classes/Session.php';
require_once __DIR__ . '/classes/Termin.php';
require_once __DIR__ . '/classes/Instruktor.php';


function proveriDozvoljenoVremeTreninga(
    string $bazen,
    string $datum,
    string $vreme,
    int $trajanjeMinuta
): string {
    $pocetak = DateTime::createFromFormat(
        'Y-m-d H:i',
        $datum . ' ' . $vreme
    );

    if (!$pocetak) {
        return 'Datum ili vreme termina nisu ispravni.';
    }

    $kraj = clone $pocetak;
    $kraj->modify('+' . $trajanjeMinuta . ' minutes');

    if ($kraj->format('Y-m-d') !== $datum) {
        return 'Termin ne može da prelazi u sledeći dan.';
    }

    $danUNedelji = (int) $pocetak->format('N');

    $minutiPocetka =
        ((int) $pocetak->format('H') * 60)
        + (int) $pocetak->format('i');

    $minutiKraja =
        ((int) $kraj->format('H') * 60)
        + (int) $kraj->format('i');

    $nazivBazena = mb_strtolower(
        trim($bazen),
        'UTF-8'
    );

    if (str_contains($nazivBazena, 'otvoreni')) {
        if ($danUNedelji >= 1 && $danUNedelji <= 6) {
            $dozvoljenPocetak = 19 * 60;
            $dozvoljenKraj = 21 * 60;
            $opisPerioda = '19:00–21:00';
        } else {
            $dozvoljenPocetak = 16 * 60;
            $dozvoljenKraj = 20 * 60;
            $opisPerioda = '16:00–20:00';
        }
    } elseif (str_contains($nazivBazena, 'zatvoreni')) {
        if ($danUNedelji === 7) {
            return 'Na zatvorenom bazenu nedeljom nema termina za trening.';
        }

        $dozvoljenPocetak = 18 * 60;
        $dozvoljenKraj = 22 * 60;
        $opisPerioda = '18:00–22:00';
    } else {
        return 'Naziv bazena mora biti „Otvoreni bazen“ ili „Zatvoreni bazen“.';
    }

    if (
        $minutiPocetka < $dozvoljenPocetak
        || $minutiKraja > $dozvoljenKraj
    ) {
        return 'Za izabrani bazen i dan trening mora u celosti biti u periodu '
            . $opisPerioda . '.';
    }

    return '';
}


Session::requireLogin();

$danas = date('Y-m-d');

$terminModel = new Termin();
$instruktorModel = new Instruktor();

$greska = '';

$id = filter_input(
    INPUT_GET,
    'id',
    FILTER_VALIDATE_INT
);

if (!$id) {
    die('Neispravan ID termina.');
}

$termin = $terminModel->findById($id);

if (!$termin) {
    die('Termin nije pronađen.');
}

$instruktori = $instruktorModel->read();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $instruktorId = filter_input(
        INPUT_POST,
        'instruktor_id',
        FILTER_VALIDATE_INT
    );

    $datum = trim($_POST['datum'] ?? '');
    $vreme = trim($_POST['vreme'] ?? '');

    $trajanjeMinuta = filter_input(
        INPUT_POST,
        'trajanje_minuta',
        FILTER_VALIDATE_INT
    );

    $bazen = trim($_POST['bazen'] ?? '');
    $tipTreninga = trim($_POST['tip_treninga'] ?? '');
    $opis = trim($_POST['opis'] ?? '');

    $kapacitet = filter_input(
        INPUT_POST,
        'kapacitet',
        FILTER_VALIDATE_INT
    );

    $rezervacijaDostupna = isset(
        $_POST['rezervacija_dostupna']
    ) ? 1 : 0;

    $dozvoljeniTipovi = [
        'Rekreativni',
        'Takmičarski',
        'Individualni'
    ];

    $brojPrijavljenih = (int) (
        $termin['broj_prijavljenih'] ?? 0
    );

    $datumObjekat = DateTime::createFromFormat(
        'Y-m-d',
        $datum
    );

    if (!$instruktorId) {
        $greska = 'Izaberite instruktora.';
    } elseif (
        !$datumObjekat
        || $datumObjekat->format('Y-m-d') !== $datum
    ) {
        $greska = 'Datum termina nije ispravan.';
    } elseif ($datum < $danas) {
        $greska = 'Termin nije moguće premestiti na datum koji je prošao.';
    } elseif ($vreme === '') {
        $greska = 'Vreme početka je obavezno.';
    } elseif (
        !$trajanjeMinuta
        || $trajanjeMinuta < 15
    ) {
        $greska = 'Trajanje termina mora biti najmanje 15 minuta.';
    } elseif ($bazen === '') {
        $greska = 'Naziv bazena je obavezan.';
    } elseif (
        !in_array(
            $tipTreninga,
            $dozvoljeniTipovi,
            true
        )
    ) {
        $greska = 'Izabrani tip treninga nije ispravan.';
    } elseif (
        !$kapacitet
        || $kapacitet < 1
    ) {
        $greska = 'Kapacitet mora biti najmanje 1.';
    } elseif ($kapacitet < $brojPrijavljenih) {
        $greska =
            'Kapacitet ne može biti manji od trenutnog broja prijavljenih polaznika.';
    } else {
        $greskaVremena = proveriDozvoljenoVremeTreninga(
            $bazen,
            $datum,
            $vreme,
            $trajanjeMinuta
        );

        if ($greskaVremena !== '') {
            $greska = $greskaVremena;
        } else {
            $uspeh = $terminModel->update($id, [
            'instruktor_id' => $instruktorId,
            'datum' => $datum,
            'vreme' => $vreme,
            'trajanje_minuta' => $trajanjeMinuta,
            'bazen' => $bazen,
            'tip_treninga' => $tipTreninga,
            'opis' => $opis !== ''
                ? $opis
                : null,
            'kapacitet' => $kapacitet,
            'rezervacija_dostupna' =>
                $rezervacijaDostupna
        ]);

        if ($uspeh) {
            header(
                'Location: termini.php?godina='
                . date('Y', strtotime($datum))
                . '&mesec='
                . date('n', strtotime($datum))
                . '&datum='
                . urlencode($datum)
                . '&izmenjeno=1'
            );

            exit;
        }

            $greska =
                'Došlo je do greške prilikom izmene termina.';
        }
    }
}

$izabraniInstruktor =
    $_POST['instruktor_id']
    ?? $termin['instruktor_id'];

$izabraniTip =
    $_POST['tip_treninga']
    ?? $termin['tip_treninga'];

$checkboxOznacen =
    $_SERVER['REQUEST_METHOD'] === 'POST'
        ? isset($_POST['rezervacija_dostupna'])
        : (int) $termin['rezervacija_dostupna'] === 1;
?>

<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Izmena termina</title>

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

                <div class="card-header bg-warning">

                    <h1 class="h4 mb-0">
                        Izmeni termin
                    </h1>

                </div>

                <div class="card-body">

                    <?php if ($greska !== ''): ?>

                        <div class="alert alert-danger">
                            <?= htmlspecialchars($greska) ?>
                        </div>

                    <?php endif; ?>

                    <div class="alert alert-info">

                        Trenutno prijavljeno:

                        <strong>
                            <?= (int) (
                                $termin['broj_prijavljenih']
                                ?? 0
                            ) ?>
                        </strong>

                        od

                        <strong>
                            <?= (int) $termin['kapacitet'] ?>
                        </strong>

                        mesta.

                    </div>

                    <div class="alert alert-info">
                        <strong>Dozvoljeni termini treninga:</strong><br>
                        Otvoreni bazen — ponedeljak–subota 19:00–21:00,
                        nedelja 16:00–20:00.<br>
                        Zatvoreni bazen — ponedeljak–subota 18:00–22:00,
                        nedeljom nema treninga.
                    </div>

                    <form method="POST">

                        <div class="mb-3">

                            <label
                                for="instruktor_id"
                                class="form-label"
                            >
                                Instruktor
                            </label>

                            <select
                                id="instruktor_id"
                                name="instruktor_id"
                                class="form-select"
                                required
                            >
                                <option value="">
                                    Izaberite instruktora
                                </option>

                                <?php foreach (
                                    $instruktori as $instruktor
                                ): ?>

                                    <option
                                        value="<?= (int) $instruktor['id'] ?>"
                                        <?= $izabraniInstruktor == $instruktor['id']
                                            ? 'selected'
                                            : '' ?>
                                    >
                                        <?= htmlspecialchars(
                                            $instruktor['ime']
                                            . ' '
                                            . $instruktor['prezime']
                                        ) ?>
                                    </option>

                                <?php endforeach; ?>

                            </select>

                        </div>

                        <div class="row">

                            <div class="col-md-6 mb-3">

                                <label
                                    for="datum"
                                    class="form-label"
                                >
                                    Datum
                                </label>

                                <input
                                    type="date"
                                    id="datum"
                                    name="datum"
                                    class="form-control"
                                    min="<?= htmlspecialchars($danas) ?>"
                                    value="<?= htmlspecialchars(
                                        $_POST['datum']
                                        ?? $termin['datum']
                                    ) ?>"
                                    required
                                >

                            </div>

                            <div class="col-md-6 mb-3">

                                <label
                                    for="vreme"
                                    class="form-label"
                                >
                                    Vreme početka
                                </label>

                                <input
                                    type="time"
                                    id="vreme"
                                    name="vreme"
                                    class="form-control"
                                    value="<?= htmlspecialchars(
                                        $_POST['vreme']
                                        ?? substr(
                                            $termin['vreme'],
                                            0,
                                            5
                                        )
                                    ) ?>"
                                    required
                                >

                            </div>

                        </div>

                        <div class="row">

                            <div class="col-md-6 mb-3">

                                <label
                                    for="trajanje_minuta"
                                    class="form-label"
                                >
                                    Trajanje u minutima
                                </label>

                                <input
                                    type="number"
                                    id="trajanje_minuta"
                                    name="trajanje_minuta"
                                    class="form-control"
                                    min="15"
                                    value="<?= htmlspecialchars(
                                        $_POST['trajanje_minuta']
                                        ?? $termin['trajanje_minuta']
                                    ) ?>"
                                    required
                                >

                            </div>

                            <div class="col-md-6 mb-3">

                                <label
                                    for="kapacitet"
                                    class="form-label"
                                >
                                    Kapacitet
                                </label>

                                <input
                                    type="number"
                                    id="kapacitet"
                                    name="kapacitet"
                                    class="form-control"
                                    min="<?= max(
                                        1,
                                        (int) (
                                            $termin['broj_prijavljenih']
                                            ?? 0
                                        )
                                    ) ?>"
                                    value="<?= htmlspecialchars(
                                        $_POST['kapacitet']
                                        ?? $termin['kapacitet']
                                    ) ?>"
                                    required
                                >

                            </div>

                        </div>

                        <div class="mb-3">

                            <label
                                for="bazen"
                                class="form-label"
                            >
                                Bazen
                            </label>

                            <input
                                type="text"
                                id="bazen"
                                name="bazen"
                                class="form-control"
                                list="bazeni"
                                value="<?= htmlspecialchars(
                                    $_POST['bazen']
                                    ?? $termin['bazen']
                                    ?? ''
                                ) ?>"
                                required
                            >

                            <datalist id="bazeni">
                                <option value="Otvoreni bazen">
                                <option value="Zatvoreni bazen">
                            </datalist>

                            <div class="form-text">
                                Unesite tačno „Otvoreni bazen“ ili „Zatvoreni bazen“.
                            </div>

                        </div>

                        <div class="mb-3">

                            <label
                                for="tip_treninga"
                                class="form-label"
                            >
                                Tip treninga
                            </label>

                            <select
                                id="tip_treninga"
                                name="tip_treninga"
                                class="form-select"
                                required
                            >
                                <?php
                                $tipovi = [
                                    'Rekreativni',
                                    'Takmičarski',
                                    'Individualni'
                                ];
                                ?>

                                <?php foreach ($tipovi as $tip): ?>

                                    <option
                                        value="<?= htmlspecialchars($tip) ?>"
                                        <?= $izabraniTip === $tip
                                            ? 'selected'
                                            : '' ?>
                                    >
                                        <?= htmlspecialchars($tip) ?>
                                    </option>

                                <?php endforeach; ?>

                            </select>

                        </div>

                        <div class="mb-3">

                            <label
                                for="opis"
                                class="form-label"
                            >
                                Opis treninga
                            </label>

                            <textarea
                                id="opis"
                                name="opis"
                                class="form-control"
                                rows="4"
                            ><?= htmlspecialchars(
                                $_POST['opis']
                                ?? $termin['opis']
                                ?? ''
                            ) ?></textarea>

                        </div>

                        <div class="form-check mb-4">

                            <input
                                type="checkbox"
                                id="rezervacija_dostupna"
                                name="rezervacija_dostupna"
                                class="form-check-input"
                                value="1"
                                <?= $checkboxOznacen
                                    ? 'checked'
                                    : '' ?>
                            >

                            <label
                                for="rezervacija_dostupna"
                                class="form-check-label"
                            >
                                Rezervacija termina je dostupna
                            </label>

                        </div>

                        <div class="d-flex gap-2">

                            <button
                                type="submit"
                                class="btn btn-warning"
                            >
                                Sačuvaj izmene
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

                </div>

            </div>

        </div>

    </div>

</div>

</body>
</html>
```
