<?php
// Charger la liste des utilisateurs
$users = json_decode(file_get_contents(__DIR__ . "/users.json"), true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $valid = false;
    foreach ($users as $user) {
        if ($user['username'] === $username && $user['password'] === $password) {
            $valid = true;
            break;
        }
    }

    if ($valid) {
        header("Location: /user/index.html");
        exit();
    } else {
        header("Location: /error/error.php");
        exit();
    }
} else {
    header("Location: /index.html");
    exit();
}
?>
