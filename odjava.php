<?php

session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: zahtevi.php');
    exit;
}

$_SESSION = [];

if (ini_get('session.use_cookies')) {
    $podesavanja = session_get_cookie_params();

    setcookie(session_name(), '', [
        'expires' => time() - 3600,
        'path' => $podesavanja['path'],
        'domain' => $podesavanja['domain'],
        'secure' => $podesavanja['secure'],
        'httponly' => $podesavanja['httponly'],
        'samesite' => $podesavanja['samesite'] ?: 'Lax'
    ]);
}

session_destroy();

header('Location: prijava.php');
exit;