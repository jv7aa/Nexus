<?php
session_start();
require '../config/db.php';

// Verifica se é admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Acesso Negado.");
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if ($id) {
    // Impede deletar a si mesmo
    if ($id == $_SESSION['user_id']) {
        header("Location: ../usuarios.php?error=self_delete");
        exit;
    }
    
    // Deleta do banco
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
    $stmt->execute(['id' => $id]);
}

header("Location: ../usuarios.php?msg=deleted");
?>