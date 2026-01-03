<?php
session_start();
require '../config/db.php';

// ... (suas validações de login e inputs continuam aqui) ...

$id = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
$qtd = filter_input(INPUT_POST, 'qtd', FILTER_VALIDATE_INT);
$obs = filter_input(INPUT_POST, 'obs', FILTER_SANITIZE_SPECIAL_CHARS); // Motivo
$user_id = $_SESSION['user_id'];

// 1. Verifica estoque atual (seu código atual já faz isso)
// ...

// 2. Tira do Estoque (seu código atual já faz isso)
$stmt = $pdo->prepare("UPDATE products SET quantity = quantity - :qtd WHERE id = :id");
$stmt->execute(['qtd' => $qtd, 'id' => $id]);

// ======================================================
// 3. NOVO: GRAVA O LOG DE HISTÓRICO
// ======================================================
$sqlLog = "INSERT INTO stock_logs (product_id, user_id, type, quantity, reason) VALUES (?, ?, 'saida', ?, ?)";
$stmtLog = $pdo->prepare($sqlLog);
$stmtLog->execute([$id, $user_id, $qtd, $obs]);

// Redireciona
header("Location: ../dashboard.php?msg=sucesso");
exit;