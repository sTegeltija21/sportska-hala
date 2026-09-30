<?php

session_start();

if (!isset($_SESSION['korisnik_id'])) {
    header('Location: prijava.php');
    exit;
}