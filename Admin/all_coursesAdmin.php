<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: Admin_Login.php"); exit;
}
include("../db.php");

$base_path = '../';
$page_title = 'Manage Courses';
include('../includes/header.php');

$courses = mysqli_query($conn, "SELECT * FROM courses ORDER BY id DESC");
?>

<main>
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:24px;">
        <div>
            <h1 class="page-title" style="margin-bottom:4px;">Manage Courses</h1>
            <p style="color:var(--text-muted);font-size:0.9rem;"><?= mysqli_num_rows($courses) ?> courses total</p>
        </div>
        <a href="add_course.php" class="btn btn-primary"> Add New Course</a>
    </div>

    <div style="overflow-x:auto;">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Course</th>
                    <th>Instructor</th>
                    <th>Mode</th>
                    <th>Location</th>
                    <th>Price</th>
                    <th>Rating</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($c = mysqli_fetch_assoc($courses)):
                    $img = !empty($c['image']) ? $c['image'] : '../image/photo1.jpg';
                ?>
                <tr>
                    <td><img src="<?= htmlspecialchars($img) ?>" onerror="this.src='../image/photo1.jpg'" style="width:56px;height:40px;object-fit:cover;border-radius:6px;"></td>
                    <td><strong><?= htmlspecialchars($c['course_name']) ?></strong></td>
                    <td><?= htmlspecialchars($c['instructor_name']) ?></td>
                    <td><span class="badge <?= $c['course_mode'] === 'Online' ? 'badge-online' : 'badge-offline' ?>"><?= $c['course_mode'] ?></span></td>
                    <td><?= htmlspecialchars($c['course_location']) ?></td>
                    <td><?= (!empty($c['course_price']) && $c['course_price'] > 0) ? 'SAR ' . $c['course_price'] : '<span style="color:var(--success)">Free</span>' ?></td>
                    <td><?= $c['course_rating'] > 0 ? '⭐ ' . $c['course_rating'] : '—' ?></td>
                    <td style="white-space:nowrap;">
                        <a href="Edit_courses.php?id=<?= $c['id'] ?>" class="btn btn-outline btn-sm">Edit</a>
                        <a href="delete_course.php?id=<?= $c['id'] ?>" class="btn btn-sm" style="background:#fee2e2;color:#991b1b;margin-left:4px;"
                           onclick="return confirm('Are you sure you want to delete this course?')"> Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</main>

<?php include('../includes/footer.php'); ?>
