<?php

if (!isset($base_path)) $base_path = '';
if (!isset($page_title)) $page_title = 'Find Course';
if (!isset($active_nav)) $active_nav = '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title) ?> - Find Course</title>
    <link rel="stylesheet" href="<?= $base_path ?>css/styles.css">
</head>
<body>

<header>
    <a class="logo" href="<?= $base_path ?>Home.php">Find<span>Course</span></a>
    <div class="header-right">
        <?php if (isset($_SESSION['user_logged_in'])): ?>
             <?= htmlspecialchars($_SESSION['user_username']) ?>
        <?php elseif (isset($_SESSION['admin_logged_in'])): ?>
             Admin: <?= htmlspecialchars($_SESSION['admin_username']) ?>
        <?php endif; ?>
    </div>
</header>

<nav>
    <div class="nav-links" id="navLinks">
        <a class="navbk <?= $active_nav === 'home' ? 'active' : '' ?>" href="<?= $base_path ?>Home.php"> Home</a>
        <a class="navbk <?= $active_nav === 'courses' ? 'active' : '' ?>" href="<?= $base_path ?>all_courses.php"> Courses</a>
        <?php if (isset($_SESSION['user_logged_in'])): ?>
            <a class="navbk <?= $active_nav === 'profile' ? 'active' : '' ?>" href="<?= $base_path ?>User/profile.php"> My Profile</a>
            <a class="navbk <?= $active_nav === 'contact' ? 'active' : '' ?>" href="<?= $base_path ?>contact.php"> Contact</a>
            <div class="nav-spacer"></div>
            <span class="nav-user"> <?= htmlspecialchars($_SESSION['user_username']) ?></span>
            <a class="nav-logout" href="<?= $base_path ?>logout.php">Logout</a>
        <?php elseif (isset($_SESSION['admin_logged_in'])): ?>
            <a class="navbk" href="<?= $base_path ?>Admin/dashboard_admin.php"> Dashboard</a>
            <div class="nav-spacer"></div>
            <span class="nav-user"> <?= htmlspecialchars($_SESSION['admin_username']) ?></span>
            <a class="nav-logout" href="<?= $base_path ?>logout.php">Logout</a>
        <?php else: ?>
            <a class="navbk <?= $active_nav === 'contact' ? 'active' : '' ?>" href="<?= $base_path ?>contact.php"> Contact</a>
            <div class="nav-spacer"></div>
            <a class="navbk <?= $active_nav === 'login' ? 'active' : '' ?>" href="<?= $base_path ?>User/User_Login.php">User Login</a>
            <a class="navbk <?= $active_nav === 'admin' ? 'active' : '' ?>" href="<?= $base_path ?>Admin/Admin_Login.php">Admin Login</a>
        <?php endif; ?>
    </div>
    <div class="hamburger" onclick="document.getElementById('navLinks').classList.toggle('open')">
        <span></span><span></span><span></span>
    </div>
</nav>
