<?php

require_once __DIR__ . '/provera_prijave.php';
require_once __DIR__ . '/klase/Zahtevi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Brisanje je dozvoljeno samo preko obrasca.');
}

$token = $_POST['csrf_token'] ?? '';
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if (
    !is_string($token) ||
    !hash_equals($_SESSION['csrf_token'] ?? '', $token) ||
    !$id
) {
    http_response_code(400);
    exit('Neispravan zahtev za brisanje.');
}

try {
    (new Zahtevi())->obrisi($id);
    header('Location: zahtevi.php');
    exit;
} catch (Throwable $greska) {
    error_log($greska->getMessage());
    http_response_code(500);
    exit('Zahtev nije moguće obrisati.');
}