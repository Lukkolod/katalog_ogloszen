<?php
session_start();
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include "../db/db_connect.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $title = $_POST['title'];
    $category = $_POST['mainCategory'];
    $subcategory = isset($_POST['subcategory']) ? $_POST['subcategory'] : NULL;
    $description = $_POST['description'];
    $price = $_POST['price'];
    
    $stmt = $conn->prepare("INSERT INTO ogloszenia (user_id, title, category, subcategory, description, price) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issssd", $user_id, $title, $category, $subcategory, $description, $price);
    $stmt->execute();
    $ogloszenie_id = $stmt->insert_id;
    $stmt->close();
    
    if (!empty($_FILES['photos']['name'][0])) {
        $upload_dir = "../uploads/";
        foreach ($_FILES['photos']['tmp_name'] as $key => $tmp_name) {
            $file_name = basename($_FILES['photos']['name'][$key]);
            $file_path = $upload_dir . time() . "_" . $file_name;
            
            if (move_uploaded_file($tmp_name, $file_path)) {
                $stmt = $conn->prepare("INSERT INTO ogloszenia_zdjecia (ogloszenie_id, file_path) VALUES (?, ?)");
                $stmt->bind_param("is", $ogloszenie_id, $file_path);
                $stmt->execute();
                $stmt->close();
            }
        }
    }
    
    $conn->close();
    header("Location: index.php");
    exit();
} else {
    header("Location: index.php");
    exit();
}
?>
