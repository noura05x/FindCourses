<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: Admin_Login.php"); exit;
}
include("../db.php");

$total_courses  = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM courses"))[0];
$total_users    = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM users"))[0];
$total_messages = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM messages"))[0];
$total_admins   = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM admin"))[0];

$base_path = '../';
$page_title = 'Admin Dashboard';
$active_nav = '';
include('../includes/header.php');
?>

<main>
    <h1 class="page-title">Admin Dashboard</h1>
    <p class="page-subtitle">Welcome back, <?= htmlspecialchars($_SESSION['admin_username']) ?>!</p>

    <!-- Stats -->
    <div class="stats-grid">
        <div class="stat-card">
        
            <div class="stat-info"><h3><?= $total_courses ?></h3><p>Total Courses</p></div>
        </div>
        <div class="stat-card">
            
            <div class="stat-info"><h3><?= $total_users ?></h3><p>Registered Users</p></div>
        </div>
        <div class="stat-card">
            
            <div class="stat-info"><h3><?= $total_messages ?></h3><p>Messages</p></div>
        </div>
        <div class="stat-card">
           
            <div class="stat-info"><h3><?= $total_admins ?></h3><p>Admins</p></div>
        </div>
    </div>

  
    <h3 style="font-family:'Playfair Display',serif;color:var(--primary);margin-bottom:16px;">Quick Actions</h3>
    <div style="display:flex;gap:12px;flex-wrap:wrap;margin-bottom:40px;">
        <a href="add_course.php" class="btn btn-primary"> Add Course</a>
        <a href="all_coursesAdmin.php" class="btn btn-outline"> Manage Courses</a>
        <a href="admin_messages.php" class="btn btn-outline"> View Messages</a>
        <a href="AddAdmin.php" class="btn btn-outline"> Add Admin</a>
    </div>

   
    <h3 style="font-family:'Playfair Display',serif;color:var(--primary);margin-bottom:16px;">Recent Courses</h3>
    <div style="overflow-x:auto;">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Course</th>
                    <th>Instructor</th>
                    <th>Mode</th>
                    <th>Location</th>
                    <th>Rating</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $courses = mysqli_query($conn, "SELECT * FROM courses ORDER BY id DESC LIMIT 8");
                while ($c = mysqli_fetch_assoc($courses)):
                ?>
                <tr>
                    <td><strong><?= htmlspecialchars($c['course_name']) ?></strong></td>
                    <td><?= htmlspecialchars($c['instructor_name']) ?></td>
                    <td><span class="badge <?= $c['course_mode'] === 'Online' ? 'badge-online' : 'badge-offline' ?>"><?= $c['course_mode'] ?></span></td>
                    <td><?= htmlspecialchars($c['course_location']) ?></td>
                    <td><?= $c['course_rating'] > 0 ? '⭐ ' . $c['course_rating'] : '—' ?></td>
                    <td>
                        <a href="Edit_courses.php?id=<?= $c['id'] ?>" class="btn btn-outline btn-sm">Edit</a>
                        <a href="delete_course.php?id=<?= $c['id'] ?>" class="btn btn-sm" style="background:#fee2e2;color:#991b1b;"
                           onclick="return confirm('Delete this course?')">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</main>

<?php include('../includes/footer.php'); ?>
