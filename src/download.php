<?php

require_once __DIR__ . '/includes/db.php';

if (!isset($_GET['id']) || !filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    http_response_code(400);
    exit("Invalid file request.");
}

$id = (int) $_GET['id'];

$stmt = $pdo->prepare("
    SELECT original_name, stored_name
    FROM uploaded_files
    WHERE id = ?
");

$stmt->execute([$id]);

$file = $stmt->fetch();

if (!$file) {
    http_response_code(404);
    exit("File not found.");
}

// Use only the generated storage filename.
$storedName = basename($file['stored_name']);
$filePath = '/var/www/uploads/' . $storedName;

if (!is_file($filePath) || !is_readable($filePath)) {
    http_response_code(404);
    exit("The requested file is unavailable.");
}

// Safely prepare the original filename for download.
$originalName = basename($file['original_name']);
$asciiName = preg_replace('/[^A-Za-z0-9._-]/', '_', $originalName);
$asciiName = $asciiName ?: 'download';

header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . $asciiName . '"; filename*=UTF-8\'\'' . rawurlencode($originalName));
header('Content-Length: ' . filesize($filePath));
header('X-Content-Type-Options: nosniff');
header('Cache-Control: private, no-store');

readfile($filePath);
exit;
?>
