<?php
require_once 'db.php';

date_default_timezone_set('Africa/Addis_Ababa');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = htmlspecialchars($_POST['full_name']);
    $email     = htmlspecialchars($_POST['email']);
    $subject   = htmlspecialchars($_POST['subject']);
    $message   = htmlspecialchars($_POST['message']);
    $current_date = date("Y-m-d H:i:s");

    if (!empty($full_name) && !empty($email) && !empty($message)) {
        
        // መልእክቱን ዳታቤዝ ውስጥ ማስገባት
        $stmt = $conn->prepare("INSERT INTO contact_messages (full_name, email, subject, message, created_at) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $full_name, $email, $subject, $message, $current_date);

        if ($stmt->execute()) {
            echo "<script>alert('መልእክትዎ በትክክል ተልኳል! እናመሰግናለን።'); window.location.href='index.html';</script>";
        } else {
            echo "<script>alert('ስህተት ተፈጥሯል፣ እባክዎ ድጋሚ ይሞክሩ።'); window.location.href='index.html';</script>";
        }
        $stmt->close();
    }
}
$conn->close();
?>