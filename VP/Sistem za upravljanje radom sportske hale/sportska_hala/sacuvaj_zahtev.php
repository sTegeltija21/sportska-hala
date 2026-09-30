<?php

require_once __DIR__ . '/provera_prijave.php';
require_once __DIR__ . '/klase/Zahtevi.php';
require_once __DIR__ . '/klase/Sportovi.php';

function prikaziGresku(string $poruka): void
{
    http_response_code(400);
    echo '<h1>Zahtev nije sačuvan</h1>';
    echo '<p>' . htmlspecialchars($poruka, ENT_QUOTES, 'UTF-8') . '</p>';
    echo '<a href="novi_zahtev.php">Nazad na unos</a>';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: novi_zahtev.php');
    exit;
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
    )
];

$ucesnici = array_map(
    'trim',
    is_array($_POST['ucesnici'] ?? null) ? $_POST['ucesnici'] : []
);

if (
    $podaci['broj_zahteva'] === '' ||
    strlen($podaci['broj_zahteva']) > 30 ||
    $podaci['podnosilac'] === '' ||
    strlen($podaci['podnosilac']) > 100 ||
    !filter_var($podaci['kontakt'], FILTER_VALIDATE_EMAIL) ||
    strlen($podaci['kontakt']) > 100 ||
    !$podaci['sport_id']
) {
    prikaziGresku('Proveri broj zahteva, podnosioca, kontakt i sport.');
}

$datum = DateTime::createFromFormat('!Y-m-d', $podaci['datum_koriscenja']);

if (!$datum || $datum->format('Y-m-d') !== $podaci['datum_koriscenja']) {
    prikaziGresku('Datum korišćenja nije ispravan.');
}

if (
    !preg_match('/^\d{2}:\d{2}$/', $podaci['vreme_od']) ||
    !preg_match('/^\d{2}:\d{2}$/', $podaci['vreme_do']) ||
    $podaci['vreme_od'] >= $podaci['vreme_do']
) {
    prikaziGresku('Vreme završetka mora biti posle vremena početka.');
}

if (
    $podaci['vreme_od'] < '08:00' ||
    $podaci['vreme_do'] > '23:00'
) {
    prikaziGresku('Termin mora biti u radnom vremenu hale: 08:00–23:00.');
}

if (count($ucesnici) < 1) {
    prikaziGresku('Unesi bar jednog učesnika.');
}

foreach ($ucesnici as $imePrezime) {
    if ($imePrezime === '' || mb_strlen($imePrezime) > 100) {
        prikaziGresku('Svaki učesnik mora imati ime i prezime do 100 znakova.');
    }
}

try {
    $sport = (new Sportovi())->pronadji((int) $podaci['sport_id']);

    if ($sport === null) {
        prikaziGresku('Izabrani sport ne postoji.');
    }

    $zahtev = new Zahtev($sport);

    foreach ($ucesnici as $imePrezime) {
        $zahtev->dodajUcesnika(new Ucesnik($imePrezime));
    }

    (new Zahtevi())->sacuvaj($podaci, $zahtev);

    echo '<h1>Zahtev je sačuvan</h1>';
    echo '<a href="novi_zahtev.php">Unesi novi zahtev</a>';
} catch (Throwable $greska) {
    error_log($greska->getMessage());
    prikaziGresku(
        'Upis nije uspeo. Proveri da li broj zahteva već postoji.'
    );
}