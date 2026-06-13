<?php

require_once __DIR__ . '/classes/Polaznik.php';

$polaznikModel = new Polaznik();

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    die('Neispravan ID polaznika.');
}

$polaznik = $polaznikModel->findById($id);

if (!$polaznik) {
    die('Polaznik nije pronađen.');
}

$uspeh = $polaznikModel->delete($id);

if ($uspeh) {
    header('Location: polaznici.php');
    exit;
}

die('Došlo je do greške prilikom brisanja polaznika.');