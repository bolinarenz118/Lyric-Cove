<?php

declare(strict_types=1);

session_set_cookie_params([
    'httponly' => true,
    'secure' => !empty($_SERVER['HTTPS'])
        && $_SERVER['HTTPS'] !== 'off',
    'samesite' => 'Lax',
]);

session_start();

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/database.php';

function jsonResponse(
    int $statusCode,
    array $data
): never {
    http_response_code($statusCode);

    echo json_encode(
        $data,
        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
    );

    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Allow: POST');

    jsonResponse(405, [
        'success' => false,
        'message' => 'Method not allowed.'
    ]);
}

$rawInput = file_get_contents('php://input');

$data = json_decode($rawInput, true);

if (!is_array($data)) {
    jsonResponse(400, [
        'success' => false,
        'message' => 'Invalid JSON payload.'
    ]);
}

$email = trim((string)($data['email'] ?? ''));
$password = (string)($data['password'] ?? '');

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    jsonResponse(400, [
        'success' => false,
        'message' => 'Invalid email or password.'
    ]);
}

if ($password === '') {
    jsonResponse(400, [
        'success' => false,
        'message' => 'Invalid email or password.'
    ]);
}

try {

    $statement = $pdo->prepare(
        'SELECT id, username, email, password_hash
         FROM users
         WHERE email = :email
         LIMIT 1'
    );

    $statement->execute([
        ':email' => $email
    ]);

    $user = $statement->fetch();

    if (
        !$user ||
        !password_verify(
            $password,
            $user['password_hash']
        )
    ) {
        jsonResponse(401, [
            'success' => false,
            'message' => 'Invalid email or password.'
        ]);
    }

    session_regenerate_id(true);

    $_SESSION['user_id'] = (int)$user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['email'] = $user['email'];

    $_SESSION['csrf_token'] = bin2hex(
        random_bytes(32)
    );

    jsonResponse(200, [
        'success' => true,
        'message' => 'Login successful.',
        'user' => [
            'id' => (int)$user['id'],
            'username' => $user['username'],
            'email' => $user['email']
        ],
        'csrf_token' => $_SESSION['csrf_token']
    ]);

} catch (PDOException $e) {

    error_log(
        'Login database error: ' . $e->getMessage()
    );

    jsonResponse(500, [
        'success' => false,
        'message' => 'Unable to process login.'
    ]);
}