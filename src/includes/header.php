<?php

require_once __DIR__ . '/auth.php';

$pageTitle = $pageTitle ?? "Student Registration System";

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= htmlspecialchars($pageTitle) ?></title>

    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>

<header class="header">

    <div class="header-container">

        <h2>Student Registration System</h2>
<nav>

    <?php if (isset($_SESSION['admin_id'])): ?>

        <a href="register.php">Register Student</a>
        <a href="students.php">Manage Students</a>
        <a href="logout.php">Logout</a>

    <?php else: ?>

        <a href="index.php">Dashboard</a>
        <a href="register.php">Register as Student</a>
        <a href="admin-login.php">Admin Login</a>

    <?php endif; ?>

</nav>

           </div>

</header>

<main class="container">
