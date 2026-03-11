<?php
session_start();
if (!isset($_SESSION['user_logged_in'])) {
    header("Location: User_Login.php"); exit;
}
include("../db.php");

$base_path = '../';
$page_title = 'Dashboard';
$active_nav = 'home';
include('../includes/header.php');

$result = mysqli_query($conn, "SELECT * FROM courses ORDER BY id DESC LIMIT 6");
?>

<main>
    <h1 class="page-title">Hello, <?= htmlspecialchars($_SESSION['user_username']) ?>! </h1>
    <p class="page-subtitle">Welcome back to FindCourse. What would you like to learn today?</p>

    <div style="display:flex;gap:12px;flex-wrap:wrap;margin-bottom:36px;">
        <a href="../all_courses.php" class="btn btn-primary"> Browse All Courses</a>
        <a href="profile.php" class="btn btn-outline"> My Profile</a>
        <a href="../contact.php" class="btn btn-outline"> Contact Us</a>
    </div>

    <h2 style="font-family:'Playfair Display',serif;color:var(--primary);margin-bottom:20px;">Latest Courses</h2>
    <div class="course-grid">
        <?php while ($course = mysqli_fetch_assoc($result)):
            $img = !empty($course['image']) ? '../Admin/' . $course['image'] : '../image/photo1.jpg';
            $stars = round($course['course_rating']);
            $star_html = str_repeat('★', $stars) . str_repeat('☆', 5 - $stars);
        ?>
        <div class="course-card">
            <img src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($course['course_name']) ?>" onerror="this.src='../image/photo1.jpg'">
            <div class="course-card-body">
                <span class="badge <?= $course['course_mode'] === 'Online' ? 'badge-online' : 'badge-offline' ?>"><?= $course['course_mode'] ?></span>
                <h3><?= htmlspecialchars($course['course_name']) ?></h3>
                <p> <?= htmlspecialchars($course['instructor_name']) ?></p>
                <p> <?= htmlspecialchars($course['course_location']) ?></p>
                <?php if ($course['course_rating'] > 0): ?>
                    <div class="stars"><?= $star_html ?></div>
                <?php endif; ?>
                <div class="card-footer">
                    <a href="../course_details.php?id=<?= $course['id'] ?>" class="btn btn-primary btn-sm">View Details</a>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
</main>

<?php include('../includes/footer.php'); ?>
