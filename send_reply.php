<?php
session_start();

if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: login.php");
    exit();
}

require_once 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = intval($_POST['id']);
    $to_email = $_POST['email'];
    $subject = $_POST['subject'];
    $reply_message = $_POST['reply_message'];

    // 1. የላኪው የኢሜይል አድራሻ እና ስም
    $from_email = "aquilatechplc@gmail.com";
    $from_name = "Aquila Tech PLC";

    $headers = "From: " . $from_name . " <" . $from_email . ">\r\n";
    $headers .= "Reply-To: " . $from_email . "\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

    // 2. ኢሜይሉን መላክ እና ሁኔታውን ማረጋገጥ (@ ሳይኖር የላከውን ውጤት መፈተሽ)
    $mail_sent = mail($to_email, $subject, $reply_message, $headers);

    if ($mail_sent) {
        // ኢሜይሉ ከተላከ በዳታቤዝ ውስጥ status ወደ 'replied' መቀየር
        $stmt = $conn->prepare("UPDATE contact_messages SET status = 'replied', reply_text = ? WHERE id = ?");
        $stmt->bind_param("si", $reply_message, $id);
        $stmt->execute();
        $stmt->close();

        // ስኬታማ ከሆነ Pop-up አሳይቶ ወደ view_message.php መመለስ
        echo "<script>
            alert('መልእክቱ በትክክል ተልኳል!');
            window.location.href = 'view_message.php';
        </script>";
        exit();
    } else {
        // ኢሜይሉ ካልተላከ Pop-up አሳይቶ ወደ view_message.php መመለስ
        echo "<script>
            alert('መልእክትዎ አልተላከም!');
            window.location.href = 'view_message.php';
        </script>";
        exit();
    }
} else {
    header("Location: view_message.php");
    exit();
}
?>