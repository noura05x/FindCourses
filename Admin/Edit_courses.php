<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: Admin_Login.php"); exit;
}
include("../db.php");

$id = intval($_GET['id'] ?? 0);
if (!$id) { header("Location: all_coursesAdmin.php"); exit; }

$stmt = $conn->prepare("SELECT * FROM courses WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$course = $stmt->get_result()->fetch_assoc();
if (!$course) { header("Location: all_coursesAdmin.php"); exit; }

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

    $image_path = $course['image'];
    if (!empty($_FILES['image']['name'])) {
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $allowed = ['jpg','jpeg','png','gif','webp'];
        if (in_array(strtolower($ext), $allowed)) {
            if (!is_dir('uploads')) mkdir('uploads', 0777, true);
            $filename = 'uploads/' . time() . '_' . basename($_FILES['image']['name']);
            move_uploaded_file($_FILES['image']['tmp_name'], $filename);
            $image_path = $filename;
        }
    }

    if (empty($errors)) {
        $stmt2 = $conn->prepare("UPDATE courses SET course_name=?, course_description=?, course_subjects=?, course_location=?, course_mode=?, instructor_name=?, instructor_contact=?, course_start_date=?, course_end_date=?, image=?, course_price=? WHERE id=?");
        $stmt2->bind_param("ssssssssssdi", $course_name, $course_description, $course_subjects, $course_location, $course_mode, $instructor_name, $instructor_contact, $course_start_date, $course_end_date, $image_path, $course_price, $id);
        if ($stmt2->execute()) {
            $success = "Course updated successfully!";
            $course = array_merge($course, compact('course_name','instructor_name','instructor_contact','course_description','course_subjects','course_location','course_mode','course_start_date','course_end_date','course_price'));
            $course['image'] = $image_path;
        } else {
            $errors[] = "Update failed.";
        }
    }
}

$base_path = '../';
$page_title = 'Edit Course';
include('../includes/header.php');
?>

<main>
    <a href="all_coursesAdmin.php" style="color:var(--primary);text-decoration:none;font-size:0.9rem;">← Back to Courses</a>

    <div style="max-width:700px;margin:24px auto 0;">
        <h1 class="page-title">Edit Course</h1>

        <?php if ($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>
        <?php foreach ($errors as $e): ?><div class="alert alert-error"><?= $e ?></div><?php endforeach; ?>

        <div class="form-container" style="max-width:100%;">
            <form method="POST" enctype="multipart/form-data">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                    <div class="form-group">
                        <label>Course Name *</label>
                        <input type="text" name="course_name" required value="<?= htmlspecialchars($course['course_name']) ?>">
                    </div>
                    <div class="form-group">
                        <label>Mode</label>
                        <select name="course_mode">
                            <option value="Online" <?= $course['course_mode'] === 'Online' ? 'selected' : '' ?>>Online</option>
                            <option value="Offline" <?= $course['course_mode'] === 'Offline' ? 'selected' : '' ?>>Offline</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Instructor Name</label>
                        <input type="text" name="instructor_name" value="<?= htmlspecialchars($course['instructor_name']) ?>">
                    </div>
                    <div class="form-group">
                        <label>Instructor Contact</label>
                        <input type="text" name="instructor_contact" value="<?= htmlspecialchars($course['instructor_contact']) ?>">
                    </div>
                    <div class="form-group">
                        <label>Location</label>
                        <input type="text" name="course_location" value="<?= htmlspecialchars($course['course_location']) ?>">
                    </div>
                    <div class="form-group">
                        <label>Price (SAR)</label>
                        <input type="number" name="course_price" min="0" step="0.01" value="<?= $course['course_price'] ?? 0 ?>">
                    </div>
                    <div class="form-group">
                        <label>Start Date</label>
                        <input type="date" name="course_start_date" value="<?= $course['course_start_date'] ?>">
                    </div>
                    <div class="form-group">
                        <label>End Date</label>
                        <input type="date" name="course_end_date" value="<?= $course['course_end_date'] ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label>Subjects</label>
                    <input type="text" name="course_subjects" value="<?= htmlspecialchars($course['course_subjects']) ?>">
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="course_description" rows="4"><?= htmlspecialchars($course['course_description']) ?></textarea>
                </div>
                <div class="form-group">
                    <label>Course Image <small style="color:var(--text-muted)">(leave blank to keep current)</small></label>
                    <?php if ($course['image']): ?>
                        <img src="<?= htmlspecialchars($course['image']) ?>" onerror="this.style.display='none'" style="width:120px;border-radius:8px;margin-bottom:8px;display:block;">
                    <?php endif; ?>
                    <input type="file" name="image" accept="image/*">
                </div>
                <button type="submit" class="btn btn-primary btn-block"> Save Changes</button>
            </form>
        </div>
    </div>
</main>

<?php include('../includes/footer.php'); ?>
