<?php
session_start();
require 'config/db.php';
if (!isset($_SESSION['user_id'])) { header("Location: index.php"); exit; }

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$produto = null;

// Busca dados do produto (se for edição)
if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $produto = $stmt->fetch();
}

// --- NOVO: BUSCA CATEGORIAS E FORNECEDORES PARA O SELECT ---
$cats = $pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();
$sups = $pdo->query("SELECT * FROM suppliers ORDER BY name ASC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <title><?= $id ? 'Editar' : 'Novo' ?> Produto</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" type="image/png" href="icon.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
<div class="login-container" style="background: var(--bg-body);"> 
    <div class="login-card" style="max-width: 700px; text-align: left;"> <h2><?= $id ? '✏️ Editar Produto' : '📦 Novo Produto' ?></h2>
        
       <form action="actions/salvar_produto.php" method="POST">
            <input type="hidden" name="id" value="<?= $produto['id'] ?? '' ?>">

             <div style="display: flex; gap: 15px;">
                <div class="form-group" style="flex: 2;">
                    <label>Nome do Produto</label>
                    <input type="text" name="name" class="form-control" value="<?= $produto['name'] ?? '' ?>" required>
                </div>
                <div class="form-group" style="flex: 1;">
                    <label>SKU</label>
                    <input type="text" name="sku" class="form-control" value="<?= $produto['sku'] ?? '' ?>" required>
                </div>
            </div>

            <div style="display: flex; gap: 15px; margin-top: 10px;">
                <div class="form-group" style="flex: 1;">
                    <label><i class="fa-solid fa-layer-group"></i> Categoria</label>
                    <select name="category_id" class="form-control">
                        <option value="">-- Selecione --</option>
                        <?php foreach ($cats as $c): ?>
                            <option value="<?= $c['id'] ?>" 
                                <?= ($produto && $produto['category_id'] == $c['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($c['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group" style="flex: 1;">
                    <label><i class="fa-solid fa-truck"></i> Fornecedor</label>
                    <select name="supplier_id" class="form-control">
                        <option value="">-- Selecione --</option>
                        <?php foreach ($sups as $s): ?>
                            <option value="<?= $s['id'] ?>" 
                                <?= ($produto && $produto['supplier_id'] == $s['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($s['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-group" style="margin-top: 10px;">
                <label><i class="fa-solid fa-barcode"></i> Código de Barras</label>
                <input type="text" name="barcode" class="form-control" value="<?= $produto['barcode'] ?? '' ?>" placeholder="Bipe aqui...">
            </div>

            <div style="display: flex; gap: 15px; margin-top: 10px;">
                <div class="form-group" style="flex: 1;">
                    <label>Quantidade</label>
                    <input type="number" name="quantity" class="form-control" value="<?= $produto['quantity'] ?? '0' ?>" required>
                </div>
                <div class="form-group" style="flex: 1;">
                    <label>Preço (R$)</label>
                    <input type="number" step="0.01" name="price" class="form-control" value="<?= $produto['price'] ?? '0.00' ?>" required>
                </div>
            </div>

            <div style="margin-top: 20px; display: flex; gap: 10px;">
                <button type="submit" class="btn btn-primary"><?= $id ? 'Salvar Alterações' : 'Cadastrar' ?></button>
                <a href="produtos.php" class="btn btn-danger" style="text-decoration: none;">Cancelar</a>
            </div>
        </form>
    </div>
</div>
</body>
</html>