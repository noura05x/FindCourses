<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: Admin_Login.php"); exit;
}
include("../db.php");

$error = $success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email    = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm  = trim($_POST['confirm_password']);

    if (strlen($username) < 3) {
        $error = "Username must be at least 3 characters.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email address.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    } elseif ($password !== $confirm) {
        $error = "Passwords do not match.";
    } else {
        $chk = $conn->prepare("SELECT ID FROM admin WHERE username = ?");
        $chk->bind_param("s", $username);
        $chk->execute();
        $chk->store_result();
        if ($chk->num_rows > 0) {
            $error = "Username already exists.";
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $ins = $conn->prepare("INSERT INTO admin (username, password, email) VALUES (?,?,?)");
            $ins->bind_param("sss", $username, $hashed, $email);
            if ($ins->execute()) {
                $success = "Admin account created successfully!";
            } else {
                $error = "Failed to create admin.";
            }
        }
    }
}

$base_path = '../';
$page_title = 'Add Admin';
include('../includes/header.php');
?>

<main>
    <div style="max-width:480px;margin:0 auto;">
        <a href="dashboard_admin.php" style="color:var(--primary);text-decoration:none;font-size:0.9rem;">← Back to Dashboard</a>
        <h1 class="page-title" style="margin-top:16px;">Add Admin</h1>

        <div class="form-container" style="max-width:100%;margin-top:16px;">
            <?php if ($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>
            <?php if ($error): ?><div class="alert alert-error"><?= $error ?></div><?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label>Username *</label>
                    <input type="text" name="username" required value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label>Email *</label>
                    <input type="email" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label>Password * <small style="color:var(--text-muted)">(min. 6 chars)</small></label>
                    <input type="password" name="password" required>
                </div>
                <div class="form-group">
                    <label>Confirm Password *</label>
                    <input type="password" name="confirm_password" required>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Create Admin Account</button>
            </form>
        </div>
    </div>
</main>

<?php include('../includes/footer.php'); ?>
