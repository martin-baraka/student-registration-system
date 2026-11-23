<?php

require_once 'includes/db.php';
require_once 'includes/auth.php';

requireAdmin();

$error = "";

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    die("Invalid student ID.");
}

// Get student information
$statement = $pdo->prepare("SELECT * FROM students WHERE id = ?");
$statement->execute([$id]);

$student = $statement->fetch();

if (!$student) {
    die("Student not found.");
}

// Load courses and academic years
$courses = $pdo->query("SELECT * FROM courses ORDER BY course_name")->fetchAll();

$years = $pdo->query("SELECT * FROM academic_years ORDER BY start_date DESC")->fetchAll();

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    if (
        !hash_equals($_SESSION['edit_token'] ?? '', $_POST['token'] ?? '')
    ) {
        $error = "Invalid request. Refresh the page and try again.";

    } else {

        $firstName = trim($_POST['first_name'] ?? '');
        $lastName = trim($_POST['last_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $gender = $_POST['gender'] ?? '';
        $courseId = (int) ($_POST['course_id'] ?? 0);
        $yearId = (int) ($_POST['academic_year_id'] ?? 0);
        $status = $_POST['status'] ?? '';

        if (
            $firstName === '' ||
            $lastName === '' ||
            !filter_var($email, FILTER_VALIDATE_EMAIL) ||
            !in_array($gender, ['Male', 'Female', 'Other'], true) ||
            !in_array($status, ['Active', 'Inactive', 'Graduated'], true) ||
            $courseId <= 0 ||
            $yearId <= 0
        ) {
            $error = "Please provide valid information.";

        } else {

            $check = $pdo->prepare(
                "SELECT id FROM students
                 WHERE email = ? AND id != ?"
            );

            $check->execute([$email, $id]);

            if ($check->fetch()) {
                $error = "That email address is already in use.";

            } else {

                $update = $pdo->prepare(
                    "UPDATE students SET
                        first_name = ?,
                        last_name = ?,
                        email = ?,
                        phone = ?,
                        gender = ?,
                        course_id = ?,
                        academic_year_id = ?,
                        status = ?
                    WHERE id = ?"
                );

                $update->execute([
                    $firstName,
                    $lastName,
                    $email,
                    $phone ?: null,
                    $gender,
                    $courseId,
                    $yearId,
                    $status,
                    $id
                ]);

                header("Location: students.php");
                exit;
            }
        }
    }
}

if (empty($_SESSION['edit_token'])) {
    $_SESSION['edit_token'] = bin2hex(random_bytes(32));
}

$pageTitle = "Edit Student";

include 'includes/header.php';
?>

<h1 class="page-title">Edit Student</h1>

<?php if ($error): ?>
    <div class="error">
        <?= htmlspecialchars($error) ?>
    </div>
<?php endif; ?>

<div class="form-container">

    <form method="POST">

        <input type="hidden" name="token"
               value="<?= htmlspecialchars($_SESSION['edit_token']) ?>">

        <div class="form-group">
            <label>First Name</label>
            <input type="text" name="first_name"
                   value="<?= htmlspecialchars($student['first_name']) ?>" required>
        </div>

        <div class="form-group">
            <label>Last Name</label>
            <input type="text" name="last_name"
                   value="<?= htmlspecialchars($student['last_name']) ?>" required>
        </div>

        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email"
                   value="<?= htmlspecialchars($student['email']) ?>" required>
        </div>

        <div class="form-group">
            <label>Phone</label>
            <input type="tel" name="phone"
                   value="<?= htmlspecialchars($student['phone'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label>Gender</label>
            <select name="gender" required>
                <?php foreach (['Male', 'Female', 'Other'] as $gender): ?>
                    <option value="<?= $gender ?>"
                        <?= $student['gender'] === $gender ? 'selected' : '' ?>>
                        <?= $gender ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label>Course</label>
            <select name="course_id" required>
                <?php foreach ($courses as $course): ?>
                    <option value="<?= $course['id'] ?>"
                        <?= $student['course_id'] == $course['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($course['course_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label>Academic Year</label>
            <select name="academic_year_id" required>
                <?php foreach ($years as $year): ?>
                    <option value="<?= $year['id'] ?>"
                        <?= $student['academic_year_id'] == $year['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($year['academic_year'] . ' - ' . $year['semester']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label>Status</label>
            <select name="status" required>
                <?php foreach (['Active', 'Inactive', 'Graduated'] as $status): ?>
                    <option value="<?= $status ?>"
                        <?= $student['status'] === $status ? 'selected' : '' ?>>
                        <?= $status ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit" class="btn">Update Student</button>

        <a href="students.php" class="btn">Cancel</a>

    </form>

</div>

<?php include 'includes/footer.php'; ?>
