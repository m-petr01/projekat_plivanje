<?php

date_default_timezone_set('Europe/Belgrade');

require_once __DIR__ . '/classes/Session.php';
require_once __DIR__ . '/classes/Termin.php';
require_once __DIR__ . '/classes/Instruktor.php';

Session::requireLogin();

$danas = date('Y-m-d');

$datumIzKalendara = $_GET['datum'] ?? $danas;

$validanDatumIzKalendara = DateTime::createFromFormat(
    'Y-m-d',
    $datumIzKalendara
);

if (
    !$validanDatumIzKalendara
    || $validanDatumIzKalendara->format('Y-m-d') !== $datumIzKalendara
    || $datumIzKalendara < $danas
) {
    $datumIzKalendara = $danas;
}

$terminModel = new Termin();
$instruktorModel = new Instruktor();

$instruktori = $instruktorModel->read();

$greska = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $instruktorId = filter_input(
        INPUT_POST,
        'instruktor_id',
        FILTER_VALIDATE_INT
    );

    $datum = $_POST['datum'] ?? '';
    $vreme = $_POST['vreme'] ?? '';

    $trajanjeMinuta = filter_input(
        INPUT_POST,
        'trajanje_minuta',
        FILTER_VALIDATE_INT
    );

    $bazen = trim($_POST['bazen'] ?? '');
    $tipTreninga = $_POST['tip_treninga'] ?? '';
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

    $datumObjekat = DateTime::createFromFormat(
        'Y-m-d',
        $datum
    );

    if (
        !$instruktorId
        || !$datumObjekat
        || $datumObjekat->format('Y-m-d') !== $datum
        || $vreme === ''
        || !$trajanjeMinuta
        || $trajanjeMinuta < 15
        || $bazen === ''
        || !in_array($tipTreninga, $dozvoljeniTipovi, true)
        || !$kapacitet
        || $kapacitet < 1
    ) {
        $greska = 'Popunite pravilno sva obavezna polja.';
    } elseif ($datum < $danas) {
        $greska = 'Nije moguće dodati termin za datum koji je prošao.';
    } else {
        $uspeh = $terminModel->create([
            'instruktor_id' => $instruktorId,
            'datum' => $datum,
            'vreme' => $vreme,
            'trajanje_minuta' => $trajanjeMinuta,
            'bazen' => $bazen,
            'tip_treninga' => $tipTreninga,
            'opis' => $opis !== '' ? $opis : null,
            'kapacitet' => $kapacitet,
            'rezervacija_dostupna' => $rezervacijaDostupna
        ]);

        if ($uspeh) {
            header(
                'Location: termini.php?godina='
                . date('Y', strtotime($datum))
                . '&mesec='
                . date('n', strtotime($datum))
                . '&datum='
                . urlencode($datum)
            );
            exit;
        }

        $greska = 'Termin nije uspešno dodat.';
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

    <title>Dodavanje termina</title>

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
                        Dodaj termin
                    </h1>
                </div>

                <div class="card-body">

                    <?php if ($greska !== ''): ?>

                        <div class="alert alert-danger">
                            <?= htmlspecialchars($greska) ?>
                        </div>

                    <?php endif; ?>

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

                                <?php foreach ($instruktori as $instruktor): ?>

                                    <option
                                        value="<?= (int) $instruktor['id'] ?>"
                                        <?= ($_POST['instruktor_id'] ?? '') == $instruktor['id']
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
                                        $_POST['datum'] ?? $datumIzKalendara
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
                                        $_POST['vreme'] ?? ''
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
                                        $_POST['trajanje_minuta'] ?? '60'
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
                                    min="1"
                                    value="<?= htmlspecialchars(
                                        $_POST['kapacitet'] ?? '10'
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
                                placeholder="Na primer: Veliki bazen"
                                value="<?= htmlspecialchars(
                                    $_POST['bazen'] ?? ''
                                ) ?>"
                                required
                            >
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
                                <option
                                    value="Rekreativni"
                                    <?= ($_POST['tip_treninga'] ?? '') === 'Rekreativni'
                                        ? 'selected'
                                        : '' ?>
                                >
                                    Rekreativni
                                </option>

                                <option
                                    value="Takmičarski"
                                    <?= ($_POST['tip_treninga'] ?? '') === 'Takmičarski'
                                        ? 'selected'
                                        : '' ?>
                                >
                                    Takmičarski
                                </option>

                                <option
                                    value="Individualni"
                                    <?= ($_POST['tip_treninga'] ?? '') === 'Individualni'
                                        ? 'selected'
                                        : '' ?>
                                >
                                    Individualni privatni
                                </option>
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
                                placeholder="Šta se konkretno trenira?"
                            ><?= htmlspecialchars($_POST['opis'] ?? '') ?></textarea>
                        </div>

                        <div class="form-check mb-4">

                            <input
                                type="checkbox"
                                class="form-check-input"
                                id="rezervacija_dostupna"
                                name="rezervacija_dostupna"
                                value="1"
                                <?= !isset($_POST['rezervacija_dostupna'])
                                    && $_SERVER['REQUEST_METHOD'] !== 'POST'
                                    ? 'checked'
                                    : (isset($_POST['rezervacija_dostupna'])
                                        ? 'checked'
                                        : '') ?>
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
                                class="btn btn-success"
                            >
                                Sačuvaj termin
                            </button>

                            <a
                                href="termini.php?godina=<?= date(
                                    'Y',
                                    strtotime($_POST['datum'] ?? $datumIzKalendara)
                                ) ?>&mesec=<?= date(
                                    'n',
                                    strtotime($_POST['datum'] ?? $datumIzKalendara)
                                ) ?>&datum=<?= urlencode(
                                    $_POST['datum'] ?? $datumIzKalendara
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
