<?php
// ARQUIVO: relatorios.php
session_start();
require 'config/db.php'; // Mantendo sua inclusão de banco

// Segurança
if (!isset($_SESSION['user_id'])) { header("Location: index.php"); exit; }

// Buscando dados para os filtros (Categorias e Fornecedores)
// Usando $pdo ou $mysqli dependendo do seu db.php. 
// Vou assumir $pdo baseado no código que você enviou, mas adicionei verificação.

if (isset($pdo)) {
    // Se for PDO
    $cats = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();
    $sups = $pdo->query("SELECT * FROM suppliers ORDER BY name")->fetchAll();
} else {
    // Se for MySQLi (Fallback caso seu db.php use mysqli)
    $cats_q = $mysqli->query("SELECT * FROM categories ORDER BY name");
    $sups_q = $mysqli->query("SELECT * FROM suppliers ORDER BY name");
    $cats = []; while($row = $cats_q->fetch_assoc()) $cats[] = $row;
    $sups = []; while($row = $sups_q->fetch_assoc()) $sups[] = $row;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Relatórios | Fortress</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="icon" type="image/png" href="icon.png">

    <style>
        .report-card {
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            max-width: 600px;
            margin: 20px auto; /* Centralizado */
        }
        
        .form-group { margin-bottom: 20px; }
        
        .form-group label { 
            display: block; 
            margin-bottom: 8px; 
            font-weight: 600; 
            color: #374151; /* Cor padrão do seu tema provável */
        }
        
        .form-control { 
            width: 100%; 
            padding: 10px; 
            border: 1px solid #d1d5db; 
            border-radius: 6px; 
            font-size: 14px;
            box-sizing: border-box;
        }

        .check-area {
            background-color: #fef3c7; /* Fundo amarelado suave */
            padding: 15px;
            border-radius: 6px;
            border: 1px solid #fcd34d;
            color: #92400e;
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
        }

        .btn-export {
            background-color: #2c3e50;
            color: white;
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn-export:hover { background-color: #1f2937; }
    </style>
</head>
<body>

<div class="wrapper">
    <?php include 'includes/menu_lateral.php'; ?>
    
    <main class="main-content">
        <div class="header-bar">
            <h2>📊 Central de Relatórios</h2>
        </div>

        <div class="report-card">
            <form action="actions/gerar_relatorio.php" method="POST" target="_blank">
                
                <div class="form-group">
                    <label><i class="fa-solid fa-tags"></i> Filtrar por Categoria</label>
                    <select name="category_id" class="form-control">
                        <option value="">Todas as Categorias</option>
                        <?php foreach($cats as $cat): ?>
                            <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label><i class="fa-solid fa-truck"></i> Filtrar por Fornecedor</label>
                    <select name="supplier_id" class="form-control">
                        <option value="">Todos os Fornecedores</option>
                        <?php foreach($sups as $sup): ?>
                            <option value="<?= $sup['id'] ?>"><?= htmlspecialchars($sup['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="check-area">
                    <input type="checkbox" name="low_stock" id="ls" value="1" style="width: 18px; height: 18px;">
                    <label for="ls" style="margin:0; cursor:pointer; font-weight: bold;">
                        <i class="fa-solid fa-triangle-exclamation"></i> Apenas produtos com Estoque Baixo 
                    </label>
                </div>

                <button type="submit" class="btn-export">
                    <i class="fa-solid fa-file-pdf"></i> Gerar Relatório PDF
                </button>
            </form>
        </div>

        <div style="text-align: center; color: #6b7280; font-size: 0.9rem; margin-top: 20px;">
            <i class="fa-solid fa-info-circle"></i> O relatório será aberto em uma nova aba.
        </div>

    </main>
</div>

</body>
</html>