<?php
session_start();
require 'config/db.php';

// Segurança
if (!isset($_SESSION['user_id'])) { header("Location: index.php"); exit; }

// === LÓGICA DE FILTRO ===
$filtro = filter_input(INPUT_GET, 'filtro', FILTER_SANITIZE_SPECIAL_CHARS);

if ($filtro === 'baixo') {
    // SE FILTRO ATIVO: Busca só o que tem menos de 10 unidades
    $titulo_pagina = "⚠️ Itens com Estoque Baixo";
    $sql = "SELECT * FROM products WHERE quantity < 10 ORDER BY quantity ASC";
} else {
    // PADRÃO: Busca tudo
    $titulo_pagina = "📦 Lista de Produtos";
    $sql = "SELECT * FROM products ORDER BY id DESC";
}

$stmt = $pdo->query($sql);
$products = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Produtos | Fortress</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="icon" type="image/png" href="icon.png">
</head>
<body>

<div class="wrapper">
    <?php include 'includes/menu_lateral.php'; ?>
    
    <main class="main-content">
        <div class="header-bar">
            <h2 style="<?= $filtro === 'baixo' ? 'color: #ef4444;' : '' ?>">
                <?= $titulo_pagina ?>
            </h2>
            
            <div style="display: flex; gap: 10px;">
                <?php if ($filtro === 'baixo'): ?>
                    <a href="produtos.php" class="btn btn-secondary" style="background-color: #6b7280; color: white;">
                        <i class="fa-solid fa-filter-circle-xmark"></i> Ver Todos
                    </a>
                <?php endif; ?>

                <a href="produto_form.php" class="btn btn-primary">
                    <i class="fa-solid fa-plus"></i> Novo Item
                </a>
            </div>
        </div>

        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>SKU</th>
                        <th>Nome</th>
                        <th>Qtd Atual</th>
                        <th>Preço</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($products) > 0): ?>
                        <?php foreach ($products as $p): ?>
                        <tr>
                            <td>#<?= htmlspecialchars($p['sku']) ?></td>
                            <td><?= htmlspecialchars($p['name']) ?></td>
                            
                            <td style="font-weight: bold; <?= $p['quantity'] < 10 ? 'color: red;' : '' ?>">
                                <?= $p['quantity'] ?>
                            </td>
                            
                            <td>R$ <?= number_format($p['price'], 2, ',', '.') ?></td>
                            
                            <td>
                                <?php if($p['quantity'] == 0): ?>
                                    <span style="color: #ef4444; font-weight: bold; font-size: 0.8rem;">ESGOTADO</span>
                                <?php elseif($p['quantity'] < 10): ?>
                                    <span style="color: #f59e0b; font-weight: bold; font-size: 0.8rem;">BAIXO</span>
                                <?php else: ?>
                                    <span style="color: #10b981; font-weight: bold; font-size: 0.8rem;">OK</span>
                                <?php endif; ?>
                            </td>

                            <td>
                                <a href="produto_form.php?id=<?= $p['id'] ?>" class="btn btn-primary" style="padding: 5px 10px; font-size: 12px;">
                                    <i class="fa-solid fa-pen"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 20px; color: #666;">
                                <?php if($filtro === 'baixo'): ?>
                                    <i class="fa-solid fa-check-circle" style="color: green; font-size: 20px;"></i><br>
                                    Nenhum produto com estoque baixo! Tudo em ordem.
                                <?php else: ?>
                                    Nenhum produto cadastrado.
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>

</body>
</html>