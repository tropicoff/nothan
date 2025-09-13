<?php
$usersFile = __DIR__ . "/users.json";

// Charger les utilisateurs
$users = file_exists($usersFile) ? json_decode(file_get_contents($usersFile), true) : [];

// Ajouter un utilisateur
if (isset($_POST['add'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    if ($username && $password) {
        $users[] = ["username" => $username, "password" => $password];
        file_put_contents($usersFile, json_encode($users, JSON_PRETTY_PRINT));
    }
    header("Location: manage_users.php");
    exit();
}

// Modifier un utilisateur
if (isset($_POST['edit'])) {
    $index = intval($_POST['index']);
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    if (isset($users[$index]) && $username && $password) {
        $users[$index] = ["username" => $username, "password" => $password];
        file_put_contents($usersFile, json_encode($users, JSON_PRETTY_PRINT));
    }
    header("Location: manage_users.php");
    exit();
}

// Supprimer un utilisateur
if (isset($_GET['delete'])) {
    $index = intval($_GET['delete']);
    if (isset($users[$index])) {
        unset($users[$index]);
        $users = array_values($users);
        file_put_contents($usersFile, json_encode($users, JSON_PRETTY_PRINT));
    }
    header("Location: manage_users.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des utilisateurs</title>
    <style>
        body {
            font-family: 'Orbitron', Arial, sans-serif;
            background: #0b0b0b;
            color: #00ff99;
            padding: 20px;
        }
        h1 { color: #00ff99; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #00ff99;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        input[type="text"] {
            background: #222;
            color: #00ff99;
            border: none;
            border-radius: 4px;
            padding: 5px;
        }
        button {
            background: #00ff99;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
            color: #000;
            font-weight: bold;
        }
        button:hover {
            background: #00cc77;
        }
        a {
            color: #ff5555;
            text-decoration: none;
        }
        a:hover { text-decoration: underline; }
        .back-button { margin-bottom: 20px; }
    </style>
</head>
<body>
    <h1>Gestion des utilisateurs</h1>
    <a href="/user/index.html"><button class="back-button">← Retour au menu</button></a>

    <table>
        <tr>
            <th>#</th>
            <th>Nom d'utilisateur</th>
            <th>Mot de passe</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($users as $i => $user): ?>
        <tr>
            <form method="post">
                <td><?= $i ?></td>
                <td><input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required></td>
                <td><input type="text" name="password" value="<?= htmlspecialchars($user['password']) ?>" required></td>
                <td>
                    <input type="hidden" name="index" value="<?= $i ?>">
                    <button type="submit" name="edit">Modifier</button>
                    <a href="?delete=<?= $i ?>" onclick="return confirm('Supprimer cet utilisateur ?')">Supprimer</a>
                </td>
            </form>
        </tr>
        <?php endforeach; ?>
    </table>

    <h2>Ajouter un utilisateur</h2>
    <form method="post">
        <input type="text" name="username" placeholder="Nom d'utilisateur" required>
        <input type="text" name="password" placeholder="Mot de passe" required>
        <button type="submit" name="add">Ajouter</button>
    </form>
</body>
</html>
