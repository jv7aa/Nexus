<?php
session_start();
require '../config/db.php';

// Recebe dados e limpa
$user_input = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_SPECIAL_CHARS);
$pass_input = $_POST['password'];

if ($user_input && $pass_input) {
    // 1. Busca o usuário pelo Username
    $stmt = $pdo->prepare("SELECT id, username, password_hash, role FROM users WHERE username = :u");
    $stmt->execute(['u' => $user_input]);
    $user = $stmt->fetch();

    // 2. Verifica a senha (Hash vs Texto Puro)
    if ($user && password_verify($pass_input, $user['password_hash'])) {
        
        // 3. Login Sucesso: Cria Sessão
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        
        // 4. Auditoria: Grava o log de sucesso
        $logStmt = $pdo->prepare("INSERT INTO audit_logs (user_id, event_type, ip_address) VALUES (?, 'LOGIN_SUCCESS', ?)");
        $logStmt->execute([$user['id'], $_SERVER['REMOTE_ADDR']]);

        header("Location: ../dashboard.php");
        exit;
    } else {
        // Falha: Loga a tentativa (Sem ID de usuário, pois falhou)
        $logStmt = $pdo->prepare("INSERT INTO audit_logs (event_type, details, ip_address) VALUES ('LOGIN_FAIL', ?, ?)");
        $logStmt->execute(["Tentativa user: $user_input", $_SERVER['REMOTE_ADDR']]);
        
        header("Location: ../index.php?error=credenciais");
        exit;
    }
}
?>