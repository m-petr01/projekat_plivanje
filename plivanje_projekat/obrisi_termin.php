```php
<?php

require_once __DIR__ . '/classes/Session.php';
require_once __DIR__ . '/classes/Termin.php';

Session::requireLogin();

$terminModel = new Termin();

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

$datum = $termin['datum'];

try {
    $uspeh = $terminModel->delete($id);

    if ($uspeh) {
        header(
            'Location: termini.php?godina='
            . date('Y', strtotime($datum))
            . '&mesec='
            . date('n', strtotime($datum))
            . '&datum='
            . urlencode($datum)
            . '&obrisano=1'
        );

        exit;
    }

    die('Termin nije uspešno obrisan.');
} catch (PDOException $exception) {
    die(
        'Termin nije moguće obrisati jer postoje povezane rezervacije.'
    );
}

