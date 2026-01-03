<?php
// config/db.php
$host = '127.0.0.1';
$db   = 'fortress_inventory';
$user = 'root'; 
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Lança erros como Exceções
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Retorna arrays associativos
    PDO::ATTR_EMULATE_PREPARES   => false,                  // Usa prepare statements REAIS do banco
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
}  catch (\PDOException $e) {
    error_log($e->getMessage());
    die('Erro interno de conexão. Contate o suporte.'); 
}
?>