<?php
require '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

session_start();


include "../db/db_connect.php";


$message = "";
if ($_SERVER['REQUEST_METHOD'] === "POST") {



    if (empty($_POST['username']) || empty($_POST['e-mail']) || empty($_POST['password']) || empty($_POST['confirm-password'])) {
        $message = "Proszę wypełnić wszystkie pola.";
    } elseif ($_POST['password'] !== $_POST['confirm-password']) {
        $message = "Hasła muszą się zgadzać.";
    } else {

        $email = $_POST['e-mail'];
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
        $token = bin2hex(random_bytes(50));

        $query = "SELECT * FROM uzytkownicy WHERE email = ? OR username = ?";
        $statement = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($statement, "ss", $email, $username);
        mysqli_stmt_execute($statement);
        $result = mysqli_stmt_get_result($statement);


        if (mysqli_num_rows($result) > 0) {
            $message = "Uzytkownik o tym adresie e-mail lub nazwie juz istnieje";
        } else {
            $query = "INSERT INTO uzytkownicy (username, email, password, verification_token) VALUES (?, ?, ?, ?)";

            $statement = mysqli_prepare($conn, $query);

            mysqli_stmt_bind_param($statement, "ssss", $_POST['username'], $email, $password, $token);

            mysqli_stmt_execute($statement);
            mysqli_stmt_close($statement);


            $confirmationLink = "http://localhost/katalog_ogloszen/src/verify.php?email=$email&token=$token";
            $mail = new PHPMailer(true);

            try {

                $mail->isSMTP();
                $mail->SMTPAuth = true;

                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                $mail->Username = "katalog.ogloszen.test@gmail.com";
                $mail->Password = "bsziozhcagucsekw";

                $mail->setFrom('katalog.ogloszen.test@gmail.com', 'Katalog ogloszen');
                $mail->addReplyTo('katalog.ogloszen.test@gmail.com', 'Katalog ogloszen');

                $mail->addAddress($email);

                $mail->isHTML(true);
                $mail->Subject = 'Potwierdzenie rejestracji';
                $mail->Body = "Kliknij ponizszy link, aby potwierdzic rejestracje: <a href='$confirmationLink'>Potwierdzenie rejestracji</a>";
                $mail->AltBody = "Kliknij ponizszy link, aby potwierdzic rejestracje: $confirmationLink";

                if ($mail->send()) {
                    echo "Wiadomość została wysłana.";
                } else {
                    echo "blad przy wysylaniu maila";
                }
            } catch (Exception $e) {
                echo "Mailer Error: {$mail->ErrorInfo}";
            }



            $_SESSION['message'] = "Sprawdź e-mail, aby potwierdzić konto.";


            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        }

        mysqli_stmt_close($statement);
    }
}

if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
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

<body class="bg-gray-900">
    <div class="h-screen w-full flex items-center justify-center" style="background: rgb(10,3,57);
background: linear-gradient(90deg, rgba(10,3,57,1) 9%, rgba(25,27,47,1) 51%, rgba(10,3,57,1) 83%);">
        <div id="modal" class="fixed inset-0 w-full flex items-center justify-center bg-black/50 hidden">
            <div class="bg-white p-6 rounded-lg shadow-lg text-center">
                <p id="modal-message" class="text-lg font-semibold mb-4"><?php echo $message; ?></p>
                <button class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-md" onclick="closeModal()">OK</button>
            </div>
        </div>
        <div class="w-full max-w-md bg-gray-800/80 rounded-lg p-6 shadow-[0_0_15px_5px_rgba(99,102,241,0.3)] shadow-blue-600/50 inset-shadow-indigo-500/50">
            <h1 class="text-3xl text-white font-bold text-center  mb-4">STWÓRZ KONTO</h1>
            <form method="POST" class="text-xl flex flex-col text-white">
                <label for="username" class="text-2xl font-bold text-gray-200 mb-4">Nazwa użytkownika</label>
                <input
                    type="text"
                    name="username"
                    placeholder="Nazwa uzytkownika"
                    class="bg-gray-700 text-gray-300 border-0 rounded-md p-2 mb-4 focus:bg-gray-600 focus:outline-none focus:ring-3 focus:ring-blue-500 transition ease-in-out duration-150 lowercase tracking-tighter " />
                <label for="e-mail" class="text-2xl font-bold text-gray-200 mb-4">Adres e-mail</label>
                <input
                    type="email"
                    name="e-mail"
                    placeholder="Adres e-mail"
                    class="bg-gray-700 text-gray-300 border-0 rounded-md p-2 mb-4 focus:bg-gray-600 focus:outline-none focus:ring-3 focus:ring-blue-500 transition ease-in-out duration-150 lowercase tracking-tighter"  />
                <label for="password" class="text-2xl font-bold text-gray-200 mb-4">Hasło</label>
                <input
                    type="password"
                    name="password"
                    placeholder="Hasło"
                    class="bg-gray-700 text-gray-300 border-0 rounded-md p-2 mb-4 focus:bg-gray-600 focus:outline-none focus:ring-3 focus:ring-blue-500 transition ease-in-out duration-150 lowercase tracking-tighter"  />
                <label
                    for="confirm-password"
                    class="text-2xl font-bold text-gray-200 mb-4">Potwierdź hasło</label>
                <input
                    type="password"
                    name="confirm-password"
                    placeholder="Potwierdź hasło"
                    class="bg-gray-700 text-gray-300 border-0 rounded-md p-2 mb-4 focus:bg-gray-600 focus:outline-none focus:ring-3 focus:ring-blue-500 transition ease-in-out duration-150 lowercase tracking-tighter"  />
                <button
                    class="p-4 m-2 bg-blue-500 hover:bg-blue-600 text-white rounded-md transition ease-in-out duration-150 hover:scale-[1.05] font-bold">
                    Zarejestruj
                </button>
                <p class="text-sm text-gray-400 text-center mt-2">Masz juz konto? <a href="login.php" class="text-blue-400">Zaloguj</a></p>
            </form>
        </div>
    </div>
</body>

</html>