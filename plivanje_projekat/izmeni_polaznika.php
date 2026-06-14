<?php

require_once __DIR__ . '/classes/Polaznik.php';
require_once __DIR__ . '/classes/Nivo.php';
require_once __DIR__ . '/classes/Nagrada.php';

$polaznikModel = new Polaznik();
$nivoModel = new Nivo();
$nagradaModel = new Nagrada();

$nivoi = $nivoModel->read();

$greska = '';

$id = filter_input(
    INPUT_GET,
    'id',
    FILTER_VALIDATE_INT
);

if (!$id) {
    die('Neispravan ID polaznika.');
}

$polaznik = $polaznikModel->findById($id);

if (!$polaznik) {
    die('Polaznik nije pronađen.');
}

$postojecaNagrada = $nagradaModel->findFirstByPolaznik($id);

$dozvoljeniNivoiZaNagrade = [];

foreach ($nivoi as $nivo) {
    $nazivNivoa = mb_strtolower(
        trim($nivo['naziv']),
        'UTF-8'
    );

    if (in_array(
        $nazivNivoa,
        ['napredni', 'takmičarski'],
        true
    )) {
        $dozvoljeniNivoiZaNagrade[] = (int) $nivo['id'];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ime = trim($_POST['ime'] ?? '');
    $prezime = trim($_POST['prezime'] ?? '');
    $datumRodjenja = $_POST['datum_rodjenja'] ?? '';
    $telefon = trim($_POST['telefon'] ?? '');
    $email = trim($_POST['email'] ?? '');

    $nivoId = filter_input(
        INPUT_POST,
        'nivo_id',
        FILTER_VALIDATE_INT
    );

    $imaNagradu = isset($_POST['ima_nagradu']);
    $nazivNagrade = trim($_POST['naziv_nagrade'] ?? '');
    $opisNagrade = trim($_POST['opis_nagrade'] ?? '');
    $datumNagrade = $_POST['datum_nagrade'] ?? '';

    $nivoPostoji = false;

    foreach ($nivoi as $nivo) {
        if ((int) $nivo['id'] === (int) $nivoId) {
            $nivoPostoji = true;
            break;
        }
    }

    $nivoDozvoljavaNagradu = in_array(
        (int) $nivoId,
        $dozvoljeniNivoiZaNagrade,
        true
    );

    if ($ime === '' || $prezime === '' || !$nivoId) {
        $greska = 'Ime, prezime i nivo znanja su obavezni.';
    } elseif (!$nivoPostoji) {
        $greska = 'Izabrani nivo znanja ne postoji.';
    } elseif (
        $email !== ''
        && !filter_var($email, FILTER_VALIDATE_EMAIL)
    ) {
        $greska = 'Email adresa nije ispravna.';
    } elseif ($imaNagradu && !$nivoDozvoljavaNagradu) {
        $greska =
            'Nagrada se može evidentirati samo za napredni '
            . 'ili takmičarski nivo.';
    } elseif ($imaNagradu && $nazivNagrade === '') {
        $greska = 'Unesite naziv nagrade.';
    } else {
        $uspeh = $polaznikModel->update($id, [
            'ime' => $ime,
            'prezime' => $prezime,
            'datum_rodjenja' =>
                $datumRodjenja !== '' ? $datumRodjenja : null,
            'telefon' => $telefon !== '' ? $telefon : null,
            'email' => $email !== '' ? $email : null,
            'nivo_id' => (int) $nivoId
        ]);

        if (!$uspeh) {
            $greska =
                'Došlo je do greške prilikom izmene polaznika.';
        } else {
            if ($imaNagradu && $nivoDozvoljavaNagradu) {
                $podaciNagrade = [
                    'naziv' => $nazivNagrade,
                    'opis' => $opisNagrade !== ''
                        ? $opisNagrade
                        : null,
                    'datum' => $datumNagrade !== ''
                        ? $datumNagrade
                        : null
                ];

                if ($postojecaNagrada) {
                    $nagradaUspeh = $nagradaModel->update(
                        (int) $postojecaNagrada['id'],
                        $podaciNagrade
                    );
                } else {
                    $podaciNagrade['polaznik_id'] = $id;

                    $nagradaUspeh = $nagradaModel->create(
                        $podaciNagrade
                    );
                }

                if (!$nagradaUspeh) {
                    $greska =
                        'Podaci polaznika su izmenjeni, ali nagrada '
                        . 'nije uspešno sačuvana.';
                } else {
                    header('Location: polaznici.php?izmenjeno=1');
                    exit;
                }
            } else {
                if ($postojecaNagrada) {
                    $nagradaModel->deleteByPolaznik($id);
                }

                header('Location: polaznici.php?izmenjeno=1');
                exit;
            }
        }
    }
}

$izabraniNivo = $_POST['nivo_id']
    ?? $polaznik['nivo_id'];

$imaNagraduOznaceno =
    $_SERVER['REQUEST_METHOD'] === 'POST'
        ? isset($_POST['ima_nagradu'])
        : (bool) $postojecaNagrada;
?>

<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Izmena polaznika</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <style>
        .award-section {
            border-left: 5px solid #ffc107;
        }
    </style>
</head>

<body class="bg-light">

<div class="container py-5">

    <div class="row justify-content-center">

        <div class="col-md-9 col-lg-7">

            <div class="card shadow-sm">

                <div class="card-header bg-warning">

                    <h1 class="h4 mb-0">
                        Izmeni polaznika
                    </h1>

                </div>

                <div class="card-body">

                    <?php if ($greska !== ''): ?>

                        <div class="alert alert-danger">
                            <?= htmlspecialchars($greska) ?>
                        </div>

                    <?php endif; ?>

                    <form method="POST">

                        <div class="row">

                            <div class="col-md-6 mb-3">

                                <label for="ime" class="form-label">
                                    Ime
                                </label>

                                <input
                                    type="text"
                                    class="form-control"
                                    id="ime"
                                    name="ime"
                                    value="<?= htmlspecialchars(
                                        $_POST['ime'] ?? $polaznik['ime']
                                    ) ?>"
                                    required
                                >

                            </div>

                            <div class="col-md-6 mb-3">

                                <label for="prezime" class="form-label">
                                    Prezime
                                </label>

                                <input
                                    type="text"
                                    class="form-control"
                                    id="prezime"
                                    name="prezime"
                                    value="<?= htmlspecialchars(
                                        $_POST['prezime']
                                        ?? $polaznik['prezime']
                                    ) ?>"
                                    required
                                >

                            </div>

                        </div>

                        <div class="mb-3">

                            <label
                                for="datum_rodjenja"
                                class="form-label"
                            >
                                Datum rođenja
                            </label>

                            <input
                                type="date"
                                class="form-control"
                                id="datum_rodjenja"
                                name="datum_rodjenja"
                                value="<?= htmlspecialchars(
                                    $_POST['datum_rodjenja']
                                    ?? $polaznik['datum_rodjenja']
                                    ?? ''
                                ) ?>"
                            >

                        </div>

                        <div class="mb-3">

                            <label for="telefon" class="form-label">
                                Telefon
                            </label>

                            <input
                                type="text"
                                class="form-control"
                                id="telefon"
                                name="telefon"
                                value="<?= htmlspecialchars(
                                    $_POST['telefon']
                                    ?? $polaznik['telefon']
                                    ?? ''
                                ) ?>"
                            >

                        </div>

                        <div class="mb-3">

                            <label for="email" class="form-label">
                                Email
                            </label>

                            <input
                                type="email"
                                class="form-control"
                                id="email"
                                name="email"
                                value="<?= htmlspecialchars(
                                    $_POST['email']
                                    ?? $polaznik['email']
                                    ?? ''
                                ) ?>"
                            >

                        </div>

                        <div class="mb-4">

                            <label for="nivo_id" class="form-label">
                                Nivo znanja
                            </label>

                            <select
                                class="form-select"
                                id="nivo_id"
                                name="nivo_id"
                                required
                            >
                                <option value="">
                                    Izaberi nivo
                                </option>

                                <?php foreach ($nivoi as $nivo): ?>

                                    <option
                                        value="<?= (int) $nivo['id'] ?>"
                                        data-naziv="<?= htmlspecialchars(
                                            mb_strtolower(
                                                trim($nivo['naziv']),
                                                'UTF-8'
                                            )
                                        ) ?>"
                                        <?= $izabraniNivo == $nivo['id']
                                            ? 'selected'
                                            : '' ?>
                                    >
                                        <?= htmlspecialchars($nivo['naziv']) ?>
                                    </option>

                                <?php endforeach; ?>

                            </select>

                        </div>

                        <div
                            id="nagrada-kartica"
                            class="card bg-light mb-4 award-section"
                        >

                            <div class="card-body">

                                <h2 class="h5">
                                    Nagrade i rezultati
                                </h2>

                                <p class="text-muted">
                                    Nagrada se može evidentirati samo za
                                    napredni ili takmičarski nivo.
                                </p>

                                <div class="form-check mb-3">

                                    <input
                                        type="checkbox"
                                        class="form-check-input"
                                        id="ima_nagradu"
                                        name="ima_nagradu"
                                        value="1"
                                        <?= $imaNagraduOznaceno
                                            ? 'checked'
                                            : '' ?>
                                    >

                                    <label
                                        for="ima_nagradu"
                                        class="form-check-label"
                                    >
                                        Polaznik ima evidentiranu nagradu
                                    </label>

                                </div>

                                <div id="polja-nagrade">

                                    <div class="mb-3">

                                        <label
                                            for="naziv_nagrade"
                                            class="form-label"
                                        >
                                            Naziv nagrade
                                        </label>

                                        <input
                                            type="text"
                                            class="form-control"
                                            id="naziv_nagrade"
                                            name="naziv_nagrade"
                                            value="<?= htmlspecialchars(
                                                $_POST['naziv_nagrade']
                                                ?? $postojecaNagrada['naziv']
                                                ?? ''
                                            ) ?>"
                                        >

                                    </div>

                                    <div class="mb-3">

                                        <label
                                            for="opis_nagrade"
                                            class="form-label"
                                        >
                                            Opis nagrade
                                        </label>

                                        <textarea
                                            class="form-control"
                                            id="opis_nagrade"
                                            name="opis_nagrade"
                                            rows="3"
                                        ><?= htmlspecialchars(
                                            $_POST['opis_nagrade']
                                            ?? $postojecaNagrada['opis']
                                            ?? ''
                                        ) ?></textarea>

                                    </div>

                                    <div class="mb-0">

                                        <label
                                            for="datum_nagrade"
                                            class="form-label"
                                        >
                                            Datum osvajanja
                                        </label>

                                        <input
                                            type="date"
                                            class="form-control"
                                            id="datum_nagrade"
                                            name="datum_nagrade"
                                            value="<?= htmlspecialchars(
                                                $_POST['datum_nagrade']
                                                ?? $postojecaNagrada['datum']
                                                ?? ''
                                            ) ?>"
                                        >

                                    </div>

                                </div>

                            </div>

                        </div>

                        <div class="d-flex gap-2">

                            <button
                                type="submit"
                                class="btn btn-warning"
                            >
                                Sačuvaj izmene
                            </button>

                            <a
                                href="polaznici.php"
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

<script>
    const nivoSelect = document.getElementById('nivo_id');
    const nagradaKartica = document.getElementById('nagrada-kartica');
    const imaNagradu = document.getElementById('ima_nagradu');
    const poljaNagrade = document.getElementById('polja-nagrade');
    const nazivNagrade = document.getElementById('naziv_nagrade');

    function osveziPrikazNagrade() {
        const izabranaOpcija =
            nivoSelect.options[nivoSelect.selectedIndex];

        const nazivNivoa =
            izabranaOpcija?.dataset.naziv ?? '';

        const nivoDozvoljavaNagradu =
            nazivNivoa === 'napredni'
            || nazivNivoa === 'takmičarski';

        nagradaKartica.style.display =
            nivoDozvoljavaNagradu ? 'block' : 'none';

        if (!nivoDozvoljavaNagradu) {
            imaNagradu.checked = false;
        }

        poljaNagrade.style.display =
            nivoDozvoljavaNagradu && imaNagradu.checked
                ? 'block'
                : 'none';

        nazivNagrade.required =
            nivoDozvoljavaNagradu && imaNagradu.checked;
    }

    nivoSelect.addEventListener(
        'change',
        osveziPrikazNagrade
    );

    imaNagradu.addEventListener(
        'change',
        osveziPrikazNagrade
    );

    osveziPrikazNagrade();
</script>

</body>
</html>
