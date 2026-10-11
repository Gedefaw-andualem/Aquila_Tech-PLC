<?php
session_start();

// Login መደረጉን ማረጋገጥ
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: login.php");
    exit();
}

require_once 'db.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // መልእክቱን ከዳታቤዝ መስረዝ
    $stmt = $conn->prepare("DELETE FROM contact_messages WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

// ብራውዘሩ ገጹን Refresh አድርጎ አዲሱን ቅደም ተከተል እንዲያሳይ URL ላይ ?deleted=1 በመጨመር መመለስ
header("Location: view_message.php?deleted=1");
exit();
?>