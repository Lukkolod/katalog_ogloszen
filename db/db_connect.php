<?php

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../'); 
$dotenv->load();

$db_password = $_ENV['DB_PASSWORD'] ?? null;

$server = 'sql313.infinityfree.com';
$user = 'if0_38451400';
$password = $db_password;
$db = 'if0_38451400_katalog_ogloszen';

$conn = mysqli_connect($server, $user, $password, $db) or die("Błąd połączenia: " . mysqli_connect_error());




?>
