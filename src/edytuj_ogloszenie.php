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

$sql = "SELECT * FROM ogloszenia WHERE id = ? AND user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: moje_ogloszenia.php");
    exit();
}

$ogloszenie = $result->fetch_assoc();

$sql_images = "SELECT * FROM ogloszenia_zdjecia WHERE ogloszenie_id = ?";
$stmt_images = $conn->prepare($sql_images);
$stmt_images->bind_param("i", $id);
$stmt_images->execute();
$result_images = $stmt_images->get_result();
$images = $result_images->fetch_all(MYSQLI_ASSOC);

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $category = trim($_POST['mainCategory']);
    $subcategory = isset($_POST['subcategory']) ? trim($_POST['subcategory']) : null;
    $description = trim($_POST['description']);
    $price = floatval(str_replace(',', '.', $_POST['price']));

    if (empty($title) || empty($category) || empty($description) || $price <= 0) {
        $error = "Wszystkie pola są wymagane, a cena musi być większa od zera.";
    } else {
        $sql_update = "UPDATE ogloszenia SET 
                      title = ?, 
                      category = ?, 
                      subcategory = ?, 
                      description = ?, 
                      price = ? 
                      WHERE id = ? AND user_id = ?";

        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("ssssdii", $title, $category, $subcategory, $description, $price, $id, $user_id);

        if ($stmt_update->execute()) {
            if (!empty($_FILES['photos']['name'][0])) {
                $upload_dir = "../uploads/"; 

                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }

                $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
                $max_size = 5 * 1024 * 1024; 

                foreach ($_FILES['photos']['tmp_name'] as $key => $tmp_name) {
                    if ($_FILES['photos']['error'][$key] === 0) {
                        $file_type = $_FILES['photos']['type'][$key];
                        $file_size = $_FILES['photos']['size'][$key];

                        if (in_array($file_type, $allowed_types) && $file_size <= $max_size) {
                            $file_name = basename($_FILES['photos']['name'][$key]);
                            $file_path = $upload_dir . time() . "_" . $file_name; 

                            if (move_uploaded_file($tmp_name, $file_path)) {
                                $sql_img = "INSERT INTO ogloszenia_zdjecia (ogloszenie_id, file_path) VALUES (?, ?)";
                                $stmt_img = $conn->prepare($sql_img);
                                $stmt_img->bind_param("is", $id, $file_path);
                                $stmt_img->execute();
                            }
                        }
                    }
                }
            }

            if (isset($_POST['delete_images']) && is_array($_POST['delete_images'])) {
                foreach ($_POST['delete_images'] as $image_id) {
                    $sql_get_image = "SELECT file_path FROM ogloszenia_zdjecia WHERE id = ? AND ogloszenie_id = ?";
                    $stmt_get_image = $conn->prepare($sql_get_image);
                    $stmt_get_image->bind_param("ii", $image_id, $id);
                    $stmt_get_image->execute();
                    $image_result = $stmt_get_image->get_result();

                    if ($image_result->num_rows > 0) {
                        $image_data = $image_result->fetch_assoc();
                        $file_path = $image_data['file_path'];

                        $sql_delete_image = "DELETE FROM ogloszenia_zdjecia WHERE id = ? AND ogloszenie_id = ?";
                        $stmt_delete_image = $conn->prepare($sql_delete_image);
                        $stmt_delete_image->bind_param("ii", $image_id, $id);

                        if ($stmt_delete_image->execute()) {
                            if (file_exists($file_path)) {
                                unlink($file_path);
                            }
                        }
                    }
                }
            }

            $message = "Ogłoszenie zostało zaktualizowane pomyślnie.";

            $stmt_images->execute();
            $result_images = $stmt_images->get_result();
            $images = $result_images->fetch_all(MYSQLI_ASSOC);

            $stmt->execute();
            $result = $stmt->get_result();
            $ogloszenie = $result->fetch_assoc();
        } else {
            $error = "Wystąpił błąd podczas aktualizacji ogłoszenia.";
        }
    }
}

$subcategories = [
    'Motoryzacja' => ['Samochody', 'Motocykle', 'Części i akcesoria', 'Quady', 'Skutery'],
    'Nieruchomości' => ['Mieszkania na sprzedaż', 'Domy do wynajęcia', 'Działki', 'Lokale użytkowe'],
    'Elektronika' => ['Telefony', 'Laptopy', 'Telewizory', 'Komputery stacjonarne'],
    'Odzież' => ['Kurtki i płaszcze', 'T-shirty', 'Buty', 'Sukienki', 'Spodnie'],
    'Zabawki' => ['Lalki', 'Klocki LEGO', 'Zabawki edukacyjne', 'Gry planszowe']
];
?>

<!DOCTYPE html>
<html lang="pl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edytuj ogłoszenie - Katalog Ogłoszeń</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
</head>

<body class="bg-gray-100">


    <main class="container mx-auto py-8 px-4">
        <div class="bg-white rounded-lg shadow-md p-6 max-w-4xl mx-auto">
            <h2 class="text-2xl font-bold mb-6 text-gray-800">Edytuj ogłoszenie</h2>

            <?php if (!empty($message)): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4" role="alert">
                    <p><?php echo $message; ?></p>
                </div>
            <?php endif; ?>

            <?php if (!empty($error)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4" role="alert">
                    <p><?php echo $error; ?></p>
                </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <div class="mb-6">
                    <label for="title" class="block text-gray-700 font-medium mb-2">Tytuł ogłoszenia</label>
                    <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($ogloszenie['title']); ?>"
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Podaj tytuł ogłoszenia" required>
                </div>

                <label class="block text-gray-700 font-medium mb-2">Kategoria</label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div>
                        <select id="mainCategory" name="mainCategory" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Wybierz kategorię główną</option>
                            <option value="Motoryzacja" <?php echo ($ogloszenie['category'] === 'Motoryzacja') ? 'selected' : ''; ?>>Motoryzacja</option>
                            <option value="Nieruchomości" <?php echo ($ogloszenie['category'] === 'Nieruchomości') ? 'selected' : ''; ?>>Nieruchomości</option>
                            <option value="Elektronika" <?php echo ($ogloszenie['category'] === 'Elektronika') ? 'selected' : ''; ?>>Elektronika</option>
                            <option value="Odzież" <?php echo ($ogloszenie['category'] === 'Odzież') ? 'selected' : ''; ?>>Odzież</option>
                            <option value="Zabawki" <?php echo ($ogloszenie['category'] === 'Zabawki') ? 'selected' : ''; ?>>Zabawki</option>
                        </select>
                    </div>

                    <div id="subcategoryContainer" class="<?php echo empty($ogloszenie['subcategory']) ? 'hidden' : ''; ?>">
                        <select id="subcategory" name="subcategory" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Wybierz podkategorię</option>
                            <?php
                            if (!empty($ogloszenie['category']) && isset($subcategories[$ogloszenie['category']])) {
                                foreach ($subcategories[$ogloszenie['category']] as $subcat) {
                                    echo '<option value="' . htmlspecialchars($subcat) . '"' .
                                        (($ogloszenie['subcategory'] === $subcat) ? ' selected' : '') .
                                        '>' . htmlspecialchars($subcat) . '</option>';
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <div class="mb-6">
                    <label for="description" class="block text-gray-700 font-medium mb-2">Opis</label>
                    <textarea id="description" name="description" rows="6" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Opisz szczegółowo przedmiot, którego dotyczy ogłoszenie" required><?php echo htmlspecialchars($ogloszenie['description']); ?></textarea>
                </div>

                <div class="mb-6">
                    <label for="price" class="block text-gray-700 font-medium mb-2">Cena (PLN)</label>
                    <input type="number" id="price" name="price" value="<?php echo htmlspecialchars($ogloszenie['price']); ?>"
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Wpisz cenę" min="0" step="0.01" required>
                </div>

                <?php if (count($images) > 0): ?>
                    <div class="mb-6">
                        <label class="block text-gray-700 font-medium mb-2">Obecne zdjęcia</label>
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-4">
                            <?php foreach ($images as $image): ?>
                                <div class="relative">
                                    <img src="<?php echo htmlspecialchars($image['file_path']); ?>" alt="Zdjęcie ogłoszenia" class="w-full h-24 object-cover rounded">
                                    <div class="absolute top-1 right-1">
                                        <input type="checkbox" id="delete_image_<?php echo $image['id']; ?>" name="delete_images[]" value="<?php echo $image['id']; ?>" class="hidden">
                                        <label for="delete_image_<?php echo $image['id']; ?>" class="bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center cursor-pointer">&times;</label>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <p class="text-sm text-gray-500 mt-2">Zaznacz zdjęcia, które chcesz usunąć</p>
                    </div>
                <?php endif; ?>

                <div class="mb-6">
                    <label class="block text-gray-700 font-medium mb-2">Dodaj nowe zdjęcia</label>
                    <div class="border-2 border-dashed border-gray-300 rounded-md p-6 text-center">
                        <div class="mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="mx-auto h-12 w-12 text-gray-400">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <p class="text-gray-500 mb-2">Przeciągnij i upuść zdjęcia tutaj lub</p>
                        <button type="button" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md transition">Wybierz zdjęcia</button>
                        <input type="file" id="photos" name="photos[]" class="hidden" accept="image/*" multiple>
                        <p class="text-gray-400 text-sm mt-2">Maksymalnie 5 zdjęć (format JPG, PNG)</p>
                    </div>

                    <div class="mt-4 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-4 photo-preview">
                    </div>
                </div>

                <div class="flex justify-end gap-4 mt-8">
                    <a href="ogloszenie.php?id=<?php echo $id; ?>"><button type="button" class="px-6 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition">Anuluj</button></a>
                    <button type="submit" class="px-6 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition">Zapisz zmiany</button>
                </div>
            </form>
        </div>
    </main>



    <script>
        document.getElementById('mainCategory').addEventListener('change', function() {
            const category = this.value;
            const subcategorySelect = document.getElementById('subcategory');
            const subcategoryContainer = document.getElementById('subcategoryContainer');

            subcategorySelect.innerHTML = '<option value="">Wybierz podkategorię</option>';

            const subcategories = {
                'Motoryzacja': ['Samochody', 'Motocykle', 'Części i akcesoria', 'Quady', 'Skutery'],
                'Nieruchomości': ['Mieszkania na sprzedaż', 'Domy do wynajęcia', 'Działki', 'Lokale użytkowe'],
                'Elektronika': ['Telefony', 'Laptopy', 'Telewizory', 'Komputery stacjonarne'],
                'Odzież': ['Kurtki i płaszcze', 'T-shirty', 'Buty', 'Sukienki', 'Spodnie'],
                'Zabawki': ['Lalki', 'Klocki LEGO', 'Zabawki edukacyjne', 'Gry planszowe']
            };

            if (category in subcategories) {
                subcategories[category].forEach(function(subcat) {
                    const option = document.createElement('option');
                    option.value = subcat;
                    option.textContent = subcat;
                    subcategorySelect.appendChild(option);
                });
                subcategoryContainer.classList.remove('hidden');
            } else {
                subcategoryContainer.classList.add('hidden');
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            const photoInput = document.getElementById('photos');
            const photoPreview = document.querySelector('.photo-preview');
            const uploadButton = document.querySelector('button.bg-blue-500');

            uploadButton.addEventListener('click', () => {
                photoInput.click();
            });

            photoInput.addEventListener('change', function() {
                if (this.files.length > 0) {
                    Array.from(this.files).slice(0, 5).forEach((file, index) => {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            const div = document.createElement('div');
                            div.className = 'relative';
                            div.innerHTML = `
                                <img src="${e.target.result}" class="w-full h-24 object-cover rounded" />
                                <button type="button" class="absolute top-1 right-1 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center" data-index="${index}">
                                    &times;
                                </button>
                            `;
                            photoPreview.appendChild(div);
                        }
                        reader.readAsDataURL(file);
                    });
                }
            });

            photoPreview.addEventListener('click', function(e) {
                if (e.target.tagName === 'BUTTON') {
                    const button = e.target;
                    const index = button.dataset.index;

                    const imgToRemove = button.closest('div');
                    imgToRemove.remove();

                    const remainingImages = Array.from(photoPreview.querySelectorAll('button'));
                    remainingImages.forEach((button, newIndex) => {
                        button.dataset.index = newIndex;
                    });

                    const files = Array.from(photoInput.files);
                    files.splice(index, 1);

                    const newFileList = new DataTransfer();
                    files.forEach(file => newFileList.items.add(file));
                    photoInput.files = newFileList.files;
                }
            });
        });
    </script>
</body>

</html>