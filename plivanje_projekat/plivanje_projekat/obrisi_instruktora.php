<?php

require_once __DIR__ . '/classes/Session.php';
require_once __DIR__ . '/classes/Instruktor.php';

Session::requireLogin();

$instruktorModel = new Instruktor();

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    die('Neispravan ID instruktora.');
}

$instruktor = $instruktorModel->findById($id);

if (!$instruktor) {
    die('Instruktor nije pronađen.');
}

try {
    $uspeh = $instruktorModel->delete($id);

    if ($uspeh) {
        header('Location: instruktori.php');
        exit;
    }

    die('Došlo je do greške prilikom brisanja instruktora.');
} catch (PDOException $exception) {
    die(
        'Instruktor ne može biti obrisan jer je povezan sa postojećim terminima.'
    );
}