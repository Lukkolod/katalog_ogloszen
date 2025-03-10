<?php

session_start();
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

include "../db/db_connect.php";


$id = intval($_GET['id']);
$user_id = $_SESSION['user_id'];


$sql = "SELECT user_id FROM ogloszenia WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: index.php");
    exit();
}

$ogloszenie = $result->fetch_assoc();

if ($ogloszenie['user_id'] != $user_id) {
    header("Location: index.php");
    exit();
}

$sql_images = "SELECT file_path FROM ogloszenia_zdjecia WHERE ogloszenie_id = ?";
$stmt_images = $conn->prepare($sql_images);
$stmt_images->bind_param("i", $id);
$stmt_images->execute();
$result_images = $stmt_images->get_result();

while ($image = $result_images->fetch_assoc()) {
    $file_path = $image['file_path'];
    if (file_exists($file_path)) {
        unlink($file_path);
    }
}

$sql_delete_images = "DELETE FROM ogloszenia_zdjecia WHERE ogloszenie_id = ?";
$stmt_delete_images = $conn->prepare($sql_delete_images);
$stmt_delete_images->bind_param("i", $id);
$stmt_delete_images->execute();

$sql_delete = "DELETE FROM ogloszenia WHERE id = ?";
$stmt_delete = $conn->prepare($sql_delete);
$stmt_delete->bind_param("i", $id);
$stmt_delete->execute();

header("Location: index.php");
exit();
?>


?>