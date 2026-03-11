<?php
session_start();
include('db.php');

$success = $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = mysqli_real_escape_string($conn, trim($_POST['name']));
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $type  = mysqli_real_escape_string($conn, trim($_POST['type'] ?? ''));
    $msg   = mysqli_real_escape_string($conn, trim($_POST['message']));

    if ($name && $email && $msg) {
        $q = "INSERT INTO messages (name, email, type, message) VALUES ('$name','$email','$type','$msg')";
        if (mysqli_query($conn, $q)) {
            $success = "Your message was sent successfully! We'll get back to you soon.";
        } else {
            $error = "Something went wrong. Please try again.";
        }
    } else {
        $error = "Please fill in all required fields.";
    }
}

$base_path = '';
$page_title = 'Contact Us';
$active_nav = 'contact';
include('includes/header.php');
?>

<main>
    <div style="max-width:660px;margin:0 auto;">
        <h1 class="page-title">Contact Us</h1>
        <p class="page-subtitle">Have a question or want to suggest a course? We'd love to hear from you.</p>

        <div class="form-container" style="max-width:100%;">
            <?php if ($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>
            <?php if ($error): ?><div class="alert alert-error"><?= $error ?></div><?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label>Your Name *</label>
                    <input type="text" name="name" required value="<?= isset($_SESSION['user_username']) ? htmlspecialchars($_SESSION['user_username']) : '' ?>">
                </div>
                <div class="form-group">
                    <label>Email Address *</label>
                    <input type="email" name="email" required>
                </div>
                <div class="form-group">
                    <label>Message Type</label>
                    <select name="type">
                        <option value="">General</option>
                        <option value="Question">Question</option>
                        <option value="Suggestion">Suggest a Course</option>
                        <option value="Complaint">Complaint</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Your Message *</label>
                    <textarea name="message" rows="5" required placeholder="Write your message here..."></textarea>
                </div>
                <button type="submit" class="btn btn-accent btn-block" style="margin-top:8px;">Send Message </button>
            </form>
        </div>
    </div>
</main>

<?php include('includes/footer.php'); ?>
