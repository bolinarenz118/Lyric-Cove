<?php

declare(strict_types=1);

session_set_cookie_params([
    'httponly' => true,
    'secure' => !empty($_SERVER['HTTPS'])
        && $_SERVER['HTTPS'] !== 'off',
    'samesite' => 'Lax',
]);

session_start();

if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Lyric Cove</title>

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: -apple-system, "BlinkMacSystemFont", "Segoe UI", "Roboto", Helvetica, Arial, sans-serif;
            background: #f3f4f6;
            background-image: url('images/bgg.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
        }

        .container {
            width: 100%;
            max-width: 400px;
            padding: 35px;
            border-radius: 20px;
            box-shadow:
                0 10px 30px rgba(0, 0, 0, 0.15);
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.25);
        }

        h1 {
            margin-top: 0;
            text-align: center;
        }

        label {
            display: block;
            margin-top: 15px;
            margin-bottom: 6px;
            font-weight: bold;
        }

        input {
            width: 100%;
            padding: 12px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 15px;
        }

        button {
            width: 100%;
            padding: 12px;
            margin-top: 20px;
            border: none;
            border-radius: 6px;
            background: #2563eb;
            color: white;
            font-size: 16px;
            cursor: pointer;
        }

        button:hover {
            background: #1d4ed8;
        }

        .message {
            margin-top: 15px;
            text-align: center;
        }

        .register {
            margin-top: 20px;
            text-align: center;
        }

        a {
            color: #2563eb;
        }

    </style>

</head>

<body>

<div class="container">

    <h1>Log into Lyric Cove</h1>

    <form id="loginForm">

        <label for="email">
            Email
        </label>

        <input
            type="email"
            id="email"
            required
            autocomplete="email"
        >

        <label for="password">
            Password
        </label>

        <input
            type="password"
            id="password"
            required
            autocomplete="current-password"
        >

        <button type="submit">
            Login
        </button>

    </form>

    <div
        id="message"
        class="message"
    ></div>

    <div class="register">
        Don't have an account?
        <a href="register.php">
            Register
        </a>
    </div>

</div>

<script>

const form =
    document.getElementById('loginForm');

const message =
    document.getElementById('message');

form.addEventListener(
    'submit',
    async function(event) {

        event.preventDefault();

        message.textContent = 'Logging in...';

        const email =
            document.getElementById('email').value.trim();

        const password =
            document.getElementById('password').value;

        try {

            const response = await fetch(
                'api/login.php',
                {
                    method: 'POST',

                    headers: {
                        'Content-Type':
                            'application/json'
                    },

                    body: JSON.stringify({
                        email: email,
                        password: password
                    })
                }
            );

            const data =
                await response.json();

            if (!response.ok) {

                message.textContent =
                    data.message ||
                    'Login failed.';

                return;
            }

            sessionStorage.setItem(
                'csrf_token',
                data.csrf_token
            );

            window.location.href =
                'dashboard.php';

        } catch (error) {

            message.textContent =
                'Unable to connect to the server.';
        }
    }
);

</script>

</body>

</html>