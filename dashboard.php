<?php
// ==========================================
// 1. LÓGICA PHP (Backend)
// ==========================================
session_start();
require 'config/db.php';

if (!isset($_SESSION['user_id'])) { header("Location: index.php"); exit; }

// Buscas Padrão
$stmtTotal = $pdo->query("SELECT count(*) FROM products");
$totalItems = $stmtTotal->fetchColumn();

$stmtLow = $pdo->query("SELECT count(*) FROM products WHERE quantity < 10");
$lowStock = $stmtLow->fetchColumn();

$stmtValue = $pdo->query("SELECT SUM(price * quantity) FROM products");
$totalValue = $stmtValue->fetchColumn() ?: 0;

// Busca Lista Recente
$stmtList = $pdo->query("SELECT * FROM products ORDER BY id DESC LIMIT 10");
$products = $stmtList->fetchAll();

// --- DADOS PARA O GRÁFICO ---
$stmtChart = $pdo->query("SELECT name, quantity FROM products ORDER BY quantity DESC LIMIT 5");
$chartData = $stmtChart->fetchAll();

$graficoNomes = [];
$graficoQtds = [];

foreach ($chartData as $item) {
    // Corta nomes muito longos para não quebrar o visual
    $graficoNomes[] = mb_strimwidth($item['name'], 0, 15, "...");
    $graficoQtds[] = $item['quantity'];
}

$jsonNomes = json_encode($graficoNomes);
$jsonQtds  = json_encode($graficoQtds);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Nexus </title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" type="image/png" href="icon.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<div class="wrapper">
    <?php include 'includes/menu_lateral.php'; ?>

    <main class="main-content">
        
        <div class="header-bar">
            <h2>Dashboard</h2>
            <div class="user-profile">
                <span>Olá, <strong><?= htmlspecialchars($_SESSION['username']) ?></strong></span>
            </div>
        </div>

        <div class="stats-grid">
            <a href="produtos.php?filtro=baixo" style="text-decoration: none; color: inherit;">
                <div class="stat-card" style="border-left: 4px solid #ef4444;">
                    <div class="stat-icon" style="color: #ef4444;"><i class="fa-solid fa-triangle-exclamation"></i></div>
                    <div class="stat-info">
                        <p>Estoque Baixo</p>
                        <h3><?= $lowStock ?></h3>
                    </div>
                </div>
            </a>

            <div class="stat-card" style="border-left: 4px solid #3b82f6;">
                <div class="stat-icon" style="color: #3b82f6;"><i class="fa-solid fa-boxes-stacked"></i></div>
                <div class="stat-info">
                    <p>Total Itens</p>
                    <h3><?= $totalItems ?></h3>
                </div>
            </div>

            <div class="stat-card" style="border-left: 4px solid #10b981;">
                <div class="stat-icon" style="color: #10b981;"><i class="fa-solid fa-sack-dollar"></i></div>
                <div class="stat-info">
                    <p>Patrimônio</p>
                    <h3>R$ <?= number_format($totalValue, 2, ',', '.') ?></h3>
                </div>
            </div>
        </div>

        <div class="card" style="background: white; padding: 20px; border-radius: 10px; margin-bottom: 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
            <h3 style="margin-bottom: 15px; color: #374151;"><i class="fa-solid fa-chart-simple"></i> Top 5 - Maiores Estoques</h3>
            <div style="height: 300px; width: 100%;">
                <canvas id="meuGrafico"></canvas>
            </div>
        </div>

        <div class="table-container">
            <h3>Atalhos / Recentes</h3>
            <table class="table">
                <thead>
                    <tr>
                        <th>Produto</th>
                        <th>Qtd</th>
                        <th>Preço</th>
                        <th>Ação</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $prod): ?>
                    <tr>
                        <td><?= htmlspecialchars($prod['name']) ?></td>
                        <td><?= $prod['quantity'] ?></td>
                        <td>R$ <?= number_format($prod['price'], 2, ',', '.') ?></td>
                        <td>
                            <a href="produto_form.php?id=<?= $prod['id'] ?>" style="color: #3b82f6;"><i class="fa-solid fa-pen"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    const ctx = document.getElementById('meuGrafico').getContext('2d');

    // 1. Cria a estrutura do gráfico (Vazio inicialmente)
    const meuChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: [], // Começa vazio
            datasets: [{
                label: 'Estoque',
                data: [], // Começa vazio
                backgroundColor: ['#374151', '#3b82f6', '#10b981', '#f59e0b', '#ef4444'],
                borderRadius: 4,
                borderWidth: 0,
                barPercentage: 0.5,
                maxBarThickness: 40
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: {
                duration: 1000, // Animação suave de 1 segundo
                easing: 'easeOutQuart'
            },
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, grid: { color: '#f3f4f6', drawBorder: false } },
                x: { grid: { display: false, drawBorder: false } }
            }
        }
    });

    // 2. Função que busca os dados novos
    function atualizarGrafico() {
        fetch('actions/dados_grafico.php') // Chama o arquivo PHP que criamos
            .then(response => response.json())
            .then(dados => {
                // Atualiza os dados do gráfico
                meuChart.data.labels = dados.labels;
                meuChart.data.datasets[0].data = dados.data;
                
                // Manda o gráfico redesenhar com os novos números
                meuChart.update(); 
            })
            .catch(error => console.error('Erro ao buscar dados:', error));
    }

    // 3. Inicia o ciclo
    atualizarGrafico(); // Chama a primeira vez imediatamente
    setInterval(atualizarGrafico, 5000); // Repete a cada 5 segundos (5000ms)

</script>
</body>
</html>