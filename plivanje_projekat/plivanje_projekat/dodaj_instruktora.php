<?php

require_once __DIR__ . '/classes/Session.php';
require_once __DIR__ . '/classes/Instruktor.php';

Session::requireLogin();

$instruktorModel = new Instruktor();

$greska = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ime = trim($_POST['ime'] ?? '');
    $prezime = trim($_POST['prezime'] ?? '');
    $telefon = trim($_POST['telefon'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $specijalnost = trim($_POST['specijalnost'] ?? '');

    if ($ime === '' || $prezime === '') {
        $greska = 'Ime i prezime su obavezni.';
    } elseif ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $greska = 'Email adresa nije ispravna.';
    } else {
        $uspeh = $instruktorModel->create([
            'ime' => $ime,
            'prezime' => $prezime,
            'telefon' => $telefon !== '' ? $telefon : null,
            'email' => $email !== '' ? $email : null,
            'specijalnost' => $specijalnost !== ''
                ? $specijalnost
                : null
        ]);

        if ($uspeh) {
            header('Location: instruktori.php');
            exit;
        }

        $greska = 'Došlo je do greške prilikom dodavanja instruktora.';
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

    <title>Dodavanje instruktora</title>

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

                <div class="card-header bg-success text-white">
                    <h1 class="h4 mb-0">
                        Dodaj novog instruktora
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
                                        $_POST['ime'] ?? ''
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
                                        $_POST['prezime'] ?? ''
                                    ) ?>"
                                    required
                                >
                            </div>

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
                                    $_POST['telefon'] ?? ''
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
                                    $_POST['email'] ?? ''
                                ) ?>"
                            >
                        </div>

                        <div class="mb-3">
                            <label for="specijalnost" class="form-label">
                                Specijalnost
                            </label>

                            <input
                                type="text"
                                class="form-control"
                                id="specijalnost"
                                name="specijalnost"
                                placeholder="Na primer: početnici, kraul, prsno..."
                                value="<?= htmlspecialchars(
                                    $_POST['specijalnost'] ?? ''
                                ) ?>"
                            >
                        </div>

                        <div class="d-flex gap-2">

                            <button
                                type="submit"
                                class="btn btn-success"
                            >
                                Sačuvaj instruktora
                            </button>

                            <a
                                href="instruktori.php"
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