<?php
session_start();

if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: login.php");
    exit();
}

require_once 'db.php';

if (isset($_GET['id']) && isset($_GET['email'])) {
    $id = intval($_GET['id']);
    $email = $_GET['email'];

    // 1. በዳታቤዝ ውስጥ status ወደ 'replied' መቀየር
    $stmt = $conn->prepare("UPDATE contact_messages SET status = 'replied' WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    // 2. የኢሜይል ማፕሊኬሽኑን ከፍቶ ቀጥታ ወደ view_message.php መመለስ (በ JavaScript)
    echo "<script>
        window.location.href = 'mailto:" . urlencode($email) . "';
        setTimeout(function(){
            window.location.href = 'view_message.php';
        }, 500);
    </script>";
    exit();
} else {
    header("Location: view_message.php");
    exit();
}
?>