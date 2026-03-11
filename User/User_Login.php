<?php
session_start();
include("../db.php");

if (isset($_SESSION['user_logged_in'])) {
    header("Location: HomeUser.php"); exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['name']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT * FROM users WHERE name = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();


    $valid = false;
    if ($user) {
        if (password_verify($password, $user['password'])) {
            $valid = true;
        } elseif ($user['password'] === $password) {
           
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $upd = $conn->prepare("UPDATE users SET password = ? WHERE ID = ?");
            $upd->bind_param("si", $hashed, $user['ID']);
            $upd->execute();
            $valid = true;
        }
    }

    if ($valid) {
        $_SESSION['user_logged_in'] = true;
        $_SESSION['user_id'] = $user['ID'];
        $_SESSION['user_username'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        header("Location: HomeUser.php"); exit;
    } else {
        $error = "Invalid username or password.";
    }
}

$base_path = '../';
$page_title = 'User Login';
$active_nav = 'login';
include('../includes/header.php');
?>

<main>
    <div class="form-container">
        <h2>Welcome Back!</h2>
        <p class="form-subtitle">Login to your FindCourse account</p>

        <?php if ($error): ?><div class="alert alert-error"><?= $error ?></div><?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="name" required placeholder="Your username" autofocus>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required placeholder="Your password">
            </div>
            <button type="submit" class="btn btn-primary btn-block" style="margin-top:8px;">Login</button>
        </form>

        <p style="text-align:center;margin-top:20px;color:var(--text-muted);font-size:0.9rem;">
            Don't have an account? <a href="AddUser.php" style="color:var(--primary);font-weight:600;">Sign Up</a>
        </p>
    </div>
</main>

<?php include('../includes/footer.php'); ?>
