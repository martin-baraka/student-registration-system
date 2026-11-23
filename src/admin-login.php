<?php

require_once 'includes/db.php';
require_once 'includes/auth.php';

$error = "";

if (isset($_SESSION['admin_id'])) {
    header("Location: students.php");
exit;
}

if (empty($_SESSION['login_token'])) {
    $_SESSION['login_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    if (!hash_equals($_SESSION['login_token'], $_POST['token'] ?? '')) {
        $error = "Invalid request. Please refresh the page.";
    } else {

        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        $statement = $pdo->prepare(
            "SELECT * FROM admins WHERE username = ? OR email = ? LIMIT 1"
        );

        $statement->execute([$username, $username]);
        $admin = $statement->fetch();

        if ($admin && password_verify($password, $admin['password'])) {

            session_regenerate_id(true);

            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];

            unset($_SESSION['login_token']);

            header("Location: index.php");
            exit;

        } else {
            $error = "Incorrect username or password.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrator Login</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>

<div class="container">

    <div class="form-container">

        <h2>Administrator Login</h2>

        <br>

        <?php if ($error): ?>
            <div class="error">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST">

            <input type="hidden" name="token"
                   value="<?= htmlspecialchars($_SESSION['login_token']) ?>">

            <div class="form-group">
                <label>Username or Email</label>
                <input type="text" name="username" required>
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>

            <button type="submit" class="btn">
                Login
            </button>

            <br><br>

            <a href="index.php">Return to Public Website</a>

        </form>

    </div>

</div>

</body>
</html>
