<?php
session_start();
include("../db.php");

if (isset($_SESSION['admin_logged_in'])) {
    header("Location: dashboard_admin.php"); exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT * FROM admin WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $admin = $stmt->get_result()->fetch_assoc();

    $valid = false;
    if ($admin) {
        if (password_verify($password, $admin['password'])) {
            $valid = true;
        } elseif ($admin['password'] === $password) {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $upd = $conn->prepare("UPDATE admin SET password = ? WHERE ID = ?");
            $upd->bind_param("si", $hashed, $admin['ID']);
            $upd->execute();
            $valid = true;
        }
    }

    if ($valid) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_id'] = $admin['ID'];
        $_SESSION['admin_username'] = $admin['username'];
        header("Location: dashboard_admin.php"); exit;
    } else {
        $error = "Invalid username or password.";
    }
}

$base_path = '../';
$page_title = 'Admin Login';
$active_nav = 'admin';
include('../includes/header.php');
?>

<main>
    <div class="form-container">
        <h2>Admin Login </h2>
        <p class="form-subtitle">Access the FindCourse admin panel</p>

        <?php if ($error): ?><div class="alert alert-error"><?= $error ?></div><?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" required placeholder="Admin username" autofocus>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required placeholder="Admin password">
            </div>
            <button type="submit" class="btn btn-primary btn-block" style="margin-top:8px;">Login to Admin Panel</button>
        </form>
    </div>
</main>

<?php include('../includes/footer.php'); ?>
