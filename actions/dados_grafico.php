<?php
// actions/dados_grafico.php
require '../config/db.php'; // Ajuste o caminho se necessário

// Busca os Top 5 produtos
$stmt = $pdo->query("SELECT name, quantity FROM products ORDER BY quantity DESC LIMIT 5");
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

$labels = [];
$values = [];

foreach ($data as $row) {
    // Corta nomes longos
    $labels[] = mb_strimwidth($row['name'], 0, 15, "...");
    $values[] = $row['quantity'];
}

// Retorna JSON puro para o Javascript ler
header('Content-Type: application/json');
echo json_encode(['labels' => $labels, 'data' => $values]);
?>