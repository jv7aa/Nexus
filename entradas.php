<?php
session_start();
require 'config/db.php';
if (!isset($_SESSION['user_id'])) { header("Location: index.php"); exit; }

// Busca a lista para o select
$stmt = $pdo->query("SELECT id, name, sku, quantity FROM products ORDER BY name ASC");
$listaProdutos = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Entrada de Estoque</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="icon" type="image/png" href="icon.png">
</head>
<body>
<div class="wrapper">
    <?php include 'includes/menu_lateral.php'; ?>

    <main class="main-content">
        <div class="header-bar">
            <h2>🚛 Registrar Entrada (Restoque)</h2>
        </div>

        <div class="card" style="background: white; padding: 30px; max-width: 600px; border-radius: 10px;">
            
            <div style="background-color: #e0f2fe; color: #0369a1; padding: 15px; border-radius: 8px; margin-bottom: 20px; border-left: 4px solid #0ea5e9;">
                <p style="margin: 0; font-size: 0.9rem;">
                    <i class="fa-solid fa-circle-info"></i> 
                    <strong>Dica:</strong> Use esta tela para adicionar itens que <em>já existem</em>. <br>
                    Se for um item inédito, <a href="produto_form.php" style="text-decoration: underline; font-weight: bold;">Cadastre o Novo Produto aqui</a> (lá você já define o estoque inicial).
                </p>
            </div>

            <form action="actions/entrada_act.php" method="POST">
                
                <div class="form-group">
                    <label>Selecione o Produto:</label>
                    <div style="display: flex; gap: 10px;">
                        <select name="product_id" class="form-control" required>
                            <option value="">-- Escolha um item existente --</option>
                            <?php foreach ($listaProdutos as $p): ?>
                                <option value="<?= $p['id'] ?>">
                                    <?= htmlspecialchars($p['name']) ?> (Atual: <?= $p['quantity'] ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        
                        <a href="produto_form.php" class="btn btn-primary" title="Cadastrar Novo Item" style="display: flex; align-items: center;">
                            <i class="fa-solid fa-plus"></i>
                        </a>
                    </div>
                </div>

                <div class="form-group" style="margin-top: 15px;">
                    <label>Quantidade a Adicionar (+):</label>
                    <input type="number" name="qtd" class="form-control" min="1" placeholder="Ex: 50" required>
                </div>

                <div class="form-group" style="margin-top: 15px;">
                    <label>Observação (Opcional):</label>
                    <input type="text" name="obs" class="form-control" placeholder="Ex: Nota Fiscal 123">
                </div>

                <button type="submit" class="btn btn-success" style="width: 100%; margin-top: 20px;">
                    <i class="fa-solid fa-check"></i> Confirmar Entrada
                </button>
            </form>
        </div>
    </main>
</div>
</body>
</html>