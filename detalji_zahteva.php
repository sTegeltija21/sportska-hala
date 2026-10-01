<?php

require_once __DIR__ . '/provera_prijave.php';
require_once __DIR__ . '/klase/Zahtevi.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    http_response_code(400);
    exit('Neispravan ID zahteva.');
}

$zahtevi = new Zahtevi();
$zahtev = $zahtevi->prikaziJedan($id);

if (!$zahtev) {
    http_response_code(404);
    exit('Zahtev nije pronađen.');
}

$ucesnici = $zahtevi->prikaziUcesnike($id);

$statusNaziv = [
    'na_cekanju' => 'Na čekanju',
    'odobren' => 'Odobren',
    'odbijen' => 'Odbijen'
];

function prikazi(string $vrednost): string
{
    return htmlspecialchars($vrednost, ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="stil.css">
    <title>Zahtev <?= prikazi($zahtev['broj_zahteva']) ?></title>
</head>
<body>
    <p><a href="zahtevi.php">Nazad na spisak</a></p>

    <p>
        <a href="izmeni_zahtev.php?id=<?= $id ?>">Izmeni zahtev</a>
        |
        <a href="stampa_zahteva.php?id=<?= $id ?>">Štampaj zahtev</a>
    </p>

    <h1>Zahtev za iznajmljivanje termina za sportsku halu</h1>

    <p><strong>Broj zahteva:</strong> <?= prikazi($zahtev['broj_zahteva']) ?></p>
    <p><strong>Datum podnošenja:</strong> <?= prikazi($zahtev['datum_podnosenja']) ?></p>
    <p><strong>Podnosilac:</strong> <?= prikazi($zahtev['podnosilac']) ?></p>
    <p><strong>Kontakt:</strong> <?= prikazi($zahtev['kontakt']) ?></p>
    <p><strong>Datum korišćenja:</strong> <?= prikazi($zahtev['datum_koriscenja']) ?></p>

    <p>
        <strong>Termin:</strong>
        <?= prikazi(substr($zahtev['vreme_od'], 0, 5)) ?>
        –
        <?= prikazi(substr($zahtev['vreme_do'], 0, 5)) ?>
    </p>

    <p><strong>Sport:</strong> <?= prikazi($zahtev['sport']) ?></p>
    <p>
        <strong>Status:</strong>
        <?= prikazi($statusNaziv[$zahtev['status']] ?? $zahtev['status']) ?>
    </p>

    <h2>Učesnici</h2>
    <ol>
        <?php foreach ($ucesnici as $ucesnik): ?>
            <li><?= prikazi($ucesnik['ime_prezime']) ?></li>
        <?php endforeach; ?>
    </ol>
</body>
</html>