<?php
session_start();
require_once '../vendor/autoload.php';
include "../db/db_connect.php";

if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $imie = trim($_POST['imie']);
    $nazwisko = trim($_POST['nazwisko']);
    $adres = trim($_POST['adres']);
    $kod_pocztowy = trim($_POST['kod_pocztowy']);
    $nr_domu = trim($_POST['nr_domu']);
    $nr_lokalu = trim($_POST['nr_lokalu']);
    $miejscowosc = trim($_POST['miejscowosc']);
    $nip = trim($_POST['nip']);

    if (empty($imie) || empty($nazwisko) || empty($adres) || empty($kod_pocztowy) || empty($nr_domu) || empty($miejscowosc) || empty($nip)) {
        die("Wszystkie pola są wymagane.");
    }


    $query = "UPDATE uzytkownicy 
    SET imie = ?, nazwisko = ?, adres = ?, nr_domu = ?, nr_lokalu = ?, kod_pocztowy = ?, miejscowosc = ?, nip = ? 
    WHERE id = ?";
    $statement = mysqli_prepare($conn, $query);

    if (!$statement) {
        die("Błąd zapytania: " . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($statement, "ssssssssi", $imie, $nazwisko, $adres, $nr_domu, $nr_lokalu, $kod_pocztowy, $miejscowosc, $nip, $user_id);

    if (mysqli_stmt_execute($statement)) {
        header("Location: index.php");
        exit();
    } else {
        die("Błąd zapisu danych: " . mysqli_stmt_error($statement));
    }
}
