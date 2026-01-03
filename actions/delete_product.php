<?php
session_start();
require '../config/db.php';

// 1. Verifica Login
if (!isset($_SESSION['user_id'])) {
    die("Acesso negado.");
}

// 2. CONTROLE DE ACESSO (RBAC) - A Barreira de Segurança
if ($_SESSION['role'] !== 'admin') {
    // Loga a tentativa de violação
    $log = $pdo->prepare("INSERT INTO audit_logs (user_id, event_type, details, ip_address) VALUES (?, 'UNAUTHORIZED_ACCESS', 'Tentou deletar sem ser admin', ?)");
    $log->execute([$_SESSION['user_id'], $_SERVER['REMOTE_ADDR']]);
    
    die("ERRO 403: Você não tem permissão para excluir itens.");
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if ($id) {
    // Deleta
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = :id");
    $stmt->execute(['id' => $id]);

    // Loga a auditoria
    $log = $pdo->prepare("INSERT INTO audit_logs (user_id, event_type, target_table, target_id, ip_address) VALUES (?, 'DELETE_PRODUCT', 'products', ?, ?)");
    $log->execute([$_SESSION['user_id'], $id, $_SERVER['REMOTE_ADDR']]);
}

header("Location: ../dashboard.php?msg=deleted");