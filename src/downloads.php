<?php
require_once __DIR__ . '/includes/db.php';

$stmt = $pdo->query("
    SELECT * FROM uploaded_files
    ORDER BY uploaded_at DESC
");

$files = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Available Downloads</title>

    <link rel="stylesheet" href="assets/css/style.css">

    <style>
        .downloads-container {
            max-width: 1000px;
            margin: 40px auto;
            padding: 20px;
        }

        .downloads-container h2 {
            margin-bottom: 10px;
        }

        .download-card {
            background: white;
            padding: 20px;
            margin: 15px 0;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        .download-card h3 {
            margin-bottom: 10px;
        }

        .download-card p {
            margin-bottom: 12px;
        }

        .download-button {
            display: inline-block;
            padding: 10px 18px;
            background: #2563eb;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }

        .download-button:hover {
            background: #1d4ed8;
        }

        .no-files {
            padding: 20px;
            background: #f1f5f9;
            border-radius: 8px;
        }
    </style>
</head>

<body>

<div class="downloads-container">

    <h2>Available Downloads</h2>

    <p>Browse and download documents shared by the administrator.</p>

    <?php if (count($files) > 0): ?>

        <?php foreach ($files as $file): ?>

            <div class="download-card">

                <h3>
                    <?= htmlspecialchars($file['title']) ?>
                </h3>

                <p>
                    <?= nl2br(htmlspecialchars($file['description'] ?? '')) ?>
                </p>

                <small>
                    Uploaded:
                    <?= htmlspecialchars($file['uploaded_at']) ?>
                </small>

                <br><br>

                <a
                    href="download.php?id=<?= (int)$file['id'] ?>"
                    class="download-button">
                    Download File
                </a>

            </div>

        <?php endforeach; ?>

    <?php else: ?>

        <div class="no-files">
            No files are currently available for download.
        </div>

    <?php endif; ?>

    <br>

    <a href="index.php">Back to Homepage</a>

</div>

</body>
</html>
