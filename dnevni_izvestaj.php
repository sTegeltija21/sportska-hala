<?php

require_once __DIR__ . '/provera_prijave.php';
require_once __DIR__ . '/klase/Izvestaji.php';

$datum = trim($_GET['datum'] ?? date('Y-m-d'));
$proveraDatuma = DateTime::createFromFormat('!Y-m-d', $datum);

if (!$proveraDatuma || $proveraDatuma->format('Y-m-d') !== $datum) {
    http_response_code(400);
    exit('Neispravan datum.');
}

$izvestaji = new Izvestaji();
$termini = $izvestaji->dnevni($datum);
$rezime = $izvestaji->rezimeZaDan($datum);

$ukupnoMinuta = (int) $rezime['rezervisano_minuta'];
$ukupnoUcesnika = (int) $rezime['ukupno_ucesnika'];
$brojTermina = (int) $rezime['broj_termina'];

$radnoVremeMinuta = 15 * 60;
$slobodnoMinuta = $radnoVremeMinuta - $ukupnoMinuta;
$procenatZauzetosti = ($ukupnoMinuta / $radnoVremeMinuta) * 100;

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
    <title>Dnevni izveštaj o zauzetosti kapaciteta</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 900px;
            margin: 35px auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #aaa;
            padding: 9px;
            text-align: left;
        }

        th {
            background: #eee;
        }

        @media print {
            .komande {
                display: none;
            }
        }
    </style>
</head>
<body>
    <p><a href="index.php">← Nazad na početnu</a></p>
    <div class="komande">
        <p><a href="zahtevi.php">Nazad na spisak zahteva</a></p>

        <form method="get">
            <label>Datum izveštaja:
                <input type="date" name="datum"
                       value="<?= prikazi($datum) ?>" required>
            </label>
            <button type="submit">Prikaži</button>
        </form>

        <p><button type="button" onclick="window.print()">Štampaj izveštaj</button></p>
    </div>

    <h1>Dnevni izveštaj o zauzetosti kapaciteta</h1>
    <p><strong>Datum:</strong> <?= prikazi($datum) ?></p>

    <table>
        <thead>
            <tr>
                <th>Broj zahteva</th>
                <th>Podnosilac</th>
                <th>Sport</th>
                <th>Od</th>
                <th>Do</th>
                <th>Učesnika</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($termini as $termin): ?>
                <tr>
                    <td><?= prikazi($termin['broj_zahteva']) ?></td>
                    <td><?= prikazi($termin['podnosilac']) ?></td>
                    <td><?= prikazi($termin['sport']) ?></td>
                    <td><?= prikazi(substr($termin['vreme_od'], 0, 5)) ?></td>
                    <td><?= prikazi(substr($termin['vreme_do'], 0, 5)) ?></td>
                    <td><?= (int) $termin['broj_ucesnika'] ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <?php if (!$termini): ?>
        <p>Za izabrani dan nema odobrenih termina.</p>
    <?php endif; ?>

    <p><strong>Radno vreme hale:</strong> 08:00–23:00</p>
    <p><strong>Broj zauzetih termina:</strong> <?= $brojTermina ?></p>
    <p>
        <strong>Ukupno rezervisano vreme:</strong>
        <?= intdiv($ukupnoMinuta, 60) ?> h
        <?= $ukupnoMinuta % 60 ?> min
    </p>
    <p>
        <strong>Slobodno vreme:</strong>
        <?= intdiv($slobodnoMinuta, 60) ?> h
        <?= $slobodnoMinuta % 60 ?> min
    </p>
    <p>
        <strong>Zauzetost kapaciteta:</strong>
        <?= number_format($procenatZauzetosti, 1, ',', '.') ?>%
    </p>
    <p><strong>Ukupno prijavljenih učesnika:</strong> <?= $ukupnoUcesnika ?></p>
</body>
</html>