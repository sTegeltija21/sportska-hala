<?php

require_once __DIR__ . '/provera_prijave.php';
require_once __DIR__ . '/klase/Zahtevi.php';
require_once __DIR__ . '/klase/Sportovi.php';

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
$sportovi = (new Sportovi())->prikaziSve();

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

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
    <title>Izmena zahteva</title>
</head>
<body>
    <p><a href="detalji_zahteva.php?id=<?= $id ?>">Nazad na zahtev</a></p>
    <h1>Izmena zahteva <?= prikazi($zahtev['broj_zahteva']) ?></h1>

    <form action="sacuvaj_izmene.php" method="post">
        <input type="hidden" name="id" value="<?= $id ?>">
        <input type="hidden" name="csrf_token"
               value="<?= prikazi($_SESSION['csrf_token']) ?>">

        <p>
            <label>Broj zahteva:
                <input type="text" name="broj_zahteva" maxlength="30" required
                       value="<?= prikazi($zahtev['broj_zahteva']) ?>">
            </label>
        </p>

        <p>
            <label>Podnosilac:
                <input type="text" name="podnosilac" maxlength="100" required
                       value="<?= prikazi($zahtev['podnosilac']) ?>">
            </label>
        </p>

        <p>
            <label>Kontakt:
                <input type="email" name="kontakt" maxlength="100" required
                       value="<?= prikazi($zahtev['kontakt']) ?>">
            </label>
        </p>

        <p>
            <label>Datum korišćenja:
                <input type="date" name="datum_koriscenja" required
                       value="<?= prikazi($zahtev['datum_koriscenja']) ?>">
            </label>
        </p>

        <p>
            <label>Vreme od:
                <input type="time" name="vreme_od"
                       min="08:00" max="23:00" required
                       value="<?= prikazi(substr($zahtev['vreme_od'], 0, 5)) ?>">
            </label>

            <label>Vreme do:
                <input type="time" name="vreme_do"
                       min="08:00" max="23:00" required
                       value="<?= prikazi(substr($zahtev['vreme_do'], 0, 5)) ?>">
            </label>
        </p>

        <p>
            <label>Sport:
                <select name="sport_id" required>
                    <?php foreach ($sportovi as $sport): ?>
                        <option value="<?= (int) $sport['id'] ?>"
                            <?= (int) $sport['id'] === (int) $zahtev['sport_id']
                                ? 'selected' : '' ?>>
                            <?= prikazi($sport['naziv']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>
        </p>

        <p>
            <label>Status:
                <select name="status" required>
                    <option value="na_cekanju"
                        <?= $zahtev['status'] === 'na_cekanju' ? 'selected' : '' ?>>
                        Na čekanju
                    </option>
                    <option value="odobren"
                        <?= $zahtev['status'] === 'odobren' ? 'selected' : '' ?>>
                        Odobren
                    </option>
                    <option value="odbijen"
                        <?= $zahtev['status'] === 'odbijen' ? 'selected' : '' ?>>
                        Odbijen
                    </option>
                </select>
            </label>
        </p>

        <h2>Učesnici</h2>

        <div id="ucesnici">
            <?php foreach ($ucesnici as $ucesnik): ?>
                <div>
                    <input type="text" name="ucesnici[]" maxlength="100" required
                           value="<?= prikazi($ucesnik['ime_prezime']) ?>">
                    <button type="button" class="ukloni">Ukloni</button>
                </div>
            <?php endforeach; ?>
        </div>

        <p><button type="button" id="dodaj_ucesnika">Dodaj učesnika</button></p>
        <button type="submit">Sačuvaj izmene</button>
    </form>

    <script>
        const listaUcesnika = document.getElementById('ucesnici');

        document.getElementById('dodaj_ucesnika').addEventListener('click', function () {
            const red = document.createElement('div');
            const polje = document.createElement('input');
            const dugme = document.createElement('button');

            polje.type = 'text';
            polje.name = 'ucesnici[]';
            polje.maxLength = 100;
            polje.required = true;
            polje.placeholder = 'Ime i prezime';

            dugme.type = 'button';
            dugme.className = 'ukloni';
            dugme.textContent = 'Ukloni';

            red.append(polje, dugme);
            listaUcesnika.appendChild(red);
        });

        listaUcesnika.addEventListener('click', function (dogadjaj) {
            if (dogadjaj.target.classList.contains('ukloni')) {
                dogadjaj.target.parentElement.remove();
            }
        });
    </script>
    <script src="skripte/provera_termina.js"></script>
</body>
</html>