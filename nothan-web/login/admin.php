<?php
/**
 * login/admin.php
 *
 * Interface web légère pour gérer les utilisateurs dans login/users.json
 * - Accessible uniquement aux utilisateurs connectés et avec role === 'admin'
 * - CRUD (Create / Edit / Delete) minimal, protégé par token CSRF
 * - Réutilise lecture/écriture JSON avec flock()
 *
 * Placer ce fichier dans le même dossier que login.php (ex: /var/www/html/login/admin.php)
 * Important : garder ce répertoire sécurisé (chmod/chown appropriés).
 */

session_start();

// --- Vérifier que l'utilisateur est admin ---
if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] ?? '') !== 'admin') {
    header('Location: /index.html');
    exit();
}

// --- Configuration ---
$USERS_FILE = __DIR__ . '/users.json';

// --- Utilitaires (mêmes principes que login.php) ---
function load_users(string $path): array {
    if (!file_exists($path)) {
        return create_initial_users($path);
    }
    $fp = fopen($path, 'r');
    if (!$fp) return [];
    if (flock($fp, LOCK_SH)) {
        $contents = stream_get_contents($fp);
        flock($fp, LOCK_UN);
        fclose($fp);
        $data = json_decode($contents, true);
        return is_array($data) ? $data : [];
    } else {
        fclose($fp);
        return [];
    }
}

function save_users(string $path, array $users): bool {
    $dir = dirname($path);
    if (!is_dir($dir)) @mkdir($dir, 0755, true);
    $fp = fopen($path, 'c+');
    if (!$fp) return false;
    if (!flock($fp, LOCK_EX)) { fclose($fp); return false; }
    ftruncate($fp, 0);
    rewind($fp);
    $written = fwrite($fp, json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    fflush($fp);
    flock($fp, LOCK_UN);
    fclose($fp);
    return $written !== false;
}

function create_initial_users(string $path): array {
    $now = (new DateTime())->format(DateTime::ATOM);
    $initial_user = [
        'id' => 1,
        'username' => 'trolix',
        'password_hash' => password_hash('trotroleroi', PASSWORD_DEFAULT),
        'role' => 'admin',
        'created_at' => $now
    ];
    $users = [$initial_user];
    @save_users($path, $users);
    return $users;
}

function sanitize_username(string $u): string {
    $u = trim($u);
    $u = strip_tags($u);
    $u = preg_replace('/[^A-Za-z0-9._-]/', '', $u);
    return substr($u, 0, 32);
}

function is_valid_username(string $u): bool {
    if ($u === '') return false;
    return preg_match('/^[A-Za-z0-9._-]{3,32}$/', $u) === 1;
}

// --- CSRF token helper ---
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(24));
}
$csrf = $_SESSION['csrf_token'];

// --- Actions (add / edit / delete) ---
$action = $_POST['action'] ?? '';
$flash = '';

if ($action) {
    // vérifier token
    $token = $_POST['csrf_token'] ?? '';
    if (!hash_equals($csrf, $token)) {
        $flash = "Jeton CSRF invalide.";
    } else {
        $users = load_users($USERS_FILE);

        if ($action === 'add') {
            $u = sanitize_username($_POST['username'] ?? '');
            $pw = trim($_POST['password'] ?? '');
            $role = ($_POST['role'] ?? 'user') === 'admin' ? 'admin' : 'user';

            if (!is_valid_username($u) || $pw === '') {
                $flash = "Nom d'utilisateur invalide ou mot de passe vide.";
            } else {
                // vérifier non-existence
                $exists = false;
                foreach ($users as $x) if ($x['username'] === $u) { $exists = true; break; }
                if ($exists) {
                    $flash = "Utilisateur déjà existant.";
                } else {
                    $ids = array_column($users, 'id');
                    $next = $ids ? max($ids) + 1 : 1;
                    $user = [
                        'id' => $next,
                        'username' => $u,
                        'password_hash' => password_hash($pw, PASSWORD_DEFAULT),
                        'role' => $role,
                        'created_at' => (new DateTime())->format(DateTime::ATOM)
                    ];
                    $users[] = $user;
                    if (save_users($USERS_FILE, $users)) {
                        $flash = "Utilisateur ajouté.";
                    } else {
                        $flash = "Erreur lors de l'écriture du fichier.";
                    }
                }
            }
        }

        else if ($action === 'edit') {
            $id = intval($_POST['id'] ?? 0);
            $newrole = ($_POST['role'] ?? 'user') === 'admin' ? 'admin' : 'user';
            $newpw = trim($_POST['password'] ?? '');

            $found = false;
            foreach ($users as &$u) {
                if (intval($u['id']) === $id) {
                    $found = true;
                    $u['role'] = $newrole;
                    if ($newpw !== '') {
                        $u['password_hash'] = password_hash($newpw, PASSWORD_DEFAULT);
                    }
                    break;
                }
            }
            unset($u);
            if (!$found) {
                $flash = "Utilisateur introuvable.";
            } else {
                if (save_users($USERS_FILE, $users)) {
                    $flash = "Utilisateur modifié.";
                } else {
                    $flash = "Erreur lors de la sauvegarde.";
                }
            }
        }

        else if ($action === 'delete') {
            $id = intval($_POST['id'] ?? 0);
            $new = [];
            $found = false;
            foreach ($users as $u) {
                if (intval($u['id']) === $id) { $found = true; continue; }
                $new[] = $u;
            }
            if (!$found) {
                $flash = "Utilisateur introuvable.";
            } else {
                if (save_users($USERS_FILE, $new)) {
                    $flash = "Utilisateur supprimé.";
                } else {
                    $flash = "Erreur lors de la suppression.";
                }
            }
        }
    }
}

// --- Charge la liste (après potentielle modification) ---
$users = load_users($USERS_FILE);
if (!is_array($users)) $users = [];

?>
<!doctype html>
<html lang="fr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Admin utilisateurs — Nébuleuse</title>
<style>
    body{font-family:Arial,Helvetica,sans-serif;background:#05020a;color:#e6e1ff;padding:20px}
    .card{background:rgba(15,6,30,0.7);border:1px solid #6f3bb6;padding:16px;border-radius:8px;box-shadow:0 6px 20px rgba(111,59,182,0.12)}
    h1{margin:0 0 12px;color:#f3e8ff}
    table{width:100%;border-collapse:collapse;margin-top:12px}
    th,td{padding:8px;border-bottom:1px solid rgba(255,255,255,0.04);text-align:left}
    th{color:#d9c9ff;font-size:13px}
    .muted{color:#bfb1e6;font-size:13px}
    .btn{background:#8a47ff;color:#fff;padding:6px 10px;border-radius:6px;border:none;cursor:pointer}
    .btn-danger{background:#ff4d6d}
    .form-row{display:flex;gap:8px;align-items:center;margin-top:8px}
    input,select{padding:6px;border-radius:6px;border:1px solid rgba(255,255,255,0.06);background:transparent;color:inherit}
    .flash{margin-bottom:12px;padding:8px;border-radius:6px;background:rgba(255,255,255,0.03);color:#ffd6e8}
    .small{font-size:12px;color:#d6c9ff}
</style>
</head>
<body>
<div class="card">
    <h1>Gestion des utilisateurs</h1>
    <p class="small">Connecté en tant que <strong><?=htmlspecialchars($_SESSION['user']['username'])?></strong> (role: <?=htmlspecialchars($_SESSION['user']['role'])?>)</p>

    <?php if ($flash): ?>
        <div class="flash"><?=htmlspecialchars($flash)?></div>
    <?php endif; ?>

    <h2>Ajouter un utilisateur</h2>
    <form method="post" style="margin-bottom:12px">
        <input type="hidden" name="csrf_token" value="<?=htmlspecialchars($csrf)?>">
        <input type="hidden" name="action" value="add">
        <div class="form-row">
            <input name="username" placeholder="Nom d'utilisateur (3-32)" required>
            <input type="password" name="password" placeholder="Mot de passe" required>
            <select name="role">
                <option value="user">user</option>
                <option value="admin">admin</option>
            </select>
            <button class="btn" type="submit">Ajouter</button>
        </div>
    </form>

    <h2>Utilisateurs existants</h2>
    <table>
        <thead>
            <tr><th>id</th><th>username</th><th>role</th><th>créé</th><th>actions</th></tr>
        </thead>
        <tbody>
            <?php foreach ($users as $u): ?>
                <tr>
                    <td class="muted"><?=htmlspecialchars($u['id'] ?? '')?></td>
                    <td><?=htmlspecialchars($u['username'] ?? '')?></td>
                    <td><?=htmlspecialchars($u['role'] ?? 'user')?></td>
                    <td class="muted"><?=htmlspecialchars($u['created_at'] ?? '')?></td>
                    <td>
                        <!-- Edit form (inline) -->
                        <form method="post" style="display:inline-block">
                            <input type="hidden" name="csrf_token" value="<?=htmlspecialchars($csrf)?>">
                            <input type="hidden" name="action" value="edit">
                            <input type="hidden" name="id" value="<?=htmlspecialchars($u['id'])?>">
                            <select name="role" style="margin-right:6px">
                                <option value="user" <?=($u['role'] ?? '') === 'user' ? 'selected' : ''?>>user</option>
                                <option value="admin" <?=($u['role'] ?? '') === 'admin' ? 'selected' : ''?>>admin</option>
                            </select>
                            <input type="password" name="password" placeholder="Nouveau mot de passe (laisser vide pour garder)">
                            <button class="btn" type="submit">Modifier</button>
                        </form>

                        <!-- Delete form -->
                        <form method="post" style="display:inline-block;margin-left:6px" onsubmit="return confirm('Confirmer suppression ?')">
                            <input type="hidden" name="csrf_token" value="<?=htmlspecialchars($csrf)?>">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?=htmlspecialchars($u['id'])?>">
                            <button class="btn btn-danger" type="submit">Supprimer</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($users)): ?>
                <tr><td colspan="5" class="muted">Aucun utilisateur</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div style="margin-top:12px">
        <form method="post" action="/login/logout.php">
            <button class="btn" type="submit">Se déconnecter</button>
        </form>
    </div>
</div>
</body>
</html>
