<?php

require_once __DIR__ . '/config/Database.php';

$database = new Database();
$connection = $database->connect();

echo 'Uspešno povezivanje sa bazom!';