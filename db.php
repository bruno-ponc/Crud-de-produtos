<?php
// db.php
$config = require __DIR__ . '/config.php';

try {
  $pdo = new PDO(
    "mysql:host={$config['db_host']};dbname={$config['db_name']};charset=utf8mb4",
    $config['db_user'],
    $config['db_pass'],
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
  );
} catch (Exception $e) {
  die("Erro BD: " . $e->getMessage());
}

// helpers simples (em pt-BR)
function gerar_salt() { return bin2hex(random_bytes(16)); } // 32 chars
function sha256_salt($salt, $senha) { return hash('sha256', $salt . $senha); }
