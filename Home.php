<?php
session_start();
include('db.php');
$base_path = '';
$page_title = 'Home';
$active_nav = 'home';
include('includes/header.php');


$total_courses = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM courses"))[0];
$online_courses = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM courses WHERE course_mode='Online'"))[0];
?>

<div class="hero">
    <h1>Invest in your <em>education</em></h1>
    <p>Explore courses, connect with expert instructors, and learn at your own pace — online or in person.</p>
    <a href="all_courses.php" class="btn btn-white" style="margin-right:12px;">Browse Courses</a>
    <?php if (!isset($_SESSION['user_logged_in']) && !isset($_SESSION['admin_logged_in'])): ?>
        <a href="User/AddUser.php" class="btn btn-accent">Sign Up Free</a>
    <?php endif; ?>
</div>

<main>
   
    <div class="stats-grid" style="margin-top:0;">
        <div class="stat-card">
            <div class="stat-icon purple">📖</div>
            <div class="stat-info">
                <h3><?= $total_courses ?></h3>
                <p>Total Courses</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon orange">💻</div>
            <div class="stat-info">
                <h3><?= $online_courses ?></h3>
                <p>Online Courses</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon green">🎓</div>
            <div class="stat-info">
                <h3>Expert</h3>
                <p>Instructors</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon blue">🌍</div>
            <div class="stat-info">
                <h3>Flexible</h3>
                <p>Learning Options</p>
            </div>
        </div>
    </div>

    <!-- Latest Courses -->
    <h2 class="page-title">Latest Courses</h2>
    <p class="page-subtitle">Recently added courses just for you</p>

    <div class="course-grid">
        <?php
        $result = mysqli_query($conn, "SELECT * FROM courses ORDER BY id DESC LIMIT 6");
        while ($course = mysqli_fetch_assoc($result)):
            $img = !empty($course['image']) ? 'Admin/' . $course['image'] : 'image/photo1.jpg';
            $stars = round($course['course_rating']);
            $star_html = str_repeat('★', $stars) . str_repeat('☆', 5 - $stars);
        ?>
        <div class="course-card">
            <div class="course-card-top">
                <img src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($course['course_name']) ?>" onerror="this.style.display='none'">
            </div>
            <div class="course-card-body">
                <span class="badge <?= $course['course_mode'] === 'Online' ? 'badge-online' : 'badge-offline' ?>">
                    <?= $course['course_mode'] ?>
                </span>
                <h3><?= htmlspecialchars($course['course_name']) ?></h3>
                <p> <?= htmlspecialchars($course['instructor_name']) ?></p>
                <p> <?= htmlspecialchars($course['course_location']) ?></p>
                <?php if ($course['course_rating'] > 0): ?>
                    <div class="stars"><?= $star_html ?> <small style="color:var(--text-muted)">(<?= $course['course_rating'] ?>)</small></div>
                <?php endif; ?>
                <div class="card-footer">
                    <a href="course_details.php?id=<?= $course['id'] ?>" class="btn btn-primary btn-sm">View Details</a>
                    <?php if (!empty($course['course_price']) && $course['course_price'] > 0): ?>
                        <span style="font-weight:700;color:var(--accent)">SAR <?= $course['course_price'] ?></span>
                    <?php else: ?>
                        <span style="font-weight:600;color:var(--success)">Free</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    </div>

    <div style="text-align:center;margin-top:36px;">
        <a href="all_courses.php" class="btn btn-outline">View All Courses →</a>
    </div>

    <!-- Why Us -->
    <div style="margin-top:60px;background:white;border-radius:20px;padding:40px;box-shadow:var(--card-shadow);text-align:center;">
        <h2 class="page-title" style="margin-bottom:12px;">Why Choose FindCourse?</h2>
        <p style="color:var(--text-muted);max-width:650px;margin:0 auto 32px;line-height:1.7;">
            Our platform connects learners with top instructors across Saudi Arabia. Whether you prefer online or in-person, we have the right course for your goals.
        </p>
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:24px;text-align:center;">
            <div>
                <div style="font-size:2.5rem;margin-bottom:8px;">🔍</div>
                <h4 style="color:var(--primary);margin-bottom:6px;">Easy Search</h4>
                <p style="color:var(--text-muted);font-size:0.9rem;">Filter by subject, mode, and location</p>
            </div>
            <div>
                <div style="font-size:2.5rem;margin-bottom:8px;">⭐</div>
                <h4 style="color:var(--primary);margin-bottom:6px;">Rated Courses</h4>
                <p style="color:var(--text-muted);font-size:0.9rem;">Real ratings from real students</p>
            </div>
            <div>
                <div style="font-size:2.5rem;margin-bottom:8px;">🔒</div>
                <h4 style="color:var(--primary);margin-bottom:6px;">Secure Platform</h4>
                <p style="color:var(--text-muted);font-size:0.9rem;">Your data is safe with us</p>
            </div>
        </div>
    </div>
</main>

<?php include('includes/footer.php'); ?>
