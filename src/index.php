<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>

</head>

<body><?php
        session_start();
        if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
            header("Location: login.php");
            exit();
        }
        ?>
    <h1>strona glowna</h1>
    <?php
    $user_id = $_SESSION['user_id'];


    include "../db/db_connect.php";

    $query = "SELECT * FROM uzytkownicy WHERE id = ?";

    $statement = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($statement, "i", $user_id);
    mysqli_stmt_execute($statement);
    $result = mysqli_stmt_get_result($statement);

    if ($row = mysqli_fetch_assoc($result)) {
        echo "<h2>Wprowadzone dane użytkownika:</h2>";
        echo "Login: " . $row['username'] . "<br/>";
        echo "email: " . $row['email'] . "<br/>";
        echo "<hr/>";

        echo "Imię: " . $row['imie'] . "<br>";
        echo "Nazwisko: " . $row['nazwisko'] . "<br>";
        echo "Adres: " . $row['adres'] . "<br>";
        echo "Kod pocztowy: " . $row['kod_pocztowy'] . "<br>";
        echo "Nr domu: " . $row['nr_domu'] . "<br>";
        echo "Nr lokalu: " . $row['nr_lokalu'] . "<br>";
        echo "Miejscowość: " . $row['miejscowosc'] . "<br>";
        echo "NIP: " . $row['nip'] . "<br>";
    } else {
        echo "Brak danych użytkownika.";
    }




    ?>

</body>


</html>