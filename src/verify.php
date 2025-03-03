<?php

session_start();
include "../db/db_connect.php";

if (isset($_GET["email"]) && isset($_GET["token"])) {
    $email = $_GET["email"];
    $token = $_GET["token"];

    $query = "SELECT * FROM uzytkownicy WHERE email = ? AND verification_token = ?";


    $statement = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($statement, "ss", $email, $token);
    mysqli_stmt_execute($statement);
    $result = mysqli_stmt_get_result($statement);

    if (mysqli_num_rows($result) > 0) {
        $updateQuery = "UPDATE uzytkownicy SET active = 1 WHERE email = ? AND verification_token = ?";
        $updateStatement = mysqli_prepare($conn, $updateQuery);
        mysqli_stmt_bind_param($updateStatement, "ss", $email, $token);
        mysqli_stmt_execute($updateStatement);

        header("Location: login.php");
        exit();
    }
}
?>
