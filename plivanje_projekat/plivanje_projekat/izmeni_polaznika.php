<?php

require_once __DIR__ . '/classes/Polaznik.php';
require_once __DIR__ . '/classes/Nivo.php';

$polaznikModel = new Polaznik();
$nivoModel = new Nivo();

$nivoi = $nivoModel->read();

$greska = '';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    die('Neispravan ID polaznika.');
}

$polaznik = $polaznikModel->findById($id);

if (!$polaznik) {
    die('Polaznik nije pronađen.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ime = trim($_POST['ime'] ?? '');
    $prezime = trim($_POST['prezime'] ?? '');
    $datumRodjenja = $_POST['datum_rodjenja'] ?? '';
    $telefon = trim($_POST['telefon'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $nivoId = $_POST['nivo_id'] ?? '';

    if ($ime === '' || $prezime === '' || $nivoId === '') {
        $greska = 'Ime, prezime i nivo znanja su obavezni.';
    } elseif ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $greska = 'Email adresa nije ispravna.';
    } else {
        $uspeh = $polaznikModel->update($id, [
            'ime' => $ime,
            'prezime' => $prezime,
            'datum_rodjenja' => $datumRodjenja !== ''
                ? $datumRodjenja
                : null,
            'telefon' => $telefon !== ''
                ? $telefon
                : null,
            'email' => $email !== ''
                ? $email
                : null,
            'nivo_id' => (int) $nivoId
        ]);

        if ($uspeh) {
            header('Location: polaznici.php');
            exit;
        }

        $greska = 'Došlo je do greške prilikom izmene polaznika.';
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

    <title>Izmena polaznika</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
</head>

<body class="bg-light">

<div class="container py-5">

    <div class="row justify-content-center">

        <div class="col-md-8 col-lg-6">

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
                                <label
                                    for="ime"
                                    class="form-label"
                                >
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
                                <label
                                    for="prezime"
                                    class="form-label"
                                >
                                    Prezime
                                </label>

                                <input
                                    type="text"
                                    class="form-control"
                                    id="prezime"
                                    name="prezime"
                                    value="<?= htmlspecialchars(
                                        $_POST['prezime'] ?? $polaznik['prezime']
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
                            <label
                                for="telefon"
                                class="form-label"
                            >
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
                            <label
                                for="email"
                                class="form-label"
                            >
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

                        <div class="mb-3">
                            <label
                                for="nivo_id"
                                class="form-label"
                            >
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

                                <?php
                                $izabraniNivo = $_POST['nivo_id']
                                    ?? $polaznik['nivo_id'];
                                ?>

                                <?php foreach ($nivoi as $nivo): ?>

                                    <option
                                        value="<?= $nivo['id'] ?>"
                                        <?= $izabraniNivo == $nivo['id']
                                            ? 'selected'
                                            : '' ?>
                                    >
                                        <?= htmlspecialchars($nivo['naziv']) ?>
                                    </option>

                                <?php endforeach; ?>

                            </select>
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

</body>
</html>