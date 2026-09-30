<?php
require_once __DIR__ . '/provera_prijave.php';
require_once __DIR__ . '/klase/Sportovi.php';

$sportovi = (new Sportovi())->prikaziSve();
?>

<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="stil.css">
    <title>Sportska hala</title>
</head>
<body class="pocetna">
    <h1>Sportska hala</h1>

    <p class="uvod">
        Upravljanje zahtevima za iznajmljivanje termina i pregled dnevne
        zauzetosti sportske hale.
    </p>

    <nav class="brze-veze" aria-label="Glavni meni">
        <a href="zahtevi.php">Spisak zahteva</a>
        <a href="novi_zahtev.php">Novi zahtev</a>
        <a href="dnevni_izvestaj.php">Dnevni izveštaj</a>
    </nav>

    <h2>Dostupni sportovi</h2>

    <ul>
        <?php foreach ($sportovi as $sport): ?>
            <li>
                <?= htmlspecialchars($sport['naziv'], ENT_QUOTES, 'UTF-8') ?>
            </li>
        <?php endforeach; ?>
    </ul>
</body>
</html>