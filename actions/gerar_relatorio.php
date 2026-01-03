<?php
// 1. Configurações para evitar que erros estraguem o PDF
ini_set('display_errors', 0); // Oculta erros na tela (vai para o log)
error_reporting(E_ALL);

// 2. Dependências
require '../vendor/autoload.php'; 
require '../config/db.php'; // Sua conexão PDO

use Dompdf\Dompdf;
use Dompdf\Options;

// 3. Captura os Filtros
$category_id = filter_input(INPUT_POST, 'category_id', FILTER_SANITIZE_NUMBER_INT);
$supplier_id = filter_input(INPUT_POST, 'supplier_id', FILTER_SANITIZE_NUMBER_INT);
$low_stock   = isset($_POST['low_stock']);
$limite_alerta = 5;

// 4. Prepara a Consulta (SQL)
$sql = "SELECT p.sku, p.name, p.quantity, p.price, 
               c.name as cat_name, s.name as sup_name 
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.id
        LEFT JOIN suppliers s ON p.supplier_id = s.id
        WHERE 1=1";

$params = [];

if (!empty($category_id)) {
    $sql .= " AND p.category_id = ?";
    $params[] = $category_id;
}

if (!empty($supplier_id)) {
    $sql .= " AND p.supplier_id = ?";
    $params[] = $supplier_id;
}

if ($low_stock) {
    $sql .= " AND p.quantity <= ?";
    $params[] = $limite_alerta;
}

$sql .= " ORDER BY p.name ASC";

// 5. Busca os dados no Banco
try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    die("Erro no banco de dados. Verifique a conexão.");
}

// 6. Monta o HTML do Relatório
$html = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        h1 { text-align: center; color: #333; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background: #333; color: #fff; padding: 8px; text-align: left; }
        td { border-bottom: 1px solid #ccc; padding: 8px; }
        .alert { color: red; font-weight: bold; }
    </style>
</head>
<body>
    <h1>Relatório de Produtos</h1>
    <p style="text-align:center">Gerado em: '.date('d/m/Y H:i').'</p>

    <table>
        <thead>
            <tr>
                <th>SKU</th>
                <th>Produto</th>
                <th>Categoria</th>
                <th>Fornecedor</th>
                <th>Qtd</th>
            </tr>
        </thead>
        <tbody>';

if (count($products) > 0) {
    foreach ($products as $row) {
        $class = ($row['quantity'] <= $limite_alerta) ? 'class="alert"' : '';
        $html .= '<tr>
            <td>'.($row['sku'] ?? '-').'</td>
            <td>'.$row['name'].'</td>
            <td>'.($row['cat_name'] ?? '-').'</td>
            <td>'.($row['sup_name'] ?? '-').'</td>
            <td '.$class.'>'.$row['quantity'].'</td>
        </tr>';
    }
} else {
    $html .= '<tr><td colspan="5" style="text-align:center">Nenhum produto encontrado.</td></tr>';
}

$html .= '</tbody></table></body></html>';

// 7. Gera e Baixa o PDF
// Limpa qualquer lixo que tenha sido impresso antes (CRUCIAL PARA NÃO CORROMPER)
if (ob_get_length()) { ob_clean(); }

$options = new Options();
$options->set('isRemoteEnabled', true);
$dompdf = new Dompdf($options);

$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// Força o download com nome correto
$nome_arquivo = "Relatorio_" . date('Y-m-d_Hi') . ".pdf";
$dompdf->stream($nome_arquivo, ["Attachment" => true]);
exit;
?>