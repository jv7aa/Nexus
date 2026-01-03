<?php
session_start();
require '../config/db.php';

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$name = filter_input(INPUT_POST, 'name');
$sku = filter_input(INPUT_POST, 'sku');
$barcode = filter_input(INPUT_POST, 'barcode');
$qty = filter_input(INPUT_POST, 'quantity');
$price = filter_input(INPUT_POST, 'price');

// --- NOVOS CAMPOS ---
// Se não selecionar nada, salva como NULL
$cat_id = filter_input(INPUT_POST, 'category_id', FILTER_VALIDATE_INT) ?: null;
$sup_id = filter_input(INPUT_POST, 'supplier_id', FILTER_VALIDATE_INT) ?: null;

// Verifica duplicidade de Barcode (igual já tínhamos)
if ($barcode) {
    $sqlCheck = "SELECT id FROM products WHERE barcode = :b AND id != :id";
    $stmtCheck = $pdo->prepare($sqlCheck);
    $stmtCheck->execute(['b' => $barcode, 'id' => $id ? $id : 0]);
    if ($stmtCheck->rowCount() > 0) {
        die("Erro: Já existe um produto com esse Código de Barras!");
    }
}

if ($id) {
    // UPDATE
    $sql = "UPDATE products SET name=?, sku=?, barcode=?, quantity=?, price=?, category_id=?, supplier_id=? WHERE id=?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$name, $sku, $barcode, $qty, $price, $cat_id, $sup_id, $id]);
    
    // Log (simplificado)
    // ... adicione seu log aqui se quiser
} else {
    // INSERT
    $sql = "INSERT INTO products (name, sku, barcode, quantity, price, created_by, category_id, supplier_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$name, $sku, $barcode, $qty, $price, $_SESSION['user_id'], $cat_id, $sup_id]);
}

header("Location: ../produtos.php");