<?php
session_start();

if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: login.php");
    exit();
}

require_once 'db.php';

if (!isset($_GET['id'])) {
    header("Location: view_message.php");
    exit();
}

$id = intval($_GET['id']);
$stmt = $conn->prepare("SELECT * FROM contact_messages WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "መልእክቱ አልተገኘም!";
    exit();
}

$message_data = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="am">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ምላሽ መስጫ - Admin</title>
    <style>
        body {
            background-color: #0d1117;
            color: #c9d1d9;
            font-family: Arial, sans-serif;
            padding: 30px;
            display: flex;
            justify-content: center;
        }
        .reply-card {
            background-color: #161b22;
            border: 1px solid #30363d;
            border-radius: 8px;
            padding: 25px;
            width: 100%;
            max-width: 600px;
        }
        h2 {
            color: #58a6ff;
            margin-top: 0;
        }
        .original-msg {
            background-color: #0d1117;
            border-left: 3px solid #1f6feb;
            padding: 12px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 6px;
            color: #8b949e;
            font-size: 14px;
        }
        input[type="text"], textarea {
            width: 100%;
            padding: 10px;
            background-color: #0d1117;
            border: 1px solid #30363d;
            border-radius: 6px;
            color: #ffffff;
            box-sizing: border-box;
            font-size: 14px;
        }
        input[type="text"]:focus, textarea:focus {
            border-color: #1f6feb;
            outline: none;
        }
        .btn-send {
            background-color: #238636;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 15px;
            font-weight: bold;
        }
        .btn-send:hover {
            background-color: #2ea043;
        }
        .btn-back {
            color: #8b949e;
            text-decoration: none;
            margin-left: 15px;
            font-size: 14px;
        }
    </style>
</head>
<body>

<div class="reply-card">
    <h2>✉️ ምላሽ መስጫ (Reply)</h2>
    
    <div class="original-msg">
        <strong>ከ፡</strong> <?php echo htmlspecialchars($message_data['full_name']); ?> (<?php echo htmlspecialchars($message_data['email']); ?>)<br>
        <strong>የመጣው መልእክት፡</strong> <?php echo htmlspecialchars($message_data['message']); ?>
    </div>

    <form action="send_reply.php" method="POST">
        <input type="hidden" name="id" value="<?php echo $message_data['id']; ?>">
        
        <div class="form-group">
            <label>ለ (To Email):</label>
            <input type="text" name="email" value="<?php echo htmlspecialchars($message_data['email']); ?>" readonly>
        </div>

        <div class="form-group">
            <label>ርዕስ (Subject):</label>
            <input type="text" name="subject" value="Re: <?php echo htmlspecialchars($message_data['subject']); ?>" required>
        </div>

        <div class="form-group">
            <label>የምላሽ መልእክትህ (Your Reply):</label>
            <textarea name="reply_message" rows="6" placeholder="እዚህ ጋር ምላሽህን ጻፍ..." required></textarea>
        </div>

        <button type="submit" class="btn-send">📤 መልእክቱን ላክ (Send Reply)</button>
        <a href="view_message.php" class="btn-back">ተመለስ</a>
    </form>
</div>

</body>
</html>