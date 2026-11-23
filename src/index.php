<?php

require_once 'includes/db.php';
require_once 'includes/auth.php';

$pageTitle = "Dashboard";

if (isset($_SESSION['admin_id'])) {
    header("Location: students.php");
    exit;
}

$totalStudents = $pdo->query("SELECT COUNT(*) FROM students")->fetchColumn();
$totalCourses = $pdo->query("SELECT COUNT(*) FROM courses")->fetchColumn();
$totalDepartments = $pdo->query("SELECT COUNT(*) FROM departments")->fetchColumn();

require_once 'includes/header.php';

?>

<section class="welcome-section">

    <h1>Welcome to the Student Registration System</h1>

    <p>
        Manage student registration and academic information
        through one simple platform.
    </p>

    <a href="register.php" class="btn">
        Register as a Student
    </a>

</section>

<section class="dashboard-section">

    <h2>System Overview</h2>

    <div class="dashboard-cards">

        <div class="dashboard-card">
            <h3>Total Students</h3>
            <p><?= $totalStudents ?></p>
        </div>

        <div class="dashboard-card">
            <h3>Available Courses</h3>
            <p><?= $totalCourses ?></p>
        </div>

        <div class="dashboard-card">
            <h3>Departments</h3>
            <p><?= $totalDepartments ?></p>
        </div>

    </div>

</section>

<section class="information-section">

    <h2>Get Started</h2>

    <p>
        New students can register using the registration form.
        Click here to register.
    </p>

    <div class="action-buttons">

        <a href="register.php" class="btn">
            Student Registration
        </a>

    </div>

</section>

<?php require_once 'includes/footer.php'; ?>
