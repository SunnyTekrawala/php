<?php
// Note: Assumes session_start() is called in the calling script (e.g., index.php, login.php)
require_once __DIR__ . '/autoloader.php';
// Include the AuthManager to check login status
// NOTE: We assume AuthManager is available via the autoloader which is loaded above.

// The class will be available if autoloader.php is loaded correctly
$isLoggedIn = class_exists('AuthManager') && AuthManager::isLoggedIn();

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voltmart - <?php echo $pageTitle ?? 'The Electric Marketplace'; ?></title>
    <style>
        body {
            font-family: sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        .navbar {
            background-color: #333;
            overflow: hidden;
            color: white;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar a {
            float: left;
            display: block;
            color: white;
            text-align: center;
            padding: 14px 16px;
            text-decoration: none;
        }

        .navbar a:hover {
            background-color: #ddd;
            color: black;
        }

        .navbar .logo {
            font-size: 1.5em;
            font-weight: bold;
        }

        .navbar .nav-links {
            display: flex;
        }

        .container {
            padding: 20px;
        }
    </style>
</head>

<body>

    <div class="navbar">
        <a href="index.php" class="logo">VOLTMART ⚡</a>
        <div class="nav-links">
            <a href="index.php">Home</a>
            <a href="products.php">Products</a>
            <a href="cart.php">Cart</a>
            <?php if ($isLoggedIn): ?>
                <a href="profile.php">Profile</a>
                <a href="logout.php">Logout</a>
            <?php else: ?>
                <a href="login.php">Login</a>
                <a href="register.php">Register</a>
            <?php endif; ?>
        </div>
    </div>

    <div class="container"></div>