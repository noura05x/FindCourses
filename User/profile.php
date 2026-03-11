<?php
session_start();
if (!isset($_SESSION['user_logged_in'])) {
    header("Location: User_Login.php"); exit;
}
include("../db.php");

$success = $error = '';
$user_id = $_SESSION['user_id'];


$stmt = $conn->prepare("SELECT * FROM users WHERE ID = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $new_email = trim($_POST['email']);
    $new_pass  = trim($_POST['new_password']);
    $confirm   = trim($_POST['confirm_password']);

    if (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email.";
    } elseif ($new_pass && $new_pass !== $confirm) {
        $error = "New passwords do not match.";
    } elseif ($new_pass && strlen($new_pass) < 6) {
        $error = "Password must be at least 6 characters.";
    } else {
        if ($new_pass) {
            $hashed = password_hash($new_pass, PASSWORD_DEFAULT);
            $upd = $conn->prepare("UPDATE users SET email=?, password=? WHERE ID=?");
            $upd->bind_param("ssi", $new_email, $hashed, $user_id);
        } else {
            $upd = $conn->prepare("UPDATE users SET email=? WHERE ID=?");
            $upd->bind_param("si", $new_email, $user_id);
        }
        if ($upd->execute()) {
            $_SESSION['user_email'] = $new_email;
            $user['email'] = $new_email;
            $success = "Profile updated successfully!";
        } else {
            $error = "Update failed. Please try again.";
        }
    }
}


$msgs = $conn->prepare("SELECT * FROM messages WHERE name = ? ORDER BY created_at DESC");
$msgs->bind_param("s", $user['name']);
$msgs->execute();
$messages = $msgs->get_result();

$base_path = '../';
$page_title = 'My Profile';
$active_nav = 'profile';
include('../includes/header.php');
?>

<main>
    <h1 class="page-title">My Profile</h1>
    <p class="page-subtitle">Manage your account information</p>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:28px;align-items:start;" class="profile-grid">

        <div class="profile-card">
            <div class="profile-avatar"><?= strtoupper(substr($user['name'], 0, 1)) ?></div>
            <h2 style="font-family:'Playfair Display',serif;margin-bottom:4px;"><?= htmlspecialchars($user['name']) ?></h2>
            <p style="color:var(--text-muted);margin-bottom:24px;"><?= htmlspecialchars($user['email']) ?></p>

            <?php if ($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>
            <?php if ($error): ?><div class="alert alert-error"><?= $error ?></div><?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" value="<?= htmlspecialchars($user['name']) ?>" disabled style="background:#eee;cursor:not-allowed;">
                </div>
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                </div>
                <div class="form-group">
                    <label>New Password <small style="color:var(--text-muted)">(leave blank to keep current)</small></label>
                    <input type="password" name="new_password" placeholder="New password...">
                </div>
                <div class="form-group">
                    <label>Confirm New Password</label>
                    <input type="password" name="confirm_password" placeholder="Confirm new password...">
                </div>
                <button type="submit" name="update_profile" class="btn btn-primary btn-block">Save Changes</button>
            </form>
        </div>

       
        <div>
            <h3 style="font-family:'Playfair Display',serif;color:var(--primary);margin-bottom:16px;">My Messages</h3>
            <?php if ($messages->num_rows === 0): ?>
                <div style="background:white;border-radius:14px;padding:28px;text-align:center;color:var(--text-muted);box-shadow:var(--card-shadow);">
                    <div style="font-size:2.5rem;margin-bottom:8px;">✉️</div>
                    <p>No messages sent yet.</p>
                    <a href="../contact.php" class="btn btn-outline btn-sm" style="margin-top:12px;">Send a Message</a>
                </div>
            <?php else: ?>
                <?php while ($msg = $messages->fetch_assoc()): ?>
                <div style="background:white;border-radius:12px;padding:18px;margin-bottom:14px;box-shadow:var(--card-shadow);">
                    <div style="display:flex;justify-content:space-between;margin-bottom:8px;">
                        <?php if ($msg['type']): ?>
                            <span class="badge badge-primary"><?= htmlspecialchars($msg['type']) ?></span>
                        <?php endif; ?>
                        <small style="color:var(--text-muted);"><?= date('M d, Y', strtotime($msg['created_at'])) ?></small>
                    </div>
                    <p style="color:var(--text);font-size:0.92rem;"><?= htmlspecialchars($msg['message']) ?></p>
                </div>
                <?php endwhile; ?>
            <?php endif; ?>
        </div>
    </div>
</main>

<style>
@media (max-width: 768px) {
    .profile-grid { grid-template-columns: 1fr !important; }
}
</style>

<?php include('../includes/footer.php'); ?>
