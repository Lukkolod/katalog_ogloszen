<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
</head>

<body class="bg-gray-100">
    <?php
    session_start();
    if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }

    $user_id = $_SESSION['user_id'];

    include "../db/db_connect.php";
    ?>

    <main class="container mx-auto py-8 px-4">
        <div class="bg-white rounded-lg shadow-md p-6 max-w-4xl mx-auto">
            <h2 class="text-2xl font-bold mb-6 text-gray-800">Stwórz nowe ogłoszenie</h2>

            <form action="dodaj_ogloszenie.php" method="POST" enctype="multipart/form-data">
                <div class="mb-6">
                    <label for="title" class="block text-gray-700 font-medium mb-2">Tytuł ogłoszenia</label>
                    <input type="text" id="title" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Podaj tytuł ogłoszenia" required name="title">
                </div>

                <label class="block text-gray-700 font-medium mb-2">Kategoria</label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <select id="mainCategory" name="mainCategory" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Wybierz kategorię główną</option>
                            <option value="Motoryzacja">Motoryzacja</option>
                            <option value="Nieruchomości">Nieruchomości</option>
                            <option value="Elektronika">Elektronika</option>
                            <option value="Odzież">Odzież</option>
                            <option value="Zabawki">Zabawki</option>
                        </select>
                    </div>

                    <div id="subcategoryContainer" class="hidden">
                        <select id="subcategory" name="subcategory" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Wybierz podkategorię</option>
                        </select>
                    </div>
                </div>

                <div class="mb-6">
                    <label for="description" class="block text-gray-700 font-medium mb-2">Opis</label>
                    <textarea id="description" rows="6" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Opisz szczegółowo przedmiot, którego dotyczy ogłoszenie" required name="description"></textarea>
                </div>

                <div class="mb-6">
                    <label for="price" class="block text-gray-700 font-medium mb-2">Cena (PLN)</label>
                    <input type="number" id="price" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Wpisz cenę" min="0" step="0.01" required name="price">
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 font-medium mb-2">Zdjęcia</label>
                    <div class="border-2 border-dashed border-gray-300 rounded-md p-6 text-center">
                        <div class="mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="mx-auto h-12 w-12 text-gray-400">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <p class="text-gray-500 mb-2">Przeciągnij i upuść zdjęcia tutaj lub</p>
                        <button type="button" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md transition">Wybierz zdjęcia</button>
                        <input type="file" id="photos" class="hidden" accept="image/*" name="photos[]" multiple>
                        <p class="text-gray-400 text-sm mt-2">Maksymalnie 5 zdjęć (format JPG, PNG)</p>
                    </div>

                    <div class="mt-4 grid grid-cols-5 gap-4 photo-preview">
                    </div>
                </div>

                <div class="flex justify-end gap-4 mt-8">
                    <a href="index.php"><button type="button" class="px-6 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition">Anuluj</button></a>
                    <button type="submit" class="px-6 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition">Opublikuj ogłoszenie</button>
                </div>
            </form>
        </div>
    </main>

    <script>
        const mainCategorySelect = document.getElementById('mainCategory');
        const subcategoryContainer = document.getElementById('subcategoryContainer');
        const subcategorySelect = document.getElementById('subcategory');

        const subcategories = {
            'Motoryzacja': ['Samochody', 'Motocykle', 'Części i akcesoria', 'Quady', 'Skutery'],
            'Nieruchomości': ['Mieszkania na sprzedaż', 'Domy do wynajęcia', 'Działki', 'Lokale użytkowe'],
            'Elektronika': ['Telefony', 'Laptopy', 'Telewizory', 'Komputery stacjonarne'],
            'Odzież': ['Kurtki i płaszcze', 'T-shirty', 'Buty', 'Sukienki', 'Spodnie'],
            'Zabawki': ['Lalki', 'Klocki LEGO', 'Zabawki edukacyjne', 'Gry planszowe']
        };

        mainCategorySelect.addEventListener('change', function() {
            const selectedCategory = this.value;

            if (selectedCategory && subcategories[selectedCategory]) {
                subcategorySelect.innerHTML = '<option value="">Wybierz podkategorię</option>';

                subcategories[selectedCategory].forEach(sub => {
                    const option = document.createElement('option');
                    option.value = sub;
                    option.textContent = sub;
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
            photoPreview.innerHTML = '';

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