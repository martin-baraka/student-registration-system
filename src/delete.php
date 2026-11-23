<?php

require_once 'includes/db.php';
require_once 'includes/auth.php';

requireAdmin();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: students.php");
    exit;
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$token = $_POST['token'] ?? '';

if (
    !$id ||
    !hash_equals($_SESSION['delete_token'] ?? '', $token)
) {
    die("Invalid deletion request.");
}

$statement = $pdo->prepare("DELETE FROM students WHERE id = ?");
$statement->execute([$id]);

unset($_SESSION['delete_token']);

header("Location: students.php");
exit;
