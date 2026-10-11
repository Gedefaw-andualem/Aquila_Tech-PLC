<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: login.php");
    exit();
}

$message = "";
$error = "";

if (isset($_POST['change_pass'])) {
    $current_pass = $_POST['current_password'];
    $new_pass = $_POST['new_password'];
    $confirm_pass = $_POST['confirm_password'];

    if ($new_pass !== $confirm_pass) {
        $error = "አዲሱ የይለፍ ቃል እና ማረጋገጫው አይመሳሰሉም!";
    } else {
        $admin_id = $_SESSION['admin_id'];
        $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->bind_param("i", $admin_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        // Current password '123' ከሆነ ወይም ከዳታቤዝ Hash ጋር ከተጣጣመ ይቀይረዋል
        if ($current_pass === '123' || password_verify($current_pass, $user['password'])) {
            $hashed_new_pass = password_hash($new_pass, PASSWORD_DEFAULT);
            $update_stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $update_stmt->bind_param("si", $hashed_new_pass, $admin_id);
            
            if ($update_stmt->execute()) {
                $message = "የይለፍ ቃልዎ በትክክል ተቀይሯል!";
            } else {
                $error = "ችግር አጋጥሟል! እባክዎ እንደገና ይሞክሩ።";
            }
        } else {
            $error = "የነበረው (Current) የይለፍ ቃል ትክክል አይደለም!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="am">
<head>
    <meta charset="UTF-8">
    <title>Change Password</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="contact" style="min-height: 100vh; display: flex; align-items: center; justify-content: center;">
        <div style="background: #111827; padding: 40px; border-radius: 12px; border: 1px solid #1f293d; width: 100%; max-width: 450px;">
            <h2 style="margin-bottom: 20px;">Change Password</h2>
            
            <?php if ($message): ?>
                <p style="color: #10b981; margin-bottom: 15px; font-size: 14px;"><?php echo $message; ?></p>
            <?php endif; ?>

            <?php if ($error): ?>
                <p style="color: #ef4444; margin-bottom: 15px; font-size: 14px;"><?php echo $error; ?></p>
            <?php endif; ?>

            <form action="change_password.php" method="POST" class="contact-form">
                <input type="password" name="current_password" placeholder="Current Password" required>
                <input type="password" name="new_password" placeholder="New Password" required>
                <input type="password" name="confirm_password" placeholder="Confirm New Password" required>
                <button type="submit" name="change_pass" class="btn-primary">Update Password</button>
            </form>
            <br>
            <p><a href="view_message.php" style="color: #60a5fa; text-decoration: none;">← Back to Messages</a></p>
        </div>
    </div>
</body>
</html>