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
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $greska = 'Email i lozinka su obavezni.';
    } else {
        $user = $userModel->login($email, $password);

        if ($user) {
            Session::login($user);

            header('Location: dashboard.php');
            exit;
        }

        $greska = 'Email ili lozinka nisu ispravni.';
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

    <title>Prijava</title>

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

                <div class="card-header bg-dark text-white">
                    <h1 class="h4 mb-0">
                        Prijava korisnika
                    </h1>
                </div>

                <div class="card-body">

                    <?php if (isset($_GET['registered'])): ?>

                        <div class="alert alert-success">
                            Registracija je uspešna. Sada se prijavite.
                        </div>

                    <?php endif; ?>

                    <?php if ($greska !== ''): ?>

                        <div class="alert alert-danger">
                            <?= htmlspecialchars($greska) ?>
                        </div>

                    <?php endif; ?>

                    <form method="POST">

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

                        <button
                            type="submit"
                            class="btn btn-dark w-100"
                        >
                            Prijavi se
                        </button>

                    </form>

                    <p class="text-center mt-3 mb-0">
                        Nemaš nalog?

                        <a href="register.php">
                            Registruj se
                        </a>
                    </p>

                </div>
            </div>

        </div>
    </div>

</div>

</body>
</html>