<?php

require_once __DIR__ . '/classes/Session.php';

Session::requireLogin();

$username = Session::getUsername();

?>

<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Kontrolna tabla</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
</head>

<body class="bg-light">

<nav class="navbar navbar-dark bg-primary">

    <div class="container">

        <span class="navbar-brand">
            Evidencija časova plivanja
        </span>

        <div class="d-flex align-items-center gap-3 text-white">

            <span>
                Prijavljen:
                <strong><?= htmlspecialchars($username) ?></strong>
            </span>

            <a href="logout.php" class="btn btn-light btn-sm">
                Odjava
            </a>

        </div>

    </div>

</nav>

<div class="container py-5">

    <div class="row justify-content-center">

        <div class="col-lg-7 col-md-9">

            <div class="text-center mb-5">
                <h1 class="display-6 fw-bold">
                    Kontrolna tabla
                </h1>

                <p class="text-muted">
                    Upravljanje podacima škole plivanja
                </p>
            </div>

            <div class="d-grid gap-4">

                <div class="card shadow-sm border-0">
                    <div class="card-body text-center p-4">

                        <h2 class="h4 mb-3">
                            Polaznici
                        </h2>

                        <p class="text-muted">
                            Pregled, dodavanje, izmena i brisanje polaznika.
                        </p>

                        <a
                            href="polaznici.php"
                            class="btn btn-primary px-4"
                        >
                            Otvori polaznike
                        </a>

                    </div>
                </div>

                <div class="card shadow-sm border-0">
                    <div class="card-body text-center p-4">

                        <h2 class="h4 mb-3">
                            Instruktori
                        </h2>

                        <p class="text-muted">
                            Pregled i upravljanje instruktorima plivanja.
                        </p>

                        <a
                            href="instruktori.php"
                            class="btn btn-primary px-4"
                        >
                            Otvori instruktore
                        </a>

                    </div>
                </div>

                <div class="card shadow-sm border-0">
                    <div class="card-body text-center p-4">

                        <h2 class="h4 mb-3">
                            Termini
                        </h2>

                        <p class="text-muted">
                            Evidencija i upravljanje zakazanim časovima.
                        </p>

                        <a
                            href="termini.php"
                            class="btn btn-primary px-4"
                        >
                            Otvori termine
                        </a>

                    </div>
                </div>

                <div class="card shadow-sm border-0">
                    <div class="card-body text-center p-4">

                        <h2 class="h4 mb-3">
                            Nivoi znanja
                        </h2>

                        <p class="text-muted">
                            Pregled i upravljanje nivoima znanja polaznika.
                        </p>

                        <a
                            href="nivoi.php"
                            class="btn btn-primary px-4"
                        >
                            Otvori nivoe
                        </a>

                    </div>
                </div>

                <div class="card shadow-sm border-0">
                    <div class="card-body text-center p-4">

                        <h2 class="h4 mb-3">
                            Sertifikati
                        </h2>

                        <p class="text-muted">
                            Evidencija izdatih sertifikata polaznicima.
                        </p>

                        <a
                            href="sertifikati.php"
                            class="btn btn-primary px-4"
                        >
                            Otvori sertifikate
                        </a>

                    </div>
                </div>

            </div>

        </div>

    </div>

</div>


</body>
</html>