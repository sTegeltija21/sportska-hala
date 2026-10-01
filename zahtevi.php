<?php

require_once __DIR__ . '/provera_prijave.php';
require_once __DIR__ . '/klase/Zahtevi.php';

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$pretraga = trim($_GET['pretraga'] ?? '');
$zahtevi = (new Zahtevi())->prikaziSve($pretraga);

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
    <title>Spisak zahteva</title>
</head>
<body>
    <p><a href="index.php">← Nazad na početnu</a></p>
    <h1>Spisak zahteva</h1>

    <form action="odjava.php" method="post">
        <button type="submit">Odjavi se</button>
    </form>

    <form method="get">
        <label>
            Broj zahteva ili podnosilac:
            <input type="search" name="pretraga"
                   value="<?= prikazi($pretraga) ?>">
        </label>
        <button type="submit">Pretraži</button>
    </form>

    <p><a href="novi_zahtev.php">Novi zahtev</a></p>

    <p>
        <a href="stampa_spiska.php?pretraga=<?= urlencode($pretraga) ?>">
            Štampaj prikazani spisak
        </a>
    </p>

    <p>
        <a href="dnevni_izvestaj.php">
            Dnevni izveštaj o zauzetosti kapaciteta
        </a>
    </p>

    <table border="1" cellpadding="8">
        <thead>
            <tr>
                <th>Broj</th>
                <th>Podnosilac</th>
                <th>Datum</th>
                <th>Termin</th>
                <th>Sport</th>
                <th>Status</th>
                <th>Detalji</th>
                <th>Brisanje</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($zahtevi as $zahtev): ?>
                <tr>
                    <td><?= prikazi($zahtev['broj_zahteva']) ?></td>
                    <td><?= prikazi($zahtev['podnosilac']) ?></td>
                    <td><?= prikazi($zahtev['datum_koriscenja']) ?></td>
                    <td>
                        <?= prikazi(substr($zahtev['vreme_od'], 0, 5)) ?>
                        –
                        <?= prikazi(substr($zahtev['vreme_do'], 0, 5)) ?>
                    </td>
                    <td><?= prikazi($zahtev['sport']) ?></td>
                    <td>
                        <?= prikazi(
                            $statusNaziv[$zahtev['status']] ?? $zahtev['status']
                        ) ?>
                    </td>
                    <td>
                        <a href="detalji_zahteva.php?id=<?= (int) $zahtev['id'] ?>">
                            Otvori
                        </a>
                    </td>
                    <td>
                        <form action="obrisi_zahtev.php" method="post"
                              onsubmit="return confirm('Obrisati zahtev i sve njegove učesnike?');">
                            <input type="hidden" name="id"
                                   value="<?= (int) $zahtev['id'] ?>">
                            <input type="hidden" name="csrf_token"
                                   value="<?= prikazi($_SESSION['csrf_token']) ?>">
                            <button type="submit">Obriši</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <?php if (!$zahtevi): ?>
        <p>Nema zahteva za zadatu pretragu.</p>
    <?php endif; ?>
</body>
</html>