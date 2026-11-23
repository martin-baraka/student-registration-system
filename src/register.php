<?php

require_once 'includes/db.php';

$successMessage = "";
$errorMessage = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $firstName = trim($_POST["first_name"] ?? "");
    $lastName = trim($_POST["last_name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $phone = trim($_POST["phone"] ?? "");
    $gender = $_POST["gender"] ?? "";
    $departmentId = (int) ($_POST["department_id"] ?? 0);
    $courseId = (int) ($_POST["course_id"] ?? 0);
    $academicYearId = (int) ($_POST["academic_year_id"] ?? 0);

    if (
        $firstName === "" ||
        $lastName === "" ||
        !filter_var($email, FILTER_VALIDATE_EMAIL) ||
        !in_array($gender, ["Male", "Female", "Other"], true) ||
        $departmentId <= 0 ||
        $courseId <= 0 ||
        $academicYearId <= 0
    ) {
        $errorMessage = "Please provide valid information in all required fields.";

    } else {

        // Confirm that the selected course belongs to the department.
        $checkCourse = $pdo->prepare(
            "SELECT id FROM courses WHERE id = ? AND department_id = ?"
        );
        $checkCourse->execute([$courseId, $departmentId]);

        if (!$checkCourse->fetch()) {
            $errorMessage = "The selected course does not belong to that department.";

        } else {

            try {

                $sql = "INSERT INTO students
                    (first_name, last_name, email, phone, gender, course_id, academic_year_id)
                    VALUES (?, ?, ?, ?, ?, ?, ?)";

                $statement = $pdo->prepare($sql);

               $statement->execute([
    $firstName,
    $lastName,
    $email,
    $phone !== "" ? $phone : null,
    $gender,
    $courseId,
    $academicYearId
]);

$registeredStudentId = $pdo->lastInsertId();

$successMessage = "Student registered successfully!";

            } catch (PDOException $error) {

                if ($error->getCode() === "23000") {
                    $errorMessage = "This email address is already registered.";
                } else {
                    error_log($error->getMessage());
                    $errorMessage = "Registration failed. Please try again.";
                }

            }
        }
    }
}

// Get all departments
$departmentsQuery = $pdo->query(
    "SELECT * FROM departments ORDER BY department_name"
);
$departments = $departmentsQuery->fetchAll();

// Get all courses
$coursesQuery = $pdo->query(
    "SELECT * FROM courses ORDER BY course_name"
);
$courses = $coursesQuery->fetchAll();

// Get academic years
$yearsQuery = $pdo->query(
    "SELECT * FROM academic_years ORDER BY start_date DESC"
);
$academicYears = $yearsQuery->fetchAll();

$pageTitle = "Register Student";

include 'includes/header.php';

?>

<h1 class="page-title">Register New Student</h1>

<?php if ($successMessage): ?>

    <div class="success">

        <h3>Registration Successful!</h3>

        <p><?= htmlspecialchars($successMessage) ?></p>

        <br>

        <strong>Student ID:</strong>
        <?= htmlspecialchars($registeredStudentId) ?>

        <br>

        <strong>Registration Status:</strong> Active

        <br><br>

        <p>Please keep your Student ID for future reference.</p>

    </div>

<?php endif; ?>

<?php if ($errorMessage): ?>
    <div class="error">
        <?= htmlspecialchars($errorMessage) ?>
    </div>
<?php endif; ?>

<div class="form-container">

    <form method="POST" action="register.php">

        <div class="form-group">
            <label for="first_name">First Name</label>
            <input type="text" id="first_name" name="first_name" required>
        </div>

        <div class="form-group">
            <label for="last_name">Last Name</label>
            <input type="text" id="last_name" name="last_name" required>
        </div>

        <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" required>
        </div>

        <div class="form-group">
            <label for="phone">Phone Number</label>
            <input type="tel" id="phone" name="phone">
        </div>

        <div class="form-group">
            <label for="gender">Gender</label>
            <select id="gender" name="gender" required>
                <option value="">Select Gender</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
                <option value="Other">Other</option>
            </select>
        </div>

        <div class="form-group">
            <label for="department_id">Department</label>
            <select id="department_id" name="department_id" required>
                <option value="">Select Department</option>

                <?php foreach ($departments as $department): ?>
                    <option value="<?= $department['id'] ?>">
                        <?= htmlspecialchars($department['department_name']) ?>
                    </option>
                <?php endforeach; ?>

            </select>
        </div>

        <div class="form-group">
            <label for="course_id">Course</label>
            <select id="course_id" name="course_id" required>
                <option value="">Select Department First</option>

                <?php foreach ($courses as $course): ?>
                    <option
                        value="<?= $course['id'] ?>"
                        data-department="<?= $course['department_id'] ?>"
                    >
                        <?= htmlspecialchars($course['course_name']) ?>
                    </option>
                <?php endforeach; ?>

            </select>
        </div>

        <div class="form-group">
            <label for="academic_year_id">Academic Year</label>
            <select id="academic_year_id" name="academic_year_id" required>

                <option value="">Select Academic Year</option>

                <?php foreach ($academicYears as $year): ?>
                    <option value="<?= $year['id'] ?>">
                        <?= htmlspecialchars($year['academic_year']) ?>
                        - <?= htmlspecialchars($year['semester']) ?>
                    </option>
                <?php endforeach; ?>

            </select>
        </div>

        <button type="submit" class="btn">
            Register Student
        </button>

    </form>

</div>

<?php include 'includes/footer.php'; ?>
?>
