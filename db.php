<?php
// db.php
$config = require __DIR__ . '/.env.php';

try {
  $dsn = "mysql:host={$config['DB_HOST']};dbname={$config['DB_NAME']};charset=utf8mb4";
  $pdo = new PDO($dsn, $config['DB_USER'], $config['DB_PASS'], [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  ]);
} catch (PDOException $e) {
  die('Error de conexión: ' . $e->getMessage());
}

function base_url(string $path = ''): string {
  global $config;
  return rtrim($config['APP_URL'], '/') . '/' . ltrim($path, '/');
}
