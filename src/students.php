<?php

require_once 'includes/db.php';
require_once 'includes/auth.php';
requireAdmin();


if (isset($_SESSION['admin_id']) && empty($_SESSION['delete_token'])) {
    $_SESSION['delete_token'] = bin2hex(random_bytes(32));
}

// Search functionality
$search = trim($_GET['search'] ?? '');

$sql = "
    SELECT
        s.id,
        s.first_name,
        s.last_name,
        s.email,
        s.phone,
        s.gender,
        s.registration_date,
        s.status,
        c.course_name,
        d.department_name,
        ay.academic_year,
        ay.semester

    FROM students s

    JOIN courses c ON s.course_id = c.id
    JOIN departments d ON c.department_id = d.id
    JOIN academic_years ay ON s.academic_year_id = ay.id
";

if ($search !== "") {
    $sql .= "
        WHERE s.first_name LIKE ?
        OR s.last_name LIKE ?
        OR s.email LIKE ?
    ";
}

$sql .= " ORDER BY s.registration_date DESC";

$statement = $pdo->prepare($sql);

if ($search !== "") {
    $term = "%$search%";
    $statement->execute([$term, $term, $term]);
} else {
    $statement->execute();
}

$students = $statement->fetchAll();

$pageTitle = "Student Records";

include 'includes/header.php';
?>

<h1 class="page-title">Student Records</h1>

<form method="GET" action="students.php" style="margin-bottom: 20px;">

    <input
        type="text"
        name="search"
        placeholder="Search by name or email..."
        value="<?= htmlspecialchars($search) ?>"
        style="padding: 11px; width: 300px; max-width: 100%;"
    >

    <button type="submit" class="btn">Search</button>

    <a href="students.php" class="btn">Reset</a>

</form>

<div class="table-container">

    <table>

        <thead>
            <tr>
                <th>ID</th>
                <th>Student Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Department</th>
                <th>Course</th>
                <th>Academic Year</th>
                <th>Status</th>
<?php if (isset($_SESSION['admin_id'])): ?>
    <th>Actions</th>
<?php endif; ?>

            </tr>
        </thead>

        <tbody>

            <?php if (count($students) > 0): ?>

                <?php foreach ($students as $student): ?>

                    <tr>
                        <td><?= $student['id'] ?></td>

                        <td>
                            <?= htmlspecialchars(
                                $student['first_name'] . ' ' . $student['last_name']
                            ) ?>
                        </td>

                        <td><?= htmlspecialchars($student['email']) ?></td>

                        <td><?= htmlspecialchars($student['phone'] ?? '') ?></td>

                        <td><?= htmlspecialchars($student['department_name']) ?></td>

                        <td><?= htmlspecialchars($student['course_name']) ?></td>

                        <td>
                            <?= htmlspecialchars($student['academic_year']) ?>
                            <?= htmlspecialchars($student['semester']) ?>
                        </td>

                        <td><?= htmlspecialchars($student['status']) ?></td>
<?php if (isset($_SESSION['admin_id'])): ?>

<td>

    <a href="edit.php?id=<?= $student['id'] ?>" class="btn">
        Edit
    </a>

    <form method="POST" action="delete.php"
          style="display:inline;"
          onsubmit="return confirm('Are you sure you want to delete this student?');">

        <input type="hidden" name="id"
               value="<?= $student['id'] ?>">

        <input type="hidden" name="token"
               value="<?= htmlspecialchars($_SESSION['delete_token']) ?>">

        <button type="submit" class="btn"
                style="background:#dc3545;">
            Delete
        </button>

    </form>

</td>

<?php endif; ?>

                    </tr>

                <?php endforeach; ?>

            <?php else: ?>

                <tr>
                    <td colspan="8">No student records found.</td>
<td colspan="<?= isset($_SESSION['admin_id']) ? 9 : 8 ?>">
    No student records found.
</td>

                </tr>

            <?php endif; ?>

        </tbody>

    </table>

</div>

<br>

<p>Total records: <strong><?= count($students) ?></strong></p>

<?php include 'includes/footer.php'; ?>
