<?php
session_start();
include('db.php');

$id = intval($_GET['id'] ?? 0);
if (!$id) { header("Location: all_courses.php"); exit; }

$stmt = $conn->prepare("SELECT * FROM courses WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$course = $stmt->get_result()->fetch_assoc();
if (!$course) { header("Location: all_courses.php"); exit; }


$rating_msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['rating']) && isset($_SESSION['user_logged_in'])) {
    $new_rating = intval($_POST['rating']);
    if ($new_rating >= 1 && $new_rating <= 5) {
       
        $stmt2 = $conn->prepare("UPDATE courses SET course_rating = ? WHERE id = ?");
        $stmt2->bind_param("di", $new_rating, $id);
        $stmt2->execute();
        $course['course_rating'] = $new_rating;
        $rating_msg = 'success';
    }
}

$base_path = '';
$page_title = htmlspecialchars($course['course_name']);
$active_nav = 'courses';
include('includes/header.php');

$img = !empty($course['image']) ? 'Admin/' . $course['image'] : 'image/photo1.jpg';
$stars = round($course['course_rating']);
$star_html = str_repeat('★', $stars) . str_repeat('☆', 5 - $stars);
?>

<main>
    <a href="all_courses.php" style="color:var(--primary);text-decoration:none;font-size:0.9rem;">← Back to Courses</a>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:36px;margin-top:24px;" class="detail-grid">
       
        <div>
            <img src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($course['course_name']) ?>"
                 onerror="this.src='image/photo1.jpg'"
                 style="width:100%;border-radius:18px;object-fit:cover;max-height:350px;box-shadow:var(--card-shadow)">
        </div>

        
        <div>
            <span class="badge <?= $course['course_mode'] === 'Online' ? 'badge-online' : 'badge-offline' ?>" style="margin-bottom:12px;">
                <?= $course['course_mode'] ?>
            </span>
            <h1 style="font-family:'Playfair Display',serif;font-size:2rem;color:var(--text);margin-bottom:12px;">
                <?= htmlspecialchars($course['course_name']) ?>
            </h1>

            <?php if ($course['course_rating'] > 0): ?>
            <div class="stars" style="font-size:1.3rem;margin-bottom:14px;">
                <?= $star_html ?> <span style="color:var(--text-muted);font-size:0.95rem;">(<?= $course['course_rating'] ?>/5)</span>
            </div>
            <?php endif; ?>

            <div style="background:var(--bg);border-radius:14px;padding:20px;margin-bottom:20px;">
                <p style="margin-bottom:10px;"><strong>Instructor:</strong> <?= htmlspecialchars($course['instructor_name']) ?></p>
                <p style="margin-bottom:10px;"><strong> Contact:</strong> <?= htmlspecialchars($course['instructor_contact']) ?></p>
                <p style="margin-bottom:10px;"><strong> Location:</strong> <?= htmlspecialchars($course['course_location']) ?></p>
                <?php if ($course['course_start_date']): ?>
                <p style="margin-bottom:10px;"><strong> Start:</strong> <?= $course['course_start_date'] ?></p>
                <p style="margin-bottom:10px;"><strong> End:</strong> <?= $course['course_end_date'] ?></p>
                <?php endif; ?>
                <?php if (!empty($course['course_price']) && $course['course_price'] > 0): ?>
                <p><strong> Price:</strong> <span style="color:var(--accent);font-weight:700;">SAR <?= $course['course_price'] ?></span></p>
                <?php else: ?>
                <p><strong> Price:</strong> <span style="color:var(--success);font-weight:700;">Free</span></p>
                <?php endif; ?>
            </div>

            <?php if (!empty($course['course_subjects'])): ?>
            <div style="margin-bottom:16px;">
                <strong style="font-size:0.9rem;"> Subjects:</strong><br>
                <div style="margin-top:8px;">
                <?php foreach (explode(',', $course['course_subjects']) as $s): ?>
                    <span class="badge badge-primary" style="margin:3px;"><?= htmlspecialchars(trim($s)) ?></span>
                <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    
    <?php if ($course['course_description']): ?>
    <div style="background:white;border-radius:16px;padding:28px;margin-top:28px;box-shadow:var(--card-shadow);">
        <h3 style="color:var(--primary);margin-bottom:12px;font-family:'Playfair Display',serif;">About this Course</h3>
        <p style="line-height:1.8;color:var(--text-muted);"><?= nl2br(htmlspecialchars($course['course_description'])) ?></p>
    </div>
    <?php endif; ?>

    
    <div style="background:white;border-radius:16px;padding:28px;margin-top:28px;box-shadow:var(--card-shadow);">
        <h3 style="color:var(--primary);margin-bottom:16px;font-family:'Playfair Display',serif;">⭐ Rate this Course</h3>

        <?php if ($rating_msg === 'success'): ?>
            <div class="alert alert-success">Thanks for your rating! 🎉</div>
        <?php endif; ?>

        <?php if (isset($_SESSION['user_logged_in'])): ?>
        <form method="POST">
            <p style="color:var(--text-muted);margin-bottom:12px;font-size:0.9rem;">How would you rate this course?</p>
            <div class="star-rating" style="flex-direction:row-reverse;justify-content:flex-start;">
                <?php for ($i = 5; $i >= 1; $i--): ?>
                    <input type="radio" name="rating" id="star<?= $i ?>" value="<?= $i ?>" <?= $stars == $i ? 'checked' : '' ?>>
                    <label for="star<?= $i ?>" title="<?= $i ?> star<?= $i > 1 ? 's' : '' ?>" style="font-size:2rem;">★</label>
                <?php endfor; ?>
            </div>
            <button type="submit" class="btn btn-primary" style="margin-top:14px;">Submit Rating</button>
        </form>
        <?php else: ?>
            <p style="color:var(--text-muted);">
                <a href="User/User_Login.php" style="color:var(--primary);font-weight:600;">Login</a> to rate this course.
            </p>
        <?php endif; ?>
    </div>
</main>

<style>
@media (max-width: 768px) {
    .detail-grid { grid-template-columns: 1fr !important; }
}
.star-rating { display:flex; gap:4px; }
.star-rating input { display:none; }
.star-rating label { cursor:pointer; color:#d1d5db; transition:color 0.1s; }
.star-rating input:checked ~ label,
.star-rating label:hover,
.star-rating label:hover ~ label { color:#f59e0b; }
</style>

<?php include('includes/footer.php'); ?>
