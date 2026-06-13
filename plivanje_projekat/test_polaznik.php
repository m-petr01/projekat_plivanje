<?php

require_once __DIR__ . '/classes/Polaznik.php';

$polaznik = new Polaznik();

$uspeh = $polaznik->create([
    'ime' => 'Marko',
    'prezime' => 'Markovic',
    'datum_rodjenja' => '2000-05-10',
    'telefon' => '061123456',
    'email' => 'marko@example.com',
    'nivo_id' => 1
]);

if ($uspeh) {
    echo 'Polaznik je uspešno dodat!';
} else {
    echo 'Greška pri dodavanju polaznika.';
}