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

        $user_id = $_SESSION['user_id'];


        include "../db/db_connect.php";
        ?>

    <div class="min-h-screen w-full" style="background: rgb(10,3,57);
background: linear-gradient(90deg, rgba(10,3,57,1) 9%, rgba(25,27,47,1) 51%, rgba(10,3,57,1) 83%);">

        <!-- LogOut -->
        <div class="w-fit h-fit bg-gray-200 text-black fixed top-[50%] left-[50%] translate-x-[-50%] translate-y-[-50%] flex rounded-md hidden flex-col" id="logout">
            <h1 class="mt-6 font-bold text-2xl p-2">Czy chcesz sie wylogować?</h1>
            <div class="flex items-center justify-around m-12">
                <a href="logout.php" class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-md transition duration-150">Tak</a>
                <a href="" class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-md transition duration-150">Nie</a>
            </div>
        </div>

        <!-- Header -->
        <div class="w-full h-24 bg-gray-800 flex items-center justify-between relative text-gray-300 text-3xl">
            <h1 class="font-bold tracking-widest ml-16">KATALOG OGLOSZEŃ</h1>

            <div class="flex">
                <button class="p-4 m-2 bg-blue-500 hover:bg-blue-600 text-white rounded-md transition ease-in-out duration-150 hover:scale-[1.05] font-bold text-sm mr-10">
                    <a href="ogloszenia.php"><span class="mr-1">➕</span> Stwórz nowe ogłoszenie</a>
                </button>
                <button class="p-4 m-2 bg-blue-500 hover:bg-blue-600 text-white rounded-md transition ease-in-out duration-150 hover:scale-[1.05] font-bold text-sm">
                    <a href="ogloszenia_uzytkownika.php"><span class="mr-1">👁️</span> Przegladaj swoje ogłoszenia</a>
                </button>
            </div>

            <svg id="profile" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-10 mr-16 cursor-pointer hover:text-blue-400 transition duration-150">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
            </svg>
        </div>

        <!-- NavBar -->
        <div class="w-full h-12 bg-gray-800 flex items-center justify-around relative text-gray-300 text-xl border-t-2 border-b-2 border-gray-600">
            <div class="relative category">
                <button class="px-4 py-2 hover:text-blue-400 transition duration-150">Motoryzacja</button>
                <div class="absolute left-0 mt-2 w-48 bg-gray-700 rounded-lg shadow-lg hidden dropdown z-10">
                    <a href="#" class="dropdown-item block px-4 py-2 hover:bg-gray-600" data-value="Samochody">Samochody</a>
                    <a href="#" class="dropdown-item block px-4 py-2 hover:bg-gray-600" data-value="Motocykle">Motocykle</a>
                    <a href="#" class="dropdown-item block px-4 py-2 hover:bg-gray-600" data-value="Części i akcesoria">Części i akcesoria</a>
                    <a href="#" class="dropdown-item block px-4 py-2 hover:bg-gray-600" data-value="Quady">Quady</a>
                    <a href="#" class="dropdown-item block px-4 py-2 hover:bg-gray-600" data-value="Skutery">Skutery</a>
                </div>
            </div>

            <div class="relative category">
                <button class="px-4 py-2 hover:text-blue-400 transition duration-150">Nieruchomości</button>
                <div class="absolute left-0 mt-2 w-48 bg-gray-700 rounded-lg shadow-lg hidden dropdown z-10">
                    <a href="#" class="dropdown-item block px-4 py-2 hover:bg-gray-600" data-value="Mieszkania na sprzedaż">Mieszkania na sprzedaż</a>
                    <a href="#" class="dropdown-item block px-4 py-2 hover:bg-gray-600" data-value="Domy do wynajęcia">Domy do wynajęcia</a>
                    <a href="#" class="dropdown-item block px-4 py-2 hover:bg-gray-600" data-value="Działki">Działki</a>
                    <a href="#" class="dropdown-item block px-4 py-2 hover:bg-gray-600" data-value="Lokale użytkowe">Lokale użytkowe</a>
                </div>
            </div>

            <div class="relative category">
                <button class="px-4 py-2 hover:text-blue-400 transition duration-150">Elektronika</button>
                <div class="absolute left-0 mt-2 w-48 bg-gray-700 rounded-lg shadow-lg hidden dropdown z-10">
                    <a href="#" class="dropdown-item block px-4 py-2 hover:bg-gray-600" data-value="Telefony">Telefony</a>
                    <a href="#" class="dropdown-item block px-4 py-2 hover:bg-gray-600" data-value="Laptopy">Laptopy</a>
                    <a href="#" class="dropdown-item block px-4 py-2 hover:bg-gray-600" data-value="Telewizory">Telewizory</a>
                    <a href="#" class="dropdown-item block px-4 py-2 hover:bg-gray-600" data-value="Komputery stacjonarne">Komputery stacjonarne</a>
                </div>
            </div>

            <div class="relative category">
                <button class="px-4 py-2 hover:text-blue-400 transition duration-150">Odzież</button>
                <div class="absolute left-0 mt-2 w-48 bg-gray-700 rounded-lg shadow-lg hidden dropdown z-10">
                    <a href="#" class="dropdown-item block px-4 py-2 hover:bg-gray-600" data-value="Kurtki i płaszcze">Kurtki i płaszcze</a>
                    <a href="#" class="dropdown-item block px-4 py-2 hover:bg-gray-600" data-value="T-shirty">T-shirty</a>
                    <a href="#" class="dropdown-item block px-4 py-2 hover:bg-gray-600" data-value="Buty">Buty</a>
                    <a href="#" class="dropdown-item block px-4 py-2 hover:bg-gray-600" data-value="Sukienki">Sukienki</a>
                    <a href="#" class="dropdown-item block px-4 py-2 hover:bg-gray-600" data-value="Spodnie">Spodnie</a>
                </div>
            </div>

            <div class="relative category">
                <button class="px-4 py-2 hover:text-blue-400 transition duration-150">Zabawki</button>
                <div class="absolute left-0 mt-2 w-48 bg-gray-700 rounded-lg shadow-lg hidden dropdown z-10">
                    <a href="#" class="dropdown-item block px-4 py-2 hover:bg-gray-600" data-value="Lalki">Lalki</a>
                    <a href="#" class="dropdown-item block px-4 py-2 hover:bg-gray-600" data-value="Klocki LEGO">Klocki LEGO</a>
                    <a href="#" class="dropdown-item block px-4 py-2 hover:bg-gray-600" data-value="Zabawki edukacyjne">Zabawki edukacyjne</a>
                    <a href="#" class="dropdown-item block px-4 py-2 hover:bg-gray-600" data-value="Gry planszowe">Gry planszowe</a>
                </div>
            </div>
        </div>
        <main class="w-full p-8">
            <div class="max-w-7xl mx-auto">
                <!-- Search Bar -->
                <div class="mb-8">
                    <form class="flex gap-2" method="GET">
                        <input type="text" name="search" placeholder="Szukaj ogłoszeń..." class="flex-1 p-3 rounded-md border border-gray-600 bg-gray-700 text-white" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                        <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-md transition duration-150">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                            </svg>
                        </button>
                    </form>
                </div>

                <div class="flex gap-4 mb-8">
                    <form method="GET" class="flex gap-2" id="sortForm">
                        <?php if (isset($_GET['search'])) { ?>
                            <input type="hidden" name="search" value="<?php echo htmlspecialchars($_GET['search']); ?>">
                        <?php } ?>
                        <?php if (isset($_GET['subcategory'])) { ?>
                            <input type="hidden" name="subcategory" value="<?php echo htmlspecialchars($_GET['subcategory']); ?>">
                        <?php } ?>
                        <select name="sort" class="p-2 bg-gray-700 text-white rounded-md border border-gray-600" onchange="document.getElementById('sortForm').submit()">
                            <option value="newest" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'newest') ? 'selected' : ''; ?>>Najnowsze</option>
                            <option value="price_asc" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'price_asc') ? 'selected' : ''; ?>>Cena: od najniższej</option>
                            <option value="price_desc" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'price_desc') ? 'selected' : ''; ?>>Cena: od najwyższej</option>
                        </select>
                    </form>

                    <?php if (isset($_GET['subcategory'])) { ?>
                        <div class="bg-blue-500 text-white px-4 py-2 rounded-md flex items-center">
                            <span>Filtr: <?php echo htmlspecialchars($_GET['subcategory']); ?></span>
                            <a href="?<?php
                                        $params = $_GET;
                                        unset($params['subcategory']);
                                        echo http_build_query($params);
                                        ?>" class="ml-2 hover:text-gray-200">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                </svg>
                            </a>
                        </div>
                    <?php } ?>
                </div>

                <h2 class="text-white text-2xl font-bold mb-6">
                    <?php
                    if (isset($_GET['subcategory'])) {
                        echo htmlspecialchars($_GET['subcategory']);
                    } else {
                        echo "Najnowsze ogłoszenia";
                    }
                    ?>
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php
                    $search = isset($_GET['search']) ? $_GET['search'] : '';
                    $subcategory = isset($_GET['subcategory']) ? $_GET['subcategory'] : '';
                    $sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';

                    $itemsPerPage = 9; 
                    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                    $offset = ($page - 1) * $itemsPerPage;

                    $sql = "SELECT o.*, u.username, u.miejscowosc 
                   FROM ogloszenia o 
                   JOIN uzytkownicy u ON o.user_id = u.id
                   WHERE 1=1";

                    if (!empty($search)) {
                        $sql .= " AND (o.title LIKE '%$search%' OR o.description LIKE '%$search%')";
                    }

                    if (!empty($subcategory)) {
                        $sql .= " AND o.subcategory = '$subcategory'";
                    }

                    switch ($sort) {
                        case 'price_asc':
                            $sql .= " ORDER BY o.price ASC";
                            break;
                        case 'price_desc':
                            $sql .= " ORDER BY o.price DESC";
                            break;
                        case 'newest':
                        default:
                            $sql .= " ORDER BY o.created_at DESC";
                            break;
                    }

                    $countSql = str_replace("SELECT o.*, u.username, u.miejscowosc", "SELECT COUNT(*) as total", $sql);
                    $countSql = preg_replace("/ORDER BY.*$/", "", $countSql);
                    $countResult = $conn->query($countSql);
                    $totalItems = $countResult->fetch_assoc()['total'];
                    $totalPages = ceil($totalItems / $itemsPerPage);

                    $sql .= " LIMIT $offset, $itemsPerPage";

                    $result = $conn->query($sql);

                    if ($result && $result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $ogloszenieId = $row['id'];
                            $imgSql = "SELECT file_path FROM ogloszenia_zdjecia WHERE ogloszenie_id = $ogloszenieId LIMIT 1";
                            $imgResult = $conn->query($imgSql);
                            $imgPath = ($imgResult && $imgResult->num_rows > 0)
                                ? $imgResult->fetch_assoc()['file_path']
                                : 'images/placeholder.jpg';
                    ?>
                            <div class="bg-gray-800 rounded-lg overflow-hidden shadow-lg transition duration-300 hover:shadow-xl hover:scale-[1.02]">
                                <a href="ogloszenie.php?id=<?php echo $row['id']; ?>" class="block">
                                    <div class="relative h-56 overflow-hidden">
                                        <img src="<?php echo $imgPath; ?>" alt="<?php echo htmlspecialchars($row['title']); ?>" class="w-full h-full object-cover">
                                        <div class="absolute top-0 right-0 bg-blue-500 text-white px-3 py-1 m-2 rounded-md font-bold">
                                            <?php echo number_format($row['price'], 2, ',', ' '); ?> zł
                                        </div>
                                    </div>
                                </a>

                                <div class="p-4">
                                    <a href="ogloszenie.php?id=<?php echo $row['id']; ?>" class="block">
                                        <h3 class="text-white font-bold text-xl mb-2"><?php echo htmlspecialchars($row['title']); ?></h3>
                                    </a>

                                    <div class="flex justify-between text-gray-400 text-sm mb-3">
                                        <span><?php echo htmlspecialchars($row['category']); ?> › <?php echo htmlspecialchars($row['subcategory']); ?></span>
                                        <span><?php echo htmlspecialchars($row['miejscowosc']); ?></span>
                                    </div>

                                    <p class="text-gray-300 mb-4 line-clamp-2"><?php echo htmlspecialchars(substr($row['description'], 0, 120)) . (strlen($row['description']) > 120 ? '...' : ''); ?></p>

                                    <div class="flex justify-between items-center">
                                        <span class="text-gray-400 text-sm">
                                            <?php echo date('d.m.Y', strtotime($row['created_at'])); ?>
                                        </span>
                                        <a href="ogloszenie.php?id=<?php echo $row['id']; ?>" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md transition duration-150">
                                            Zobacz więcej
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php
                        }
                    } else {
                        ?>
                        <div class="col-span-full bg-gray-800 rounded-lg p-8 text-center">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-16 h-16 mx-auto text-gray-500 mb-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.182 16.318A4.486 4.486 0 0 0 12.016 15a4.486 4.486 0 0 0-3.198 1.318M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0ZM9.75 9.75c0 .414-.168.75-.375.75S9 10.164 9 9.75 9.168 9 9.375 9s.375.336.375.75Zm-.375 0h.008v.015h-.008V9.75Zm5.625 0c0 .414-.168.75-.375.75s-.375-.336-.375-.75.168-.75.375-.75.375.336.375.75Zm-.375 0h.008v.015h-.008V9.75Z" />
                            </svg>
                            <h3 class="text-white text-xl font-bold mb-2">Brak ogłoszeń</h3>
                            <p class="text-gray-400">Nie znaleziono żadnych ogłoszeń spełniających podane kryteria.</p>
                            <a href="ogloszenia.php" class="mt-4 inline-block bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md transition duration-150">
                                Dodaj pierwsze ogłoszenie
                            </a>
                        </div>
                    <?php
                    }
                    ?>
                </div>

                <?php if ($totalPages > 1) { ?>
                    <div class="mt-8 flex justify-center">
                        <nav class="flex items-center space-x-1">
                            <?php if ($page > 1) { ?>
                                <a href="?<?php
                                            $params = $_GET;
                                            $params['page'] = $page - 1;
                                            echo http_build_query($params);
                                            ?>" class="px-3 py-2 rounded-md bg-gray-700 text-gray-300 hover:bg-gray-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                                    </svg>
                                </a>
                            <?php } ?>

                            <?php
                            $range = 2; 
                            $startPage = max(1, $page - $range);
                            $endPage = min($totalPages, $page + $range);

                            if ($startPage > 1) {
                                $params = $_GET;
                                $params['page'] = 1;
                                echo '<a href="?' . http_build_query($params) . '" class="px-4 py-2 rounded-md ' .
                                    (($page == 1) ? 'bg-blue-500 text-white' : 'bg-gray-700 text-gray-300 hover:bg-gray-600') .
                                    '">1</a>';

                                if ($startPage > 2) {
                                    echo '<span class="px-4 py-2 text-gray-400">...</span>';
                                }
                            }

                            for ($i = $startPage; $i <= $endPage; $i++) {
                                if ($i == 1 || $i == $totalPages) continue; 

                                $params = $_GET;
                                $params['page'] = $i;
                                echo '<a href="?' . http_build_query($params) . '" class="px-4 py-2 rounded-md ' .
                                    (($page == $i) ? 'bg-blue-500 text-white' : 'bg-gray-700 text-gray-300 hover:bg-gray-600') .
                                    '">' . $i . '</a>';
                            }

                            if ($endPage < $totalPages) {
                                if ($endPage < $totalPages - 1) {
                                    echo '<span class="px-4 py-2 text-gray-400">...</span>';
                                }

                                $params = $_GET;
                                $params['page'] = $totalPages;
                                echo '<a href="?' . http_build_query($params) . '" class="px-4 py-2 rounded-md ' .
                                    (($page == $totalPages) ? 'bg-blue-500 text-white' : 'bg-gray-700 text-gray-300 hover:bg-gray-600') .
                                    '">' . $totalPages . '</a>';
                            }
                            ?>

                            <?php if ($page < $totalPages) { ?>
                                <a href="?<?php
                                            $params = $_GET;
                                            $params['page'] = $page + 1;
                                            echo http_build_query($params);
                                            ?>" class="px-3 py-2 rounded-md bg-gray-700 text-gray-300 hover:bg-gray-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                                    </svg>
                                </a>
                            <?php } ?>
                        </nav>
                    </div>
                <?php } ?>
            </div>
        </main>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const dropdownItems = document.querySelectorAll('.dropdown-item');

            dropdownItems.forEach(item => {
                item.addEventListener('click', (event) => {
                    event.preventDefault();

                    const selectedValue = item.getAttribute('data-value');

                    let currentUrl = new URL(window.location.href);
                    let params = new URLSearchParams(currentUrl.search);

                    params.set('subcategory', selectedValue);

                    if (params.has('page')) params.delete('page'); 

                    window.location.href = `${currentUrl.pathname}?${params.toString()}`;
                });
            });
        });



        document.getElementById("profile").addEventListener("click", () => {
            document.getElementById("logout").classList.toggle("hidden");
        })

        document.addEventListener('DOMContentLoaded', function() {
            const categories = document.querySelectorAll('.category');
            const dropdowns = document.querySelectorAll('.dropdown');
            const dropdownItems = document.querySelectorAll('.dropdown-item');

            categories.forEach((category, index) => {
                category.addEventListener('click', (event) => {
                    dropdowns.forEach(dropdown => {
                        if (!dropdown.classList.contains('hidden')) {
                            dropdown.classList.add('hidden');
                        }
                    });

                    dropdowns[index].classList.toggle('hidden');

                    event.stopPropagation();
                });
            });

            dropdownItems.forEach(item => {
                item.addEventListener('click', (event) => {
                    event.preventDefault();


                    selectedValue = item.getAttribute('data-value');


                    dropdownItems.forEach(i => i.classList.remove('text-blue-500'));
                    item.classList.add('text-blue-500');

                    // console.log('Wybrana opcja:', selectedValue);


                    dropdowns.forEach(dropdown => dropdown.classList.add('hidden'));
                });
            });


            document.addEventListener('click', (event) => {
                let isClickInside = false;

                categories.forEach(category => {
                    if (category.contains(event.target)) {
                        isClickInside = true;
                    }
                });

                dropdowns.forEach(dropdown => {
                    if (dropdown.contains(event.target)) {
                        isClickInside = true;
                    }
                });

                if (!isClickInside) {
                    dropdowns.forEach(dropdown => {
                        dropdown.classList.add('hidden');
                    });
                }
            });
        });
    </script>
</body>

</html>