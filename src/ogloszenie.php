<?php
session_start();
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

include "../db/db_connect.php";

$id = intval($_GET['id']);

$sql = "SELECT o.*, u.username, u.email, u.imie, u.nazwisko, u.miejscowosc 
FROM ogloszenia o 
JOIN uzytkownicy u ON o.user_id = u.id 
WHERE o.id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: index.php");
    exit();
}

$ogloszenie = $result->fetch_assoc();

$sql_images = "SELECT * FROM ogloszenia_zdjecia WHERE ogloszenie_id = ?";
$stmt_images = $conn->prepare($sql_images);
$stmt_images->bind_param("i", $id);
$stmt_images->execute();
$result_images = $stmt_images->get_result();
$images = $result_images->fetch_all(MYSQLI_ASSOC);
?>

<html lang="pl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($ogloszenie['title']); ?> - Katalog Ogłoszeń</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
    <nav class="bg-white shadow-md">
        <div class="container mx-auto px-4 py-4 flex justify-between items-center">
            <a href="index.php" class="text-2xl font-bold text-blue-600">Katalog Ogłoszeń</a>
        </div>
    </nav>

    <div class="container mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="bg-blue-500 text-white p-4">
                <h1 class="text-2xl font-bold"><?php echo htmlspecialchars($ogloszenie['title']); ?></h1>
                <div class="flex justify-between items-center mt-2">
                    <div>
                        <p class="text-sm">Kategoria: <?php echo htmlspecialchars($ogloszenie['category']); ?>
                            <?php if (!empty($ogloszenie['subcategory'])): ?>
                                > <?php echo htmlspecialchars($ogloszenie['subcategory']); ?>
                            <?php endif; ?>
                        </p>
                        <p class="text-sm">Dodano: <?php echo date('d.m.Y H:i', strtotime($ogloszenie['created_at'])); ?></p>
                    </div>
                    <div class="text-2xl font-bold"><?php echo number_format($ogloszenie['price'], 2, ',', ' '); ?> zł</div>
                </div>
            </div>

            <div class="p-4">
                <?php if (count($images) > 0): ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
                        <?php foreach ($images as $image): ?>
                            <div class="rounded overflow-hidden group">
                                <img src="<?php echo htmlspecialchars($image['file_path']); ?>" alt="Zdjęcie ogłoszenia" class="w-full h-64 object-cover group-hover:scale-125 group-hover:object-contain transition-all duration-1000 ease-in-out">
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="bg-gray-200 h-64 flex items-center justify-center mb-6">
                        <p class="text-gray-500">Brak zdjęć</p>
                    </div>
                <?php endif; ?>

                <div class="mb-6">
                    <h2 class="text-xl font-bold mb-2">Opis</h2>
                    <div class="prose max-w-none">
                        <?php echo nl2br(htmlspecialchars($ogloszenie['description'])); ?>
                    </div>
                </div>

                <div class="border-t pt-4">
                    <h2 class="text-xl font-bold mb-2">Informacje o sprzedającym</h2>
                    <div class="bg-gray-50 p-4 rounded">
                        <p><strong>Nazwa użytkownika:</strong> <?php echo htmlspecialchars($ogloszenie['username']); ?></p>
                        <p><strong>Kontakt:</strong> <?php echo htmlspecialchars($ogloszenie['email']); ?></p>
                        <?php if (!empty($ogloszenie['imie']) && !empty($ogloszenie['nazwisko'])): ?>
                            <p><strong>Imię i nazwisko:</strong> <?php echo htmlspecialchars($ogloszenie['imie']) . ' ' . htmlspecialchars($ogloszenie['nazwisko']); ?></p>
                        <?php endif; ?>
                        <?php if (!empty($ogloszenie['miejscowosc'])): ?>
                            <p><strong>Lokalizacja:</strong> <?php echo htmlspecialchars($ogloszenie['miejscowosc']); ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="flex justify-between mt-6">
                    <a href="index.php" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md transition duration-150">
                        Powrót do ogłoszeń
                    </a>

                    <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $ogloszenie['user_id']): ?>
                        <div class="space-x-2">
                            <a href="edytuj_ogloszenie.php?id=<?php echo $ogloszenie['id']; ?>" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-md transition duration-150">
                                Edytuj
                            </a>
                            <a href="usun_ogloszenie.php?id=<?php echo $ogloszenie['id']; ?>" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-md transition duration-150" onclick="return confirm('Czy na pewno chcesz usunąć to ogłoszenie?')">
                                Usuń
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>    
</body>

</html>