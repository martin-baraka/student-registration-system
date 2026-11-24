<?php
session_start();

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

// Only administrators can access this page.
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../admin-login.php");
    exit();
}

$message = "";
$error = "";

$uploadDirectory = "/var/www/uploads/";

// Handle file upload
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['upload_file'])) {

    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if ($title === "") {
        $error = "Please enter a file title.";

    } elseif (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
        $error = "Please select a valid file.";

    } else {

        $file = $_FILES['file'];

        // Maximum file size: 10MB
        if ($file['size'] > 10 * 1024 * 1024) {
            $error = "File size must not exceed 10MB.";

        } else {

            $allowedExtensions = [
                'pdf', 'doc', 'docx',
                'ppt', 'pptx', 'xls', 'xlsx',
                'txt', 'jpg', 'jpeg', 'png'
            ];

            $originalName = basename($file['name']);
            $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

            if (!in_array($extension, $allowedExtensions, true)) {
                $error = "This file type is not allowed.";

            } else {

                $storedName = bin2hex(random_bytes(16)) . "." . $extension;

                if (!is_dir($uploadDirectory)) {
                    mkdir($uploadDirectory, 0755, true);
                }

                $destination = $uploadDirectory . $storedName;

                if (move_uploaded_file($file['tmp_name'], $destination)) {

                    try {
                        $stmt = $pdo->prepare("
                            INSERT INTO uploaded_files
                            (title, description, original_name, stored_name)
                            VALUES (?, ?, ?, ?)
                        ");

                        $stmt->execute([
                            $title,
                            $description,
                            $originalName,
                            $storedName
                        ]);

                        $message = "File uploaded successfully!";

                    } catch (PDOException $e) {
                        unlink($destination);
                        $error = "Database error while uploading file.";
                    }

                } else {
                    $error = "Unable to save the uploaded file.";
                }
            }
        }
    }
}

// Handle file deletion
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['delete_file'])) {

    $id = filter_input(INPUT_POST, 'file_id', FILTER_VALIDATE_INT);

    if ($id) {

        $stmt = $pdo->prepare("SELECT stored_name FROM uploaded_files WHERE id = ?");
        $stmt->execute([$id]);

        $file = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($file) {

            $stmt = $pdo->prepare("DELETE FROM uploaded_files WHERE id = ?");
            $stmt->execute([$id]);

            $path = $uploadDirectory . basename($file['stored_name']);

            if (is_file($path)) {
                unlink($path);
            }

            $message = "File deleted successfully.";

        } else {
            $error = "File not found.";
        }
    }
}

// Fetch uploaded files
$stmt = $pdo->query("SELECT * FROM uploaded_files ORDER BY uploaded_at DESC");
$files = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Manage Files</title>

    <link rel="stylesheet" href="../assets/css/style.css">

    <style>
        .file-container {
            max-width: 1100px;
            margin: 40px auto;
            padding: 20px;
        }

        .file-form {
            background: white;
            padding: 25px;
            border-radius: 10px;
            margin-bottom: 30px;
        }

        .file-form input,
        .file-form textarea {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
        }

        .file-form button {
            padding: 12px 25px;
            background: #2563eb;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .file-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }

        .file-table th,
        .file-table td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        .download-button {
            display: inline-block;
            background: #2563eb;
            color: white;
            padding: 8px 12px;
            border-radius: 5px;
            text-decoration: none;
            margin-right: 5px;
            white-space: nowrap;
        }

        .download-button:hover {
            background: #1d4ed8;
        }

        .delete-button {
            background: #dc2626;
            color: white;
            padding: 8px 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            white-space: nowrap;
        }

        .delete-button:hover {
            background: #b91c1c;
        }

        .success {
            color: green;
            margin-bottom: 15px;
        }

        .error {
            color: red;
            margin-bottom: 15px;
        }

        .action-buttons {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        @media (max-width: 768px) {
            .file-container {
                overflow-x: auto;
            }

            .file-table {
                min-width: 750px;
            }
        }
    </style>
</head>

<body>

<div class="file-container">

    <h2>Manage Uploaded Files</h2>

    <?php if ($message): ?>
        <p class="success">
            <?= htmlspecialchars($message) ?>
        </p>
    <?php endif; ?>

    <?php if ($error): ?>
        <p class="error">
            <?= htmlspecialchars($error) ?>
        </p>
    <?php endif; ?>

    <!-- Upload Form -->
    <div class="file-form">

        <h3>Upload New File</h3>

        <form method="POST" enctype="multipart/form-data">

            <label>File Title</label>

            <input
                type="text"
                name="title"
                placeholder="Enter file title"
                maxlength="255"
                required
            >

            <label>Description</label>

            <textarea
                name="description"
                placeholder="Enter a short description"
                rows="4"
            ></textarea>

            <label>Select File</label>

            <input
                type="file"
                name="file"
                required
            >

            <button type="submit" name="upload_file">
                Upload File
            </button>

        </form>

    </div>

    <!-- Uploaded Files -->
    <h3>Uploaded Files</h3>

    <table class="file-table">

        <thead>
            <tr>
                <th>Title</th>
                <th>Description</th>
                <th>File Name</th>
                <th>Date Uploaded</th>
                <th>Action</th>
            </tr>
        </thead>

        <tbody>

        <?php if (count($files) > 0): ?>

            <?php foreach ($files as $file): ?>

                <tr>

                    <td>
                        <?= htmlspecialchars($file['title']) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($file['description'] ?? '') ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($file['original_name']) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($file['uploaded_at']) ?>
                    </td>

                    <td>

                        <div class="action-buttons">

                            <!-- Download Button -->
                            <a
                                href="../download.php?id=<?= (int)$file['id'] ?>"
                                class="download-button"
                                title="Download File"
                            >
                                ⬇ Download
                            </a>

                            <!-- Delete Button -->
                            <form
                                method="POST"
                                onsubmit="return confirm('Delete this file?');"
                            >

                                <input
                                    type="hidden"
                                    name="file_id"
                                    value="<?= (int)$file['id'] ?>"
                                >

                                <button
                                    type="submit"
                                    name="delete_file"
                                    class="delete-button"
                                    title="Delete File"
                                >
                                    🗑 Delete
                                </button>

                            </form>

                        </div>

                    </td>

                </tr>

            <?php endforeach; ?>

        <?php else: ?>

            <tr>
                <td colspan="5" style="text-align: center;">
                    No files have been uploaded yet.
                </td>
            </tr>

        <?php endif; ?>

        </tbody>

    </table>

    <br>

    <a href="../index.php">Back to Homepage</a>

</div>

</body>
</html>

