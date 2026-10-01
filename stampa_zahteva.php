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
    <title>Štampa zahteva <?= prikazi($zahtev['broj_zahteva']) ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 40px auto;
            color: #111;
        }

        h1 {
            font-size: 22px;
            margin-bottom: 30px;
        }

        h2 {
            font-size: 17px;
            margin-top: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #bbb;
            padding: 10px;
            text-align: left;
        }

        th {
            background: #eee;
        }

        .dugme {
            margin-bottom: 25px;
        }

        @media print {
            body {
                margin: 15mm auto;
            }

            .dugme {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="dugme">
        <button type="button" onclick="window.print()">Štampaj</button>
        <a href="detalji_zahteva.php?id=<?= $id ?>">Nazad</a>
    </div>

    <h1>Zahtev za iznajmljivanje termina za sportsku halu</h1>

    <h2>Podaci o zahtevu</h2>
    <table>
        <tr>
            <td><strong>Broj zahteva:</strong> <?= prikazi($zahtev['broj_zahteva']) ?></td>
            <td><strong>Datum podnošenja:</strong> <?= prikazi($zahtev['datum_podnosenja']) ?></td>
        </tr>
        <tr>
            <td><strong>Podnosilac:</strong> <?= prikazi($zahtev['podnosilac']) ?></td>
            <td><strong>Kontakt:</strong> <?= prikazi($zahtev['kontakt']) ?></td>
        </tr>
        <tr>
            <td><strong>Datum korišćenja:</strong> <?= prikazi($zahtev['datum_koriscenja']) ?></td>
            <td>
                <strong>Vreme:</strong>
                <?= prikazi(substr($zahtev['vreme_od'], 0, 5)) ?>
                –
                <?= prikazi(substr($zahtev['vreme_do'], 0, 5)) ?>
            </td>
        </tr>
        <tr>
            <td><strong>Sport:</strong> <?= prikazi($zahtev['sport']) ?></td>
            <td>
                <strong>Status:</strong>
                <?= prikazi($statusNaziv[$zahtev['status']] ?? $zahtev['status']) ?>
            </td>
        </tr>
    </table>

    <h2>Spisak učesnika</h2>
    <table>
        <thead>
            <tr>
                <th>R. br.</th>
                <th>Ime i prezime učesnika</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($ucesnici as $redniBroj => $ucesnik): ?>
                <tr>
                    <td><?= $redniBroj + 1 ?></td>
                    <td><?= prikazi($ucesnik['ime_prezime']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <p><strong>Ukupan broj prijavljenih učesnika:</strong> <?= count($ucesnici) ?></p>
</body>
</html>