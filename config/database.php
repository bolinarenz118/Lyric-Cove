<?php

declare(strict_types=1);

$host = getenv('CMS_DB_HOST') ?: '127.0.0.1';
$dbname = getenv('CMS_DB_NAME') ?: 'cms_php';
$username = getenv('CMS_DB_USER') ?: 'root';
$password = getenv('CMS_DB_PASSWORD') ?: '';

$dsn = "mysql:host={$host};dbname={$dbname};charset=utf8mb4";

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,

    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,

    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, $username, $password, $options);
} catch (PDOException $e) {

    error_log('Database connection failed: ' . $e->getMessage());

    http_response_code(500);
    exit('Database connection failed.');
}