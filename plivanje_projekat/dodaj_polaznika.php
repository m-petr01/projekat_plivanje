<?php

require_once __DIR__ . '/classes/Polaznik.php';
require_once __DIR__ . '/classes/Nivo.php';
require_once __DIR__ . '/classes/Nagrada.php';

$polaznikModel = new Polaznik();
$nivoModel = new Nivo();
$nagradaModel = new Nagrada();

$nivoi = $nivoModel->read();
$greska = '';

$dozvoljeniNivoiZaNagrade = [];
foreach ($nivoi as $nivo) {
    $nazivNivoa = mb_strtolower(trim($nivo['naziv']), 'UTF-8');
    if (in_array($nazivNivoa, ['napredni', 'takmičarski'], true)) {
        $dozvoljeniNivoiZaNagrade[] = (int) $nivo['id'];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ime = trim($_POST['ime'] ?? '');
    $prezime = trim($_POST['prezime'] ?? '');
    $datumRodjenja = $_POST['datum_rodjenja'] ?? '';
    $telefon = trim($_POST['telefon'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $nivoId = filter_input(INPUT_POST, 'nivo_id', FILTER_VALIDATE_INT);

    $imaNagradu = isset($_POST['ima_nagradu']);
    $nazivNagrade = trim($_POST['naziv_nagrade'] ?? '');
    $opisNagrade = trim($_POST['opis_nagrade'] ?? '');
    $datumNagrade = $_POST['datum_nagrade'] ?? '';

    $nivoPostoji = false;
    foreach ($nivoi as $nivo) {
        if ((int) $nivo['id'] === (int) $nivoId) {
            $nivoPostoji = true;
            break;
        }
    }

    $nivoDozvoljavaNagradu = in_array((int) $nivoId, $dozvoljeniNivoiZaNagrade, true);

    if ($ime === '' || $prezime === '' || !$nivoId) {
        $greska = 'Ime, prezime i nivo znanja su obavezni.';
    } elseif (!$nivoPostoji) {
        $greska = 'Izabrani nivo znanja ne postoji.';
    } elseif ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $greska = 'Email adresa nije ispravna.';
    } elseif ($imaNagradu && !$nivoDozvoljavaNagradu) {
        $greska = 'Nagrada se pri unosu može evidentirati samo za napredni ili takmičarski nivo.';
    } elseif ($imaNagradu && $nazivNagrade === '') {
        $greska = 'Unesite naziv nagrade.';
    } else {
        $noviPolaznikId = $polaznikModel->createAndReturnId([
            'ime' => $ime,
            'prezime' => $prezime,
            'datum_rodjenja' => $datumRodjenja !== '' ? $datumRodjenja : null,
            'telefon' => $telefon !== '' ? $telefon : null,
            'email' => $email !== '' ? $email : null,
            'nivo_id' => (int) $nivoId
        ]);

        if ($noviPolaznikId === false) {
            $greska = 'Došlo je do greške prilikom dodavanja polaznika.';
        } elseif ($imaNagradu) {
            $nagradaSacuvana = $nagradaModel->create([
                'polaznik_id' => $noviPolaznikId,
                'naziv' => $nazivNagrade,
                'opis' => $opisNagrade !== '' ? $opisNagrade : null,
                'datum' => $datumNagrade !== '' ? $datumNagrade : null
            ]);

            if (!$nagradaSacuvana) {
                $polaznikModel->delete($noviPolaznikId);
                $greska = 'Nagrada nije sačuvana, pa dodavanje polaznika nije završeno.';
            } else {
                header('Location: polaznici.php?dodato=1');
                exit;
            }
        } else {
            header('Location: polaznici.php?dodato=1');
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dodavanje polaznika</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>.award-section{border-left:5px solid #ffc107;}</style>
</head>
<body class="bg-light">
<div class="container py-5">
<div class="row justify-content-center">
<div class="col-md-9 col-lg-7">
<div class="card shadow-sm">
<div class="card-header bg-success text-white"><h1 class="h4 mb-0">Dodaj novog polaznika</h1></div>
<div class="card-body">
<?php if ($greska !== ''): ?><div class="alert alert-danger"><?= htmlspecialchars($greska) ?></div><?php endif; ?>
<form method="POST">
<div class="row">
<div class="col-md-6 mb-3"><label for="ime" class="form-label">Ime</label><input type="text" class="form-control" id="ime" name="ime" value="<?= htmlspecialchars($_POST['ime'] ?? '') ?>" required></div>
<div class="col-md-6 mb-3"><label for="prezime" class="form-label">Prezime</label><input type="text" class="form-control" id="prezime" name="prezime" value="<?= htmlspecialchars($_POST['prezime'] ?? '') ?>" required></div>
</div>
<div class="mb-3"><label for="datum_rodjenja" class="form-label">Datum rođenja</label><input type="date" class="form-control" id="datum_rodjenja" name="datum_rodjenja" value="<?= htmlspecialchars($_POST['datum_rodjenja'] ?? '') ?>"></div>
<div class="mb-3"><label for="telefon" class="form-label">Telefon</label><input type="text" class="form-control" id="telefon" name="telefon" value="<?= htmlspecialchars($_POST['telefon'] ?? '') ?>"></div>
<div class="mb-3"><label for="email" class="form-label">Email</label><input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"></div>
<div class="mb-4"><label for="nivo_id" class="form-label">Nivo znanja</label><select class="form-select" id="nivo_id" name="nivo_id" required><option value="">Izaberi nivo</option><?php foreach ($nivoi as $nivo): ?><option value="<?= (int) $nivo['id'] ?>" data-naziv="<?= htmlspecialchars(mb_strtolower(trim($nivo['naziv']), 'UTF-8')) ?>" <?= ($_POST['nivo_id'] ?? '') == $nivo['id'] ? 'selected' : '' ?>><?= htmlspecialchars($nivo['naziv']) ?></option><?php endforeach; ?></select></div>

<div id="nagrada-kartica" class="card bg-light mb-4 award-section">
<div class="card-body">
<h2 class="h5">Nagrade i rezultati</h2>
<p class="text-muted">Nagrada se može evidentirati samo za napredni ili takmičarski nivo.</p>
<div class="form-check mb-3"><input type="checkbox" class="form-check-input" id="ima_nagradu" name="ima_nagradu" value="1" <?= isset($_POST['ima_nagradu']) ? 'checked' : '' ?>><label for="ima_nagradu" class="form-check-label">Polaznik ima evidentiranu nagradu</label></div>
<div id="polja-nagrade">
<div class="mb-3"><label for="naziv_nagrade" class="form-label">Naziv nagrade</label><input type="text" class="form-control" id="naziv_nagrade" name="naziv_nagrade" placeholder="Na primer: Prvo mesto – 100 m kraul" value="<?= htmlspecialchars($_POST['naziv_nagrade'] ?? '') ?>"></div>
<div class="mb-3"><label for="opis_nagrade" class="form-label">Opis nagrade</label><textarea class="form-control" id="opis_nagrade" name="opis_nagrade" rows="3" placeholder="Naziv takmičenja, disciplina i rezultat."><?= htmlspecialchars($_POST['opis_nagrade'] ?? '') ?></textarea></div>
<div class="mb-0"><label for="datum_nagrade" class="form-label">Datum osvajanja</label><input type="date" class="form-control" id="datum_nagrade" name="datum_nagrade" value="<?= htmlspecialchars($_POST['datum_nagrade'] ?? '') ?>"></div>
</div>
</div>
</div>

<div class="d-flex gap-2"><button type="submit" class="btn btn-success">Sačuvaj polaznika</button><a href="polaznici.php" class="btn btn-secondary">Otkaži</a></div>
</form>
</div></div></div></div></div>
<script>
const nivoSelect=document.getElementById('nivo_id');
const kartica=document.getElementById('nagrada-kartica');
const ima=document.getElementById('ima_nagradu');
const polja=document.getElementById('polja-nagrade');
const naziv=document.getElementById('naziv_nagrade');
function osvezi(){
 const opt=nivoSelect.options[nivoSelect.selectedIndex];
 const nivo=opt?.dataset.naziv??'';
 const dozvoljeno=nivo==='napredni'||nivo==='takmičarski';
 kartica.style.display=dozvoljeno?'block':'none';
 if(!dozvoljeno){ima.checked=false;}
 polja.style.display=dozvoljeno&&ima.checked?'block':'none';
 naziv.required=dozvoljeno&&ima.checked;
}
nivoSelect.addEventListener('change',osvezi);
ima.addEventListener('change',osvezi);
osvezi();
</script>
</body></html>
