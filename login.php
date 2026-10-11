<?php
session_start();
require_once 'db.php';

$error = "";

if (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // ተጠቃሚውን ከዳታቤዝ መፈለግ
    $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        // ዳታቤዝ ላይ ከተቀየረው/ከተመዘገበው አዲስ ፓስወርድ ጋር በትክክል መዛመዱን ያረጋግጣል
        if (password_verify($password, $row['password'])) {
            $_SESSION['is_admin'] = true;
            $_SESSION['admin_id'] = $row['id'];
            $_SESSION['admin_username'] = $row['username'];
            header("Location: view_message.php");
            exit();
        } else {
            $error = "ተሳስተዋል! የይለፍ ቃል አልተዛመደም።";
        }
    } else {
        $error = "ተሳስተዋል! የተጠቃሚ ስም አልተገኘም።";
    }
}
?>

<!DOCTYPE html>
<html lang="am">
<head>
    <meta charset="UTF-8">
    <title>Admin Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="contact" style="min-height: 100vh; display: flex; align-items: center; justify-content: center;">
        <div style="background: #111827; padding: 40px; border-radius: 12px; border: 1px solid #1f293d; width: 100%; max-width: 400px;">
            <h2 style="margin-bottom: 20px;">Admin Login</h2>
            
            <?php if ($error): ?>
                <p style="color: #ef4444; margin-bottom: 15px; font-size: 14px;"><?php echo $error; ?></p>
            <?php endif; ?>

            <form action="login.php" method="POST" class="contact-form">
                <input type="text" name="username" placeholder="Username" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit" name="login" class="btn-primary">Sign In</button>
            </form>
        </div>
    </div>
</body>
</html>