<?php
session_start();
include("../db.php");

if (isset($_SESSION['user_logged_in'])) {
    header("Location: HomeUser.php"); exit;
}

$error = $success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['name']);
    $email    = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm  = trim($_POST['confirm_password']);

    if (strlen($username) < 3) {
        $error = "Username must be at least 3 characters.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    } elseif ($password !== $confirm) {
        $error = "Passwords do not match.";
    } else {
        
        $stmt = $conn->prepare("SELECT ID FROM users WHERE name = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = "Username already taken. Please choose another.";
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $ins = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
            $ins->bind_param("sss", $username, $email, $hashed);
            if ($ins->execute()) {
                $_SESSION['user_logged_in'] = true;
                $_SESSION['user_id'] = $conn->insert_id;
                $_SESSION['user_username'] = $username;
                $_SESSION['user_email'] = $email;
                header("Location: HomeUser.php"); exit;
            } else {
                $error = "Registration failed. Please try again.";
            }
        }
    }
}

$base_path = '../';
$page_title = 'Sign Up';
$active_nav = 'login';
include('../includes/header.php');
?>

<main>
    <div class="form-container">
        <h2>Create Account </h2>
        <p class="form-subtitle">Join FindCourse and start learning today</p>

        <?php if ($error): ?><div class="alert alert-error"><?= $error ?></div><?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Username *</label>
                <input type="text" name="name" required placeholder="Choose a username" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Email Address *</label>
                <input type="email" name="email" required placeholder="your@email.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Password * <small style="color:var(--text-muted)">(min. 6 characters)</small></label>
                <input type="password" name="password" required placeholder="Create a password">
            </div>
            <div class="form-group">
                <label>Confirm Password *</label>
                <input type="password" name="confirm_password" required placeholder="Repeat your password">
            </div>
            <button type="submit" class="btn btn-primary btn-block" style="margin-top:8px;">Create Account</button>
        </form>

        <p style="text-align:center;margin-top:20px;color:var(--text-muted);font-size:0.9rem;">
            Already have an account? <a href="User_Login.php" style="color:var(--primary);font-weight:600;">Login</a>
        </p>
    </div>
</main>

<?php include('../includes/footer.php'); ?>
