<?php
session_start();
include('db.php');
$base_path = '';
$page_title = 'All Courses';
$active_nav = 'courses';
include('includes/header.php');


$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, trim($_GET['search'])) : '';
$mode_filter = isset($_GET['mode']) ? $_GET['mode'] : '';
$location_filter = isset($_GET['location']) ? mysqli_real_escape_string($conn, trim($_GET['location'])) : '';


$where = [];
if ($search) $where[] = "(course_name LIKE '%$search%' OR instructor_name LIKE '%$search%' OR course_subjects LIKE '%$search%')";
if ($mode_filter === 'Online' || $mode_filter === 'Offline') $where[] = "course_mode = '$mode_filter'";
if ($location_filter) $where[] = "course_location LIKE '%$location_filter%'";

$where_sql = count($where) ? 'WHERE ' . implode(' AND ', $where) : '';
$result = mysqli_query($conn, "SELECT * FROM courses $where_sql ORDER BY id DESC");
$count = mysqli_num_rows($result);
?>

<main>
    <h1 class="page-title">All Courses</h1>
    <p class="page-subtitle">Explore and filter courses to find your perfect match</p>

    
    <form method="GET" class="search-bar">
        <div class="form-group" style="flex:2;min-width:200px;">
            <label> Search</label>
            <input type="text" name="search" placeholder="Course name, instructor, subject..." value="<?= htmlspecialchars($search) ?>">
        </div>
        <div class="form-group">
            <label> Mode</label>
            <select name="mode">
                <option value="">All</option>
                <option value="Online" <?= $mode_filter === 'Online' ? 'selected' : '' ?>>Online</option>
                <option value="Offline" <?= $mode_filter === 'Offline' ? 'selected' : '' ?>>Offline</option>
            </select>
        </div>
        <div class="form-group">
            <label> Location</label>
            <input type="text" name="location" placeholder="City..." value="<?= htmlspecialchars($location_filter) ?>">
        </div>
        <div style="display:flex;gap:8px;align-items:flex-end;">
            <button type="submit" class="btn btn-primary">Search</button>
            <?php if ($search || $mode_filter || $location_filter): ?>
                <a href="all_courses.php" class="btn btn-outline">Clear</a>
            <?php endif; ?>
        </div>
    </form>

    <p style="color:var(--text-muted);margin-bottom:20px;font-size:0.9rem;">
        Showing <strong><?= $count ?></strong> course<?= $count !== 1 ? 's' : '' ?>
        <?= $search ? ' for "<strong>' . htmlspecialchars($search) . '</strong>"' : '' ?>
    </p>

    <?php if ($count === 0): ?>
        <div style="text-align:center;padding:80px 20px;color:var(--text-muted);">
            <div style="font-size:3rem;margin-bottom:16px;">🔍</div>
            <h3>No courses found</h3>
            <p>Try adjusting your search or filters.</p>
            <a href="all_courses.php" class="btn btn-primary" style="margin-top:16px;">View All Courses</a>
        </div>
    <?php else: ?>
    <div class="course-grid">
        <?php while ($course = mysqli_fetch_assoc($result)):
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
                <?php if (!empty($course['course_subjects'])): ?>
                    <p style="margin-top:4px;">
                        <?php foreach (explode(',', $course['course_subjects']) as $s): ?>
                            <span class="badge badge-primary" style="margin:2px 2px 0 0;"><?= htmlspecialchars(trim($s)) ?></span>
                        <?php endforeach; ?>
                    </p>
                <?php endif; ?>
                <?php if ($course['course_rating'] > 0): ?>
                    <div class="stars" style="margin-top:8px;"><?= $star_html ?> <small style="color:var(--text-muted)">(<?= $course['course_rating'] ?>)</small></div>
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
    <?php endif; ?>
</main>

<?php include('includes/footer.php'); ?>
