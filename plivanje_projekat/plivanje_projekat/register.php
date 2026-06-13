<?php

require_once __DIR__ . '/classes/User.php';
require_once __DIR__ . '/classes/Session.php';

Session::start();

if (Session::isLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}

$userModel = new User();

$greska = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $passwordConfirmation = $_POST['password_confirmation'] ?? '';

    if (
        $username === ''
        || $email === ''
        || $password === ''
        || $passwordConfirmation === ''
    ) {
        $greska = 'Sva polja su obavezna.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $greska = 'Email adresa nije ispravna.';
    } elseif (strlen($username) < 3) {
        $greska = 'Korisničko ime mora imati najmanje 3 karaktera.';
    } elseif (strlen($password) < 6) {
        $greska = 'Lozinka mora imati najmanje 6 karaktera.';
    } elseif ($password !== $passwordConfirmation) {
        $greska = 'Lozinke se ne podudaraju.';
    } elseif ($userModel->findByEmail($email)) {
        $greska = 'Korisnik sa tom email adresom već postoji.';
    } else {
        $uspeh = $userModel->register(
            $username,
            $email,
            $password
        );

        if ($uspeh) {
            header('Location: login.php?registered=1');
            exit;
        }

        $greska = 'Registracija nije uspela.';
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

    <title>Registracija</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
</head>

<body class="bg-light">

<div class="container py-5">

    <div class="row justify-content-center">

        <div class="col-md-7 col-lg-5">

            <div class="card shadow-sm">

                <div class="card-header bg-primary text-white">
                    <h1 class="h4 mb-0">
                        Registracija korisnika
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
                            <label for="username" class="form-label">
                                Korisničko ime
                            </label>

                            <input
                                type="text"
                                class="form-control"
                                id="username"
                                name="username"
                                value="<?= htmlspecialchars(
                                    $_POST['username'] ?? ''
                                ) ?>"
                                required
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
                                required
                            >
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">
                                Lozinka
                            </label>

                            <input
                                type="password"
                                class="form-control"
                                id="password"
                                name="password"
                                required
                            >
                        </div>

                        <div class="mb-3">
                            <label
                                for="password_confirmation"
                                class="form-label"
                            >
                                Ponovi lozinku
                            </label>

                            <input
                                type="password"
                                class="form-control"
                                id="password_confirmation"
                                name="password_confirmation"
                                required
                            >
                        </div>

                        <button
                            type="submit"
                            class="btn btn-primary w-100"
                        >
                            Registruj se
                        </button>

                    </form>

                    <p class="text-center mt-3 mb-0">
                        Već imaš nalog?

                        <a href="login.php">
                            Prijavi se
                        </a>
                    </p>

                </div>
            </div>

        </div>
    </div>

</div>

</body>
</html>