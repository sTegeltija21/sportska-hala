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
    <title>Novi zahtev</title>
</head>
<body>
    <p><a href="index.php">← Nazad na početnu</a></p>
    <h1>Zahtev za iznajmljivanje termina</h1>

    <form action="sacuvaj_zahtev.php" method="post">
        <label>Broj zahteva:
            <input type="text" name="broj_zahteva" maxlength="30" required>
        </label>
        <br><br>

        <label>Podnosilac:
            <input type="text" name="podnosilac" maxlength="100" required>
        </label>
        <br><br>

        <label>Kontakt:
            <input type="email" name="kontakt" maxlength="100" required>
        </label>
        <br><br>

        <label>Datum korišćenja:
            <input type="date" name="datum_koriscenja" required>
        </label>
        <br><br>

        <label>Vreme od:
            <input type="time" name="vreme_od"
                   min="08:00" max="23:00" required>
        </label>

        <label>Vreme do:
            <input type="time" name="vreme_do"
                   min="08:00" max="23:00" required>
        </label>
        <br><br>

        <label>Sport:
            <select name="sport_id" required>
                <option value="">Izaberi sport</option>
                <?php foreach ($sportovi as $sport): ?>
                    <option value="<?= (int) $sport['id'] ?>">
                        <?= htmlspecialchars($sport['naziv'], ENT_QUOTES, 'UTF-8') ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>

        <h2>Učesnici</h2>
        <div id="ucesnici">
            <div>
                <input type="text" name="ucesnici[]" maxlength="100"
                       placeholder="Ime i prezime" required>
            </div>
        </div>

        <p><button type="button" id="dodaj_ucesnika">Dodaj učesnika</button></p>
        <button type="submit">Sačuvaj zahtev</button>
    </form>

    <script>
        document.getElementById('dodaj_ucesnika').addEventListener('click', function () {
            const red = document.createElement('div');
            const polje = document.createElement('input');

            polje.type = 'text';
            polje.name = 'ucesnici[]';
            polje.maxLength = 100;
            polje.placeholder = 'Ime i prezime';
            polje.required = true;

            red.appendChild(polje);
            document.getElementById('ucesnici').appendChild(red);
        });
    </script>
    <script src="skripte/provera_termina.js"></script>
</body>
</html>