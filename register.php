<?php

declare(strict_types=1);
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
            max-width: 420px;
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
            text-align: center;
            margin-top: 0;
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
        }

        button {
            width: 100%;
            padding: 12px;
            margin-top: 20px;
            border: none;
            border-radius: 6px;
            background: #16a34a;
            color: white;
            font-size: 16px;
            cursor: pointer;
        }

        button:hover {
            background: #15803d;
        }

        .message {
            margin-top: 15px;
            text-align: center;
        }

        .login {
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

    <h1>Create Account</h1>

    <form id="registerForm">

        <label for="username">
            Username
        </label>

        <input
            type="text"
            id="username"
            required
            minlength="3"
            maxlength="50"
            pattern="[A-Za-z0-9_]+"
        >

        <label for="email">
            Email
        </label>

        <input
            type="email"
            id="email"
            required
        >

        <label for="password">
            Password
        </label>

        <input
            type="password"
            id="password"
            required
            minlength="8"
            autocomplete="new-password"
        >

        <button type="submit">
            Register
        </button>

    </form>

    <div
        id="message"
        class="message"
    ></div>

    <div class="login">
        Already have an account?
        <a href="login.php">
            Login
        </a>
    </div>

</div>

<script>

const form =
    document.getElementById('registerForm');

const message =
    document.getElementById('message');

form.addEventListener(
    'submit',
    async function(event) {

        event.preventDefault();

        message.textContent =
            'Creating account...';

        const username =
            document.getElementById('username')
                .value
                .trim();

        const email =
            document.getElementById('email')
                .value
                .trim();

        const password =
            document.getElementById('password')
                .value;

        try {

            const response = await fetch(
                'api/register.php',
                {
                    method: 'POST',

                    headers: {
                        'Content-Type':
                            'application/json'
                    },

                    body: JSON.stringify({
                        username,
                        email,
                        password
                    })
                }
            );

            const data =
                await response.json();

            if (!response.ok) {

                message.textContent =
                    data.message ||
                    'Registration failed.';

                return;
            }

            message.textContent =
                'Registration successful! Redirecting...';

            setTimeout(
                function() {
                    window.location.href =
                        'login.php';
                },
                1000
            );

        } catch (error) {

            message.textContent =
                'Unable to connect to the server.';
        }
    }
);

</script>

</body>

</html>