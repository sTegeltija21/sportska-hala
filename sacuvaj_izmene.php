<?php

require_once __DIR__ . '/provera_prijave.php';
require_once __DIR__ . '/klase/Zahtevi.php';
require_once __DIR__ . '/klase/Sportovi.php';

function greska(string $poruka): void
{
    http_response_code(400);
    echo '<h1>Izmene nisu sačuvane</h1>';
    echo '<p>' . htmlspecialchars($poruka, ENT_QUOTES, 'UTF-8') . '</p>';
    echo '<a href="zahtevi.php">Nazad na spisak</a>';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Nedozvoljen način slanja.');
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$token = $_POST['csrf_token'] ?? '';

if (
    !$id ||
    !is_string($token) ||
    !hash_equals($_SESSION['csrf_token'] ?? '', $token)
) {
    greska('Neispravan zahtev za izmenu.');
}

$podaci = [
    'broj_zahteva' => trim($_POST['broj_zahteva'] ?? ''),
    'podnosilac' => trim($_POST['podnosilac'] ?? ''),
    'kontakt' => trim($_POST['kontakt'] ?? ''),
    'datum_koriscenja' => trim($_POST['datum_koriscenja'] ?? ''),
    'vreme_od' => trim($_POST['vreme_od'] ?? ''),
    'vreme_do' => trim($_POST['vreme_do'] ?? ''),
    'sport_id' => filter_var(
        $_POST['sport_id'] ?? null,
        FILTER_VALIDATE_INT
    ),
    'status' => $_POST['status'] ?? ''
];

$ucesnici = $_POST['ucesnici'] ?? [];

if (!is_array($ucesnici)) {
    greska('Spisak učesnika nije ispravan.');
}

$ucesnici = array_map('trim', $ucesnici);

if (
    $podaci['broj_zahteva'] === '' ||
    strlen($podaci['broj_zahteva']) > 30 ||
    $podaci['podnosilac'] === '' ||
    mb_strlen($podaci['podnosilac']) > 100 ||
    !filter_var($podaci['kontakt'], FILTER_VALIDATE_EMAIL) ||
    strlen($podaci['kontakt']) > 100 ||
    !$podaci['sport_id'] ||
    !in_array(
        $podaci['status'],
        ['na_cekanju', 'odobren', 'odbijen'],
        true
    )
) {
    greska('Proveri podatke o zahtevu.');
}

$datum = DateTime::createFromFormat('!Y-m-d', $podaci['datum_koriscenja']);

if (!$datum || $datum->format('Y-m-d') !== $podaci['datum_koriscenja']) {
    greska('Datum korišćenja nije ispravan.');
}

$pocetak = DateTime::createFromFormat('!H:i', $podaci['vreme_od']);
$kraj = DateTime::createFromFormat('!H:i', $podaci['vreme_do']);

if (
    !$pocetak ||
    !$kraj ||
    $pocetak->format('H:i') !== $podaci['vreme_od'] ||
    $kraj->format('H:i') !== $podaci['vreme_do'] ||
    $pocetak >= $kraj
) {
    greska('Vreme završetka mora biti posle vremena početka.');
}

if (
    $podaci['vreme_od'] < '08:00' ||
    $podaci['vreme_do'] > '23:00'
) {
    greska('Termin mora biti u radnom vremenu hale: 08:00–23:00.');
}

if (count($ucesnici) < 1) {
    greska('Zahtev mora imati bar jednog učesnika.');
}

foreach ($ucesnici as $imePrezime) {
    if ($imePrezime === '' || mb_strlen($imePrezime) > 100) {
        greska('Proveri imena učesnika.');
    }
}

try {
    $sport = (new Sportovi())->pronadji((int) $podaci['sport_id']);

    if ($sport === null) {
        greska('Izabrani sport ne postoji.');
    }

    $zahtev = new Zahtev($sport);

    foreach ($ucesnici as $imePrezime) {
        $zahtev->dodajUcesnika(new Ucesnik($imePrezime));
    }

    (new Zahtevi())->izmeni($id, $podaci, $zahtev);

    header('Location: detalji_zahteva.php?id=' . $id);
    exit;
} catch (RuntimeException $izuzetak) {
    greska($izuzetak->getMessage());
} catch (Throwable $izuzetak) {
    error_log($izuzetak->getMessage());
    greska('Čuvanje nije uspelo. Proveri da li broj zahteva već postoji.');
}