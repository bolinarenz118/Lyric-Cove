<?php

declare(strict_types=1);

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

$username = trim((string)($data['username'] ?? ''));
$email = trim((string)($data['email'] ?? ''));
$password = (string)($data['password'] ?? '');

if (!preg_match('/^[A-Za-z0-9_]{3,50}$/', $username)) {
    jsonResponse(400, [
        'success' => false,
        'message' =>
            'Username must contain 3-50 letters, numbers, or underscores.'
    ]);
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    jsonResponse(400, [
        'success' => false,
        'message' => 'Please provide a valid email address.'
    ]);
}

if (strlen($password) < 8) {
    jsonResponse(400, [
        'success' => false,
        'message' => 'Password must contain at least 8 characters.'
    ]);
}

try {

    $checkStatement = $pdo->prepare(
        'SELECT id FROM users
         WHERE username = :username OR email = :email
         LIMIT 1'
    );

    $checkStatement->execute([
        ':username' => $username,
        ':email' => $email
    ]);

    if ($checkStatement->fetch()) {
        jsonResponse(400, [
            'success' => false,
            'message' => 'Username or email is already registered.'
        ]);
    }

    $passwordHash = password_hash(
        $password,
        PASSWORD_DEFAULT
    );

    $statement = $pdo->prepare(
        'INSERT INTO users
            (username, email, password_hash)
         VALUES
            (:username, :email, :password_hash)'
    );

    $statement->execute([
        ':username' => $username,
        ':email' => $email,
        ':password_hash' => $passwordHash
    ]);

    jsonResponse(201, [
        'success' => true,
        'message' => 'Registration successful.'
    ]);

} catch (PDOException $e) {

    error_log(
        'Registration database error: ' . $e->getMessage()
    );

    jsonResponse(500, [
        'success' => false,
        'message' => 'Unable to register user.'
    ]);
}