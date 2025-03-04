<?php

session_start();
$message = "";

include "../db/db_connect.php";

if ($_SERVER['REQUEST_METHOD'] === "POST") {

    if (empty($_POST['username']) || empty($_POST['password'])) {
        $message = "Proszę wypełnić wszystkie pola.";
    } else {

        $username = $_POST['username'];
        $password = $_POST['password'];

        $query = "SELECT * FROM uzytkownicy WHERE username = ?";
        $statement = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($statement, "s", $username);
        mysqli_stmt_execute($statement);
        $result = mysqli_stmt_get_result($statement);
        $user = mysqli_fetch_assoc($result);

        if ($user) {
            if ($user['active'] == 1) {
                if (password_verify($password, $user['password'])) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    header("Location: profil.php");
                    exit();
                } else {
                    $message = "Nieprawidłowe hasło.";
                }
            } else {
                $message = "Konto nie zostało jeszcze aktywowane. Sprawdź swój e-mail.";
            }
        } else {
            $message = "Użytkownik o tym loginie nie istnieje.";
        }
    }
}



?>

<!DOCTYPE html>
<html lang="pl">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Katalog ogloszen</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <script>
        const closeModal = () => {
            document.getElementById("modal").classList.add("hidden");

        }
        const showModal = () => {
            document.getElementById("modal").classList.remove("hidden");

        };

        window.onload = function() {
            <?php if (!empty($message)): ?>
                showModal();
            <?php endif; ?>
        };
    </script>
</head>

<body>
    <div class="h-screen w-full flex items-center justify-center" style="background: rgb(10,3,57);
background: linear-gradient(90deg, rgba(10,3,57,1) 9%, rgba(25,27,47,1) 51%, rgba(10,3,57,1) 83%);">
        <div id="modal" class="fixed inset-0 w-full flex items-center justify-center bg-black/50 hidden">
            <div class="bg-white p-6 rounded-lg shadow-lg text-center">
                <p id="modal-message" class="text-lg font-semibold mb-4"><?php echo $message; ?></p>
                <button class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-md" onclick="closeModal()">OK</button>
            </div>
        </div>
        <div class="w-full max-w-md bg-gray-800 rounded-lg p-6 shadow-[0_0_15px_5px_rgba(99,102,241,0.3)] shadow-blue-600/50 inset-shadow-indigo-500/50">
            <form method="POST" class="text-xl flex flex-col text-white">
                <h1 class="text-3xl text-white font-bold text-center mb-4">
                    ZALOGUJ SIĘ
                </h1>
                <label for="username" class="text-2xl font-bold text-gray-200 mb-4">Nazwa użytkownika</label>
                <input
                    type="text"
                    name="username"
                    placeholder="Nazwa uzytkownika"
                    class="bg-gray-700 text-gray-300 border-0 rounded-md p-2 mb-4 focus:bg-gray-600 focus:outline-none focus:ring-3 focus:ring-blue-500 transition ease-in-out duration-150 lowercase" />
                <label for="password" class="text-2xl font-bold text-gray-200 mb-4">Hasło</label>
                <input
                    type="password"
                    name="password"
                    placeholder="Hasło"
                    class="bg-gray-700 text-gray-300 border-0 rounded-md p-2 mb-4 focus:bg-gray-600 focus:outline-none focus:ring-3 focus:ring-blue-500 transition ease-in-out duration-150 lowercase" />

                <button
                    class="p-4 m-2 bg-blue-500 hover:bg-blue-600 text-white rounded-md transition ease-in-out duration-150 hover:scale-[1.05] font-bold">
                    Zaloguj
                </button>
                <p class="text-sm text-gray-400 text-center mt-2">
                    Nie masz konta?
                    <a href="register.php" class="text-blue-400">Stwórz</a>
                </p>
            </form>
        </div>
    </div>
</body>

</html>