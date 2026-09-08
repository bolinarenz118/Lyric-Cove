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

if (!isset($_SESSION['user_id'])) {

    jsonResponse(401, [
        'success' => false,
        'message' => 'Authentication required.'
    ]);
}


function verifyCsrfToken(): void
{
    $sessionToken = $_SESSION['csrf_token'] ?? '';

    $requestToken = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';

    if (
        $sessionToken === '' ||
        $requestToken === '' ||
        !hash_equals(
            $sessionToken,
            $requestToken
        )
    ) {
        jsonResponse(403, [
            'success' => false,
            'message' => 'Invalid CSRF token.'
        ]);
    }
}


function validatePostInput(
    array $data
): array {

    $title = trim((string)($data['title'] ?? ''));
    $content = trim((string)($data['content'] ?? ''));

    if ($title === '') {
        jsonResponse(400, [
            'success' => false,
            'message' => 'Title is required.'
        ]);
    }

    if (mb_strlen($title) > 255) {
        jsonResponse(400, [
            'success' => false,
            'message' => 'Title cannot exceed 255 characters.'
        ]);
    }

    if ($content === '') {
        jsonResponse(400, [
            'success' => false,
            'message' => 'Content is required.'
        ]);
    }

    if (mb_strlen($content) > 100000) {
        jsonResponse(400, [
            'success' => false,
            'message' => 'Content is too long.'
        ]);
    }

    return [
        'title' => $title,
        'content' => $content
    ];
}

$method = $_SERVER['REQUEST_METHOD'];

try {

    
    if ($method === 'GET') {

        $id = filter_input(
            INPUT_GET,
            'id',
            FILTER_VALIDATE_INT
        );

        if ($id !== null && $id !== false) {

            $statement = $pdo->prepare(
                'SELECT
                    p.id,
                    p.title,
                    p.content,
                    p.created_at,
                    p.updated_at,
                    u.username AS author
                 FROM posts p
                 INNER JOIN users u
                    ON p.user_id = u.id
                 WHERE p.id = :id
                   AND p.user_id = :user_id
                 LIMIT 1'
            );

            $statement->execute([
                ':id' => $id,
                ':user_id' => $_SESSION['user_id']
            ]);

            $post = $statement->fetch();

            if (!$post) {
                jsonResponse(404, [
                    'success' => false,
                    'message' => 'Track not found.'
                ]);
            }

            jsonResponse(200, [
                'success' => true,
                'post' => $post
            ]);
        }

        
        $statement = $pdo->prepare(
            'SELECT
                p.id,
                p.title,
                p.content,
                p.created_at,
                p.updated_at,
                u.username AS author
             FROM posts p
             INNER JOIN users u
                ON p.user_id = u.id
             WHERE p.user_id = :user_id
             ORDER BY p.created_at DESC'
        );

        $statement->execute([
            ':user_id' => $_SESSION['user_id']
        ]);

        $posts = $statement->fetchAll();

        jsonResponse(200, [
            'success' => true,
            'posts' => $posts
        ]);
    }

    
    if ($method === 'POST') {

        verifyCsrfToken();

        $rawInput = file_get_contents('php://input');

        $data = json_decode($rawInput, true);

        if (!is_array($data)) {
            jsonResponse(400, [
                'success' => false,
                'message' => 'Invalid JSON payload.'
            ]);
        }

        $validated = validatePostInput($data);

        $statement = $pdo->prepare(
            'INSERT INTO posts
                (user_id, title, content)
             VALUES
                (:user_id, :title, :content)'
        );

        $statement->execute([
            ':user_id' => $_SESSION['user_id'],
            ':title' => $validated['title'],
            ':content' => $validated['content']
        ]);

        $newId = (int)$pdo->lastInsertId();

        jsonResponse(201, [
            'success' => true,
            'message' => 'Track created successfully.',
            'id' => $newId
        ]);
    }

     
     
    if ($method === 'PUT') {

        verifyCsrfToken();

        $id = filter_input(
            INPUT_GET,
            'id',
            FILTER_VALIDATE_INT
        );

        if (!$id) {
            jsonResponse(400, [
                'success' => false,
                'message' => 'A valid post ID is required.'
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

        $validated = validatePostInput($data);

        $checkStatement = $pdo->prepare(
            'SELECT id
             FROM posts
             WHERE id = :id
               AND user_id = :user_id
             LIMIT 1'
        );

        $checkStatement->execute([
            ':id' => $id,
            ':user_id' => $_SESSION['user_id']
        ]);

        if (!$checkStatement->fetch()) {
            jsonResponse(404, [
                'success' => false,
                'message' => 'Track not found.'
            ]);
        }

        $statement = $pdo->prepare(
            'UPDATE posts
             SET title = :title,
                 content = :content
             WHERE id = :id
               AND user_id = :user_id'
        );

        $statement->execute([
            ':title' => $validated['title'],
            ':content' => $validated['content'],
            ':id' => $id,
            ':user_id' => $_SESSION['user_id']
        ]);

        jsonResponse(200, [
            'success' => true,
            'message' => 'Track updated successfully.'
        ]);
    }

    
    
    if ($method === 'DELETE') {

        verifyCsrfToken();

        $id = filter_input(
            INPUT_GET,
            'id',
            FILTER_VALIDATE_INT
        );

        if (!$id) {
            jsonResponse(400, [
                'success' => false,
                'message' => 'A valid post ID is required.'
            ]);
        }

        $statement = $pdo->prepare(
            'DELETE FROM posts
             WHERE id = :id
               AND user_id = :user_id'
        );

        $statement->execute([
            ':id' => $id,
            ':user_id' => $_SESSION['user_id']
        ]);

        if ($statement->rowCount() === 0) {
            jsonResponse(404, [
                'success' => false,
                'message' => 'Track not found.'
            ]);
        }

        jsonResponse(200, [
            'success' => true,
            'message' => 'Track deleted successfully.'
        ]);
    }

    header(
        'Allow: GET, POST, PUT, DELETE'
    );

    jsonResponse(405, [
        'success' => false,
        'message' => 'Method not allowed.'
    ]);

} catch (PDOException $e) {

    error_log(
        'Posts API database error: ' . $e->getMessage()
    );

    jsonResponse(500, [
        'success' => false,
        'message' => 'Database operation failed.'
    ]);
}