<?php

session_start();
require_once __DIR__ . '/klase/Korisnici.php';

if (isset($_SESSION['korisnik_id'])) {
    header('Location: index.php');
    exit;
}

$poruka = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $korisnickoIme = trim($_POST['korisnicko_ime'] ?? '');
    $lozinka = $_POST['lozinka'] ?? '';

    if ($korisnickoIme === '' || $lozinka === '') {
        $poruka = 'Unesi korisničko ime i lozinku.';
    } else {
        $korisnik = (new Korisnici())->pronadjiPoImenu($korisnickoIme);

        if (
            $korisnik &&
            password_verify($lozinka, $korisnik['lozinka_hash'])
        ) {
            session_regenerate_id(true);

            $_SESSION['korisnik_id'] = (int) $korisnik['id'];
            $_SESSION['korisnicko_ime'] = $korisnik['korisnicko_ime'];

            header('Location: index.php');
            exit;
        }

        $poruka = 'Korisničko ime ili lozinka nisu ispravni.';
    }
}
?>

<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="stil.css">
    <title>Prijava</title>
</head>
<body class="prijava">
    <h1>Prijava u sistem sportske hale</h1>

    <?php if ($poruka !== ''): ?>
        <p><?= htmlspecialchars($poruka, ENT_QUOTES, 'UTF-8') ?></p>
    <?php endif; ?>

    <form method="post">
        <p>
            <label>Korisničko ime:
                <input type="text" name="korisnicko_ime" required>
            </label>
        </p>

        <p>
            <label>Lozinka:
                <input type="password" name="lozinka" required>
            </label>
        </p>

        <button type="submit">Prijavi se</button>
    </form>
</body>
</html>