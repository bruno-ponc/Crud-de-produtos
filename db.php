<?php
// conexão PDO

$dbHost = '127.0.0.1';
$dbUser = 'root';
$dbPass = '';
$dbName = 'lista_tarefas';
$charset = 'utf8mb4';

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

$dsn = "mysql:host=$dbHost;dbname=$dbName;charset=$charset";
$pdo = new PDO($dsn, $dbUser, $dbPass, $options);
