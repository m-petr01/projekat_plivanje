```php
<?php

require_once __DIR__ . '/classes/Session.php';
require_once __DIR__ . '/classes/Instruktor.php';

Session::requireLogin();

$instruktorModel = new Instruktor();

$greska = '';

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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ime = trim($_POST['ime'] ?? '');
    $prezime = trim($_POST['prezime'] ?? '');
    $telefon = trim($_POST['telefon'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $specijalnost = trim($_POST['specijalnost'] ?? '');

    $godineIskustva = filter_input(
        INPUT_POST,
        'godine_iskustva',
        FILTER_VALIDATE_INT
    );

    $obrazovanje = trim($_POST['obrazovanje'] ?? '');
    $biografija = trim($_POST['biografija'] ?? '');
    $sertifikatiOpis = trim($_POST['sertifikati_opis'] ?? '');

    if ($ime === '' || $prezime === '') {
        $greska = 'Ime i prezime su obavezni.';
    } elseif (
        $email !== ''
        && !filter_var($email, FILTER_VALIDATE_EMAIL)
    ) {
        $greska = 'Email adresa nije ispravna.';
    } elseif (
        ($_POST['godine_iskustva'] ?? '') !== ''
        && (
            $godineIskustva === false
            || $godineIskustva < 0
        )
    ) {
        $greska = 'Godine iskustva moraju biti pozitivan broj.';
    } else {
        $uspeh = $instruktorModel->update($id, [
            'ime' => $ime,
            'prezime' => $prezime,

            'telefon' => $telefon !== ''
                ? $telefon
                : null,

            'email' => $email !== ''
                ? $email
                : null,

            'specijalnost' => $specijalnost !== ''
                ? $specijalnost
                : null,

            'godine_iskustva' =>
                ($_POST['godine_iskustva'] ?? '') !== ''
                    ? $godineIskustva
                    : null,

            'obrazovanje' => $obrazovanje !== ''
                ? $obrazovanje
                : null,

            'biografija' => $biografija !== ''
                ? $biografija
                : null,

            'sertifikati_opis' => $sertifikatiOpis !== ''
                ? $sertifikatiOpis
                : null
        ]);

        if ($uspeh) {
            header(
                'Location: profil_instruktora.php?id=' . $id
            );
            exit;
        }

        $greska = 'Došlo je do greške prilikom izmene instruktora.';
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

    <title>Izmena instruktora</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
</head>

<body class="bg-light">

<div class="container py-5">

    <div class="row justify-content-center">

        <div class="col-lg-8">

            <div class="card shadow-sm">

                <div class="card-header bg-warning">

                    <h1 class="h4 mb-0">
                        Izmeni profil instruktora
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
                                        $_POST['ime']
                                        ?? $instruktor['ime']
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
                                        $_POST['prezime']
                                        ?? $instruktor['prezime']
                                    ) ?>"
                                    required
                                >

                            </div>

                        </div>

                        <div class="row">

                            <div class="col-md-6 mb-3">

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
                                        ?? $instruktor['telefon']
                                        ?? ''
                                    ) ?>"
                                >

                            </div>

                            <div class="col-md-6 mb-3">

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
                                        ?? $instruktor['email']
                                        ?? ''
                                    ) ?>"
                                >

                            </div>

                        </div>

                        <div class="mb-3">

                            <label
                                for="specijalnost"
                                class="form-label"
                            >
                                Specijalnost
                            </label>

                            <input
                                type="text"
                                class="form-control"
                                id="specijalnost"
                                name="specijalnost"
                                placeholder="Na primer: kraul, početnici, takmičarsko plivanje..."
                                value="<?= htmlspecialchars(
                                    $_POST['specijalnost']
                                    ?? $instruktor['specijalnost']
                                    ?? ''
                                ) ?>"
                            >

                        </div>

                        <div class="row">

                            <div class="col-md-4 mb-3">

                                <label
                                    for="godine_iskustva"
                                    class="form-label"
                                >
                                    Godine iskustva
                                </label>

                                <input
                                    type="number"
                                    class="form-control"
                                    id="godine_iskustva"
                                    name="godine_iskustva"
                                    min="0"
                                    value="<?= htmlspecialchars(
                                        $_POST['godine_iskustva']
                                        ?? $instruktor['godine_iskustva']
                                        ?? ''
                                    ) ?>"
                                >

                            </div>

                            <div class="col-md-8 mb-3">

                                <label
                                    for="obrazovanje"
                                    class="form-label"
                                >
                                    Obrazovanje
                                </label>

                                <input
                                    type="text"
                                    class="form-control"
                                    id="obrazovanje"
                                    name="obrazovanje"
                                    placeholder="Na primer: Fakultet sporta i fizičkog vaspitanja"
                                    value="<?= htmlspecialchars(
                                        $_POST['obrazovanje']
                                        ?? $instruktor['obrazovanje']
                                        ?? ''
                                    ) ?>"
                                >

                            </div>

                        </div>

                        <div class="mb-3">

                            <label
                                for="biografija"
                                class="form-label"
                            >
                                Biografija
                            </label>

                            <textarea
                                class="form-control"
                                id="biografija"
                                name="biografija"
                                rows="5"
                                placeholder="Unesite opis iskustva, načina rada i profesionalne biografije instruktora."
                            ><?= htmlspecialchars(
                                $_POST['biografija']
                                ?? $instruktor['biografija']
                                ?? ''
                            ) ?></textarea>

                        </div>

                        <div class="mb-4">

                            <label
                                for="sertifikati_opis"
                                class="form-label"
                            >
                                Sertifikati i stručne kvalifikacije
                            </label>

                            <textarea
                                class="form-control"
                                id="sertifikati_opis"
                                name="sertifikati_opis"
                                rows="5"
                                placeholder="Unesite sertifikate, licence, završene obuke i stručne kvalifikacije."
                            ><?= htmlspecialchars(
                                $_POST['sertifikati_opis']
                                ?? $instruktor['sertifikati_opis']
                                ?? ''
                            ) ?></textarea>

                        </div>

                        <div class="d-flex gap-2">

                            <button
                                type="submit"
                                class="btn btn-warning"
                            >
                                Sačuvaj izmene
                            </button>

                            <a
                                href="profil_instruktora.php?id=<?= (int) $instruktor['id'] ?>"
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
