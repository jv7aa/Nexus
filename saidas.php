<?php
session_start();
require 'config/db.php';
if (!isset($_SESSION['user_id'])) { header("Location: index.php"); exit; }

$stmt = $pdo->query("SELECT id, name, sku, quantity FROM products ORDER BY name ASC");
$listaProdutos = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Saída de Estoque</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="icon" type="image/png" href="icon.png">
</head>
<body>
<div class="wrapper">
    <?php include 'includes/menu_lateral.php'; ?>

    <main class="main-content">
        <div class="header-bar">
            <h2>📤 Registrar Saída</h2>
        </div>
        
        <?php if(isset($_GET['error']) && $_GET['error'] == 'sem_estoque'): ?>
            <div style="background: #fee2e2; color: #b91c1c; padding: 10px; border-radius: 5px; margin-bottom: 15px;">
                Erro: Estoque insuficiente para essa operação!
            </div>
        <?php endif; ?>

        <div class="card" style="background: white; padding: 30px; max-width: 600px; border-radius: 10px;">
            <form action="actions/saidas_act.php" method="POST">
                
                <div class="form-group">
                    <label>Selecione o Produto:</label>
                    <select name="product_id" class="form-control" required>
                        <option value="">-- Escolha um item --</option>
                        <?php foreach ($listaProdutos as $p): ?>
                            <option value="<?= $p['id'] ?>">
                                <?= htmlspecialchars($p['name']) ?> (Saldo: <?= $p['quantity'] ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group" style="margin-top: 15px;">
                    <label>Quantidade a Retirar:</label>
                    <input type="number" name="qtd" class="form-control" min="1" placeholder="Ex: 10" required>
                </div>

                <div class="form-group" style="margin-top: 15px;">
                    <label>Motivo da Saída:</label>
                    <input type="text" name="obs" class="form-control" placeholder="Ex: Venda, Perda, Defeito" required>
                </div>

                <button type="submit" class="btn btn-danger" style="width: 100%; margin-top: 20px;">
                    Confirmar Saída
                </button>
            </form>
        </div>
    </main>
</div>
</body>
</html>