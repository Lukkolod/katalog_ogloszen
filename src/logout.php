<?php
session_start();

// Unieważnij sesję, aby wylogować użytkownika
session_unset();
session_destroy();

// Przekieruj do strony logowania
header("Location: login.php");
exit();
