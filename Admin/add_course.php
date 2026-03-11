<?php
session_start();
include('../db.php');
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: Admin_Login.php"); exit;
}

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $course_name        = trim($_POST['course_name']);
    $instructor_name    = trim($_POST['instructor_name']);
    $instructor_contact = trim($_POST['instructor_contact']);
    $course_description = trim($_POST['course_description']);
    $course_subjects    = trim($_POST['course_subjects']);
    $course_location    = trim($_POST['course_location']);
    $course_mode        = $_POST['course_mode'];
    $course_start_date  = $_POST['course_start_date'];
    $course_end_date    = $_POST['course_end_date'];
    $course_price       = floatval($_POST['course_price'] ?? 0);

    if (!$course_name) $errors[] = "Course name is required.";
    if (!$instructor_name) $errors[] = "Instructor name is required.";

    $image_path = '';
    if (!empty($_FILES['image']['name'])) {
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $allowed = ['jpg','jpeg','png','gif','webp'];
        if (!in_array(strtolower($ext), $allowed)) {
            $errors[] = "Invalid image type.";
        } else {
            if (!is_dir('uploads')) mkdir('uploads', 0777, true);
            $filename = 'uploads/' . time() . '_' . basename($_FILES['image']['name']);
            move_uploaded_file($_FILES['image']['tmp_name'], $filename);
            $image_path = $filename;
        }
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO courses (course_name, course_description, course_subjects, course_location, course_mode, instructor_name, instructor_contact, course_start_date, course_end_date, image, course_price) VALUES (?,?,?,?,?,?,?,?,?,?,?)");
        $stmt->bind_param("ssssssssssd", $course_name, $course_description, $course_subjects, $course_location, $course_mode, $instructor_name, $instructor_contact, $course_start_date, $course_end_date, $image_path, $course_price);
        if ($stmt->execute()) {
            $success = "Course added successfully!";
        } else {
            $errors[] = "Database error: " . $conn->error;
        }
    }
}

$base_path = '../';
$page_title = 'Add Course';
include('../includes/header.php');
?>

<main>
    <a href="dashboard_admin.php" style="color:var(--primary);text-decoration:none;font-size:0.9rem;">← Back to Dashboard</a>

    <div style="max-width:700px;margin:24px auto 0;">
        <h1 class="page-title">Add New Course</h1>

        <?php if ($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>
        <?php foreach ($errors as $e): ?><div class="alert alert-error"><?= $e ?></div><?php endforeach; ?>

        <div class="form-container" style="max-width:100%;">
            <form method="POST" enctype="multipart/form-data">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                    <div class="form-group">
                        <label>Course Name *</label>
                        <input type="text" name="course_name" required value="<?= htmlspecialchars($_POST['course_name'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label>Mode *</label>
                        <select name="course_mode" required>
                            <option value="Online">Online</option>
                            <option value="Offline">Offline</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Instructor Name *</label>
                        <input type="text" name="instructor_name" required value="<?= htmlspecialchars($_POST['instructor_name'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label>Instructor Contact</label>
                        <input type="text" name="instructor_contact" value="<?= htmlspecialchars($_POST['instructor_contact'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label>Location</label>
                        <input type="text" name="course_location" value="<?= htmlspecialchars($_POST['course_location'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label>Price (SAR) <small style="color:var(--text-muted)">0 = Free</small></label>
                        <input type="number" name="course_price" min="0" step="0.01" value="<?= htmlspecialchars($_POST['course_price'] ?? '0') ?>">
                    </div>
                    <div class="form-group">
                        <label>Start Date</label>
                        <input type="date" name="course_start_date" value="<?= htmlspecialchars($_POST['course_start_date'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label>End Date</label>
                        <input type="date" name="course_end_date" value="<?= htmlspecialchars($_POST['course_end_date'] ?? '') ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label>Subjects <small style="color:var(--text-muted)">(comma separated, e.g. HTML, CSS, JS)</small></label>
                    <input type="text" name="course_subjects" value="<?= htmlspecialchars($_POST['course_subjects'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label>Course Description</label>
                    <textarea name="course_description" rows="4"><?= htmlspecialchars($_POST['course_description'] ?? '') ?></textarea>
                </div>
                <div class="form-group">
                    <label>Course Image</label>
                    <input type="file" name="image" accept="image/*">
                </div>
                <button type="submit" class="btn btn-primary btn-block"> Add Course</button>
            </form>
        </div>
    </div>
</main>

<?php include('../includes/footer.php'); ?>
