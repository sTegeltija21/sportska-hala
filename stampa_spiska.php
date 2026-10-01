<?php

require_once __DIR__ . '/provera_prijave.php';
require_once __DIR__ . '/klase/Zahtevi.php';

$pretraga = trim($_GET['pretraga'] ?? '');
$zahtevi = (new Zahtevi())->prikaziSve($pretraga);

function prikazi(string $vrednost): string
{
    return htmlspecialchars($vrednost, ENT_QUOTES, 'UTF-8');
}

$statusNaziv = [
    'na_cekanju' => 'Na čekanju',
    'odobren' => 'Odobren',
    'odbijen' => 'Odbijen'
];
?>

<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="stil.css">
    <title>Štampa spiska zahteva</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 30px;
            color: #111;
        }

        h1 {
            font-size: 22px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #aaa;
            padding: 8px;
            text-align: left;
        }

        th {
            background: #eee;
        }

        @media print {
            .komande {
                display: none;
            }

            body {
                margin: 12mm;
            }
        }
    </style>
</head>
<body>
    <div class="komande">
        <button type="button" onclick="window.print()">Štampaj</button>
        <a href="zahtevi.php?pretraga=<?= urlencode($pretraga) ?>">
            Nazad na spisak
        </a>
    </div>

    <h1>Spisak zahteva za iznajmljivanje sportske hale</h1>

    <?php if ($pretraga !== ''): ?>
        <p><strong>Filter:</strong> <?= prikazi($pretraga) ?></p>
    <?php else: ?>
        <p>Svi zahtevi</p>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th>Broj</th>
                <th>Podnosilac</th>
                <th>Datum</th>
                <th>Termin</th>
                <th>Sport</th>
                <th>Status</th>
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
                        <?= prikazi($statusNaziv[$zahtev['status']] ?? $zahtev['status']) ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <p><strong>Ukupno zahteva:</strong> <?= count($zahtevi) ?></p>
</body>
</html>