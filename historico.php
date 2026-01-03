<?php
session_start();
require 'config/db.php';
if (!isset($_SESSION['user_id'])) { header("Location: index.php"); exit; }

// BUSCA INTELIGENTE COM JOINS
// Traz o nome do Produto e o nome do Usuário, não só os IDs
$sql = "SELECT l.*, p.name as prod_name, u.username 
        FROM stock_logs l 
        JOIN products p ON l.product_id = p.id 
        JOIN users u ON l.user_id = u.id 
        ORDER BY l.created_at DESC";
$logs = $pdo->query($sql)->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <title>Histórico | Fortress</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" type="image/png" href="icon.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
<div class="wrapper">
    <?php include 'includes/menu_lateral.php'; ?>

    <main class="main-content">
        <div class="header-bar">
            <h2>📜 Histórico de Movimentações</h2>
        </div>

        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Data/Hora</th>
                        <th>Usuário</th>
                        <th>Tipo</th>
                        <th>Produto</th>
                        <th>Qtd</th>
                        <th>Motivo</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($logs as $log): ?>
                    <tr>
                        <td style="color: #666; font-size: 0.9rem;">
                            <?= date('d/m/Y H:i', strtotime($log['created_at'])) ?>
                        </td>
                        
                        <td style="font-weight: bold;"><?= htmlspecialchars($log['username']) ?></td>
                        
                        <td>
                            <?php if($log['type'] == 'entrada'): ?>
                                <span style="background: #dcfce7; color: #166534; padding: 4px 8px; border-radius: 4px; font-size: 0.8rem; font-weight: bold;">ENTRADA</span>
                            <?php elseif($log['type'] == 'saida'): ?>
                                <span style="background: #fee2e2; color: #991b1b; padding: 4px 8px; border-radius: 4px; font-size: 0.8rem; font-weight: bold;">SAÍDA</span>
                            <?php else: ?>
                                <span style="background: #e0f2fe; color: #075985; padding: 4px 8px; border-radius: 4px; font-size: 0.8rem; font-weight: bold;">AJUSTE</span>
                            <?php endif; ?>
                        </td>

                        <td><?= htmlspecialchars($log['prod_name']) ?></td>
                        
                        <td style="font-weight: bold; font-size: 1.1rem;">
                            <?= $log['quantity'] ?>
                        </td>
                        
                        <td style="color: #555; font-style: italic;">
                            <?= htmlspecialchars($log['reason']) ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>
</body>
</html>