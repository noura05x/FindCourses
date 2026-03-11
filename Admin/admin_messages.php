<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: Admin_Login.php"); exit;
}
include("../db.php");

$base_path = '../';
$page_title = 'Messages';
include('../includes/header.php');

$messages = mysqli_query($conn, "SELECT * FROM messages ORDER BY created_at DESC");
?>

<main>
    <h1 class="page-title">Messages</h1>
    <p class="page-subtitle"><?= mysqli_num_rows($messages) ?> messages received</p>

    <div style="overflow-x:auto;">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Type</th>
                    <th>Message</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($msg = mysqli_fetch_assoc($messages)): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($msg['name']) ?></strong></td>
                    <td><?= htmlspecialchars($msg['email']) ?></td>
                    <td>
                        <?php if ($msg['type']): ?>
                            <span class="badge badge-primary"><?= htmlspecialchars($msg['type']) ?></span>
                        <?php else: ?>—<?php endif; ?>
                    </td>
                    <td style="max-width:320px;"><?= htmlspecialchars($msg['message']) ?></td>
                    <td style="white-space:nowrap;"><?= date('M d, Y', strtotime($msg['created_at'])) ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</main>

<?php include('../includes/footer.php'); ?>
