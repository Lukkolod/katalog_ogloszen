<?php

session_start();
include "../db/db_connect.php";


if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$query = "SELECT imie, nazwisko, adres, kod_pocztowy, miejscowosc, nip FROM uzytkownicy WHERE id = ?";
$statement = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($statement, "i", $user_id);
mysqli_stmt_execute($statement);
$result = mysqli_stmt_get_result($statement);
$user = mysqli_fetch_assoc($result);

if ($user && $user['imie'] && $user['nazwisko'] && $user['adres'] && $user['kod_pocztowy'] && $user['miejscowosc'] && $user['nip']) {
    header("Location: index.php");
    exit();
}

?>



<!DOCTYPE html>
<html lang="pl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Uzupelnij dane</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>

</head>

<body>
    <div class="h-screen w-full flex items-center justify-center flex-col" style="background: rgb(10,3,57);
background: linear-gradient(90deg, rgba(10,3,57,1) 9%, rgba(25,27,47,1) 51%, rgba(10,3,57,1) 83%);">
        <h1 class="text-3xl text-white font-bold text-center mb-8">UZUPEŁNIJ POZOSTAŁE DANE PRZED PRZEJŚCIEM DALEJ</h1>
        <div class="w-full max-w-md bg-gray-800/80 rounded-lg p-6 shadow-[0_0_15px_5px_rgba(99,102,241,0.3)] shadow-blue-600/50 inset-shadow-indigo-500/50">

            <form method="post" action="dane.php" class="text-xl flex flex-col text-white">
                <label for="imie" class="text-lg font-semibold text-gray-200">Imię:</label>
                <input type="text" name="imie" id="imie" required
                    class="bg-gray-700 text-gray-300 border-0 rounded-md p-2 w-full focus:bg-gray-600 focus:outline-none focus:ring-3 focus:ring-blue-500 transition ease-in-out duration-150">

                <label for="nazwisko" class="text-lg font-semibold text-gray-200">Nazwisko:</label>
                <input type="text" name="nazwisko" id="nazwisko" required
                    class="bg-gray-700 text-gray-300 border-0 rounded-md p-2 w-full focus:bg-gray-600 focus:outline-none focus:ring-3 focus:ring-blue-500 transition ease-in-out duration-150">

                <label for="adres" class="text-lg font-semibold text-gray-200">Adres:</label>
                <input type="text" name="adres" id="adres" required
                    class="bg-gray-700 text-gray-300 border-0 rounded-md p-2 w-full focus:bg-gray-600 focus:outline-none focus:ring-3 focus:ring-blue-500 transition ease-in-out duration-150">

                <label for="kod_pocztowy" class="text-lg font-semibold text-gray-200">Kod pocztowy:</label>
                <input type="text" name="kod_pocztowy" id="kod_pocztowy" required
                    class="bg-gray-700 text-gray-300 border-0 rounded-md p-2 w-full focus:bg-gray-600 focus:outline-none focus:ring-3 focus:ring-blue-500 transition ease-in-out duration-150">
                <label for="nr_domu" class="text-lg font-semibold text-gray-200">Nr domu:</label>
                <input type="text" name="nr_domu" id="nr_domu" required
                    class="bg-gray-700 text-gray-300 border-0 rounded-md p-2 w-full focus:bg-gray-600 focus:outline-none focus:ring-3 focus:ring-blue-500 transition ease-in-out duration-150">
                <label for="nr_lokalu" class="text-lg font-semibold text-gray-200">Nr lokalu:</label>
                <input type="text" name="nr_lokalu" id="nr_lokalu" required
                    class="bg-gray-700 text-gray-300 border-0 rounded-md p-2 w-full focus:bg-gray-600 focus:outline-none focus:ring-3 focus:ring-blue-500 transition ease-in-out duration-150">



                <label for="miejscowosc" class="text-lg font-semibold text-gray-200">Miejscowość:</label>
                <input type="text" name="miejscowosc" id="miejscowosc" required
                    class="bg-gray-700 text-gray-300 border-0 rounded-md p-2 w-full focus:bg-gray-600 focus:outline-none focus:ring-3 focus:ring-blue-500 transition ease-in-out duration-150">

                <div class="flex space-x-6 h-full m-4 items-center justify-center">
                    <label for="nip" class="text-lg font-semibold text-gray-200">NIP:</label>
                    <input type="text" name="nip" id="nip" required
                        class="bg-gray-700 text-gray-300 border-0 rounded-md p-2 focus:bg-gray-600 focus:outline-none focus:ring-3 focus:ring-blue-500 transition ease-in-out duration-150 w-36 h-8">
                    <button type="button" class="p-2 bg-blue-500 hover:bg-blue-600 text-white rounded-md transition ease-in-out duration-150 hover:scale-[1.05] font-bold  text-sm" onclick="pobierzDaneGUS()">
                        Pobierz dane z GUS
                    </button>
                </div>

                <button type="submit" class="p-4 m-2 bg-blue-500 hover:bg-blue-600 text-white rounded-md transition ease-in-out duration-150 hover:scale-[1.05] font-bold">
                    Zapisz
                </button>
            </form>
        </div>
    </div>
</body>

<script>
    const pobierzDaneGUS = async () => {
        const nip = document.getElementById("nip").value.trim();

        if (nip.length !== 10) {
            alert("Wprowadź poprawny 10-cyfrowy NIP.");
            return;
        }

        try {

            const res = await fetch(`gus.php`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded",
                },
                body: "nip=" + encodeURIComponent(nip)
            })

            if (!res.ok) {
                throw new Error("Wystąpił błąd po stronie serwera.");
            }

            const data = await res.json();

            if (data.success) {

                document.getElementById("nip").value = data.nip || '';

                document.getElementById("adres").value = data.adres.ulica || '';
                document.getElementById("nr_domu").value = data.adres.numer_nieruchomosci || '';
                document.getElementById("nr_lokalu").value = data.adres.numer_lokalu || '';
                document.getElementById("kod_pocztowy").value = data.adres.kod_pocztowy || '';
                document.getElementById("miejscowosc").value = data.adres.miejscowosc || '';

            } else {
                alert("Wystąpił problem w wczytywaniu danych z GUS: " + data.message);
            }


        } catch (err) {
            console.error("Bląd: ", err);
            alert("Wystąpił problem podczas pobierania danych.");
        }

    }
</script>

</html>