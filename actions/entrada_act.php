<?php
session_start();
require '../config/db.php';

$id = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
$qtd = filter_input(INPUT_POST, 'qtd', FILTER_VALIDATE_INT);
$obs = filter_input(INPUT_POST, 'obs', FILTER_SANITIZE_SPECIAL_CHARS);

if ($id && $qtd > 0) {
    // 1. Atualiza o Estoque (SOMA)
    $stmt = $pdo->prepare("UPDATE products SET quantity = quantity + :q WHERE id = :id");
    $stmt->execute(['q' => $qtd, 'id' => $id]);

    // 2. Grava Log de Auditoria
    $log = $pdo->prepare("INSERT INTO audit_logs (user_id, event_type, target_table, target_id, details, ip_address) VALUES (?, 'ENTRADA_ESTOQUE', 'products', ?, ?, ?)");
    $log->execute([$_SESSION['user_id'], $id, "Adicionou $qtd un. Obs: $obs", $_SERVER['REMOTE_ADDR']]);

    header("Location: ../dashboard.php?msg=entrada_ok");
} else {
    header("Location: ../entradas.php?error=dados_invalidos");
}
?>