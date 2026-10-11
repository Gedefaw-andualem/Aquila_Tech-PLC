<?php
session_start();

// Login መደረጉን ማረጋገጥ
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: login.php");
    exit();
}

require_once 'db.php';

// 1. ያልተነበቡትን መልእክቶች ብዛት መቁጠር
$unread_count = 0;
$unread_sql = "SELECT COUNT(*) AS total_unread FROM contact_messages WHERE status = 'unread'";
$unread_result = $conn->query($unread_sql);
if ($unread_result) {
    $unread_row = $unread_result->fetch_assoc();
    $unread_count = $unread_row['total_unread'];
}

// 2. Search Query (ሁለቱም በ ORDER BY id DESC እንዲሆኑ ተስተካክሏል)
$search = "";
if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
    $search = $conn->real_escape_string($_GET['search']);
    $sql = "SELECT * FROM contact_messages WHERE full_name LIKE '%$search%' OR email LIKE '%$search%' ORDER BY id DESC";
} else {
    // አዳዲስ መልእክቶች ሁልጊዜ ከላይ እንዲሆኑ ORDER BY id DESC ተደርጓል
    $sql = "SELECT * FROM contact_messages ORDER BY id DESC";
}

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="am">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>የመጡ መልእክቶች - Admin Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .admin-header-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
            background-color: #0d1117;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .header-left {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .header-left h2 {
            color: #58a6ff;
            font-size: 20px;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .search-box-form {
            display: flex;
            gap: 8px;
        }

        .search-box-form input {
            padding: 7px 12px;
            background-color: #161b22;
            border: 1px solid #30363d;
            border-radius: 6px;
            color: #ffffff;
            font-size: 14px;
            outline: none;
        }

        .search-box-form input:focus {
            border-color: #1f6feb;
        }

        .search-box-form button {
            padding: 7px 14px;
            background-color: #238636;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
        }

        .reset-link {
            color: #8b949e;
            text-decoration: none;
            font-size: 13px;
            align-self: center;
        }

        .header-actions {
            display: flex;
            gap: 10px;
        }

        .btn-change-pass {
            background-color: #1f6feb;
            color: white;
            padding: 8px 16px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 14px;
        }

        .btn-logout {
            background-color: #da3633;
            color: white;
            padding: 8px 16px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 14px;
        }

        .btn-reply {
            background-color: #238636;
            color: white;
            padding: 6px 12px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 13px;
            display: inline-block;
            margin-right: 5px;
        }

        .btn-delete {
            background-color: #da3633;
            color: white;
            padding: 6px 12px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 13px;
            display: inline-block;
        }

        .notif-badge {
            background-color: #da3633;
            color: white;
            border-radius: 50%;
            padding: 2px 8px;
            font-size: 13px;
            font-weight: bold;
        }

        .badge-new {
            background-color: #da3633;
            color: white;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: bold;
            margin-left: 6px;
        }

        .badge-replied {
            background-color: #238636;
            color: white;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: bold;
            margin-left: 6px;
        }

        .row-unread {
            background-color: #1c2128 !important;
        }

        .sent-reply-box {
            margin-top: 8px;
            padding: 8px;
            background-color: #0d1117;
            border-left: 3px solid #238636;
            font-size: 12px;
            color: #8b949e;
            border-radius: 4px;
        }

        .sent-reply-box strong {
            color: #238636;
        }

        .action-cell {
            white-space: nowrap;
        }
    </style>
</head>
<body style="background-color: #0d1117; color: #c9d1d9; font-family: sans-serif; margin: 0; padding: 20px;">

    <!-- Top Admin Header -->
    <div class="admin-header-bar">
        <div class="header-left">
            <h2>
                የመጡ መልእክቶች
                <?php if ($unread_count > 0): ?>
                    <span class="notif-badge" title="አዲስ መልእክቶች"><?php echo $unread_count; ?></span>
                <?php endif; ?>
            </h2>

            <!-- የፍለጋ ሳጥን -->
            <form action="view_message.php" method="GET" class="search-box-form">
                <input type="text" name="search" placeholder="በስም ወይም በኢሜይል ፈልግ..." value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit">🔍 ፈልግ</button>
                <?php if (!empty($search)): ?>
                    <a href="view_message.php" class="reset-link">🔄 ሁሉንም አሳይ</a>
                <?php endif; ?>
            </form>
        </div>

        <div class="header-actions">
            <a href="change_password.php" class="btn-change-pass">🔑 Change Password</a>
           <a href="logout.php" class="btn-logout" onclick="return confirm('Are you sure you want to log out?');">🚪 Logout</a>
        </div>
    </div>

    <!-- Message Table Section -->
   <!-- Message Table Section -->
<div style="margin-top: 20px; overflow-x: auto;">
    <table border="1" cellspacing="0" cellpadding="12" style="width: 100%; border-collapse: collapse; border-color: #30363d; background-color: #161b22; text-align: left;">
        <thead>
            <tr style="background-color: #0d1117; color: #58a6ff;">
                <th># No</th> <!-- ID የነበረውን ወደ ተራ ቁጥር (# No) ቀይረነዋል -->
                <th>ስም</th>
                <th>ኢሜይል</th>
                <th>ርዕስ እና መልእክት</th>
                <th>የተላከ ምላሽ (Sent Reply)</th>
                <th>ቀንና ሰዓት</th>
                <th>ተግባር (Action)</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result && $result->num_rows > 0) {
                $count = 1; // ተራ ቁጥር ከ 1 እንዲጀምር መቁጠሪያ (Counter) አስተካክለናል
                while($row = $result->fetch_assoc()) {
                    $is_unread = (isset($row['status']) && $row['status'] == 'unread');
                    $is_replied = (isset($row['status']) && $row['status'] == 'replied');
                    
                    $row_class = $is_unread ? "class='row-unread'" : "";

                    echo "<tr $row_class>";
                    
                    // ከዳታቤዝ ID ይልቅ ተራ ቁጥሩን ($count) እያሳየ ይጨምራል
                    echo "<td>#" . $count++ . "</td>"; 
                    
                    echo "<td><b>" . htmlspecialchars($row["full_name"]) . "</b>";
                    if ($is_unread) {
                        echo " <span class='badge-new'>NEW</span>";
                    } elseif ($is_replied) {
                        echo " <span class='badge-replied'>✓ REPLIED</span>";
                    }
                    echo "</td>";

                    echo "<td>" . htmlspecialchars($row["email"]) . "</td>";
                    
                    // ርዕስ እና መልእክት
                    echo "<td>";
                    echo "<b>" . htmlspecialchars($row["subject"]) . "</b><br>";
                    echo "<span style='color: #8b949e; font-size: 13px;'>" . htmlspecialchars($row["message"]) . "</span>";
                    echo "</td>";

                    // የተላከው ምላሽ
                    echo "<td>";
                    if (!empty($row["reply_text"])) {
                        echo "<div class='sent-reply-box'>";
                        echo "<strong>💬 የተላከው ምላሽ፡</strong><br>";
                        echo htmlspecialchars($row["reply_text"]);
                        echo "</div>";
                    } else {
                        echo "<span style='color: #484f58; font-size: 12px;'>ምንም ምላሽ አልተላከም</span>";
                    }
                    echo "</td>";

                    echo "<td style='color: #238636; font-size: 13px;'>" . (isset($row["created_at"]) ? $row["created_at"] : '-') . "</td>";
                    
                    // Reply እና Delete በተኖች (ዳታቤዝ ውስጥ ለማጥፋት ትክክለኛው $row['id'] ለ Delete በተኑ ይሰጣል)
                    echo "<td class='action-cell'>";
                    echo "<a href='reply_form.php?id=" . $row["id"] . "' class='btn-reply'>Reply</a>";
                    echo "<a href='delete_message.php?id=" . $row["id"] . "' class='btn-delete' onclick='return confirm(\"Are you sure delate?\");'>Delete 🗑️</a>";
                    echo "</td>";

                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='7' style='text-align:center; padding: 20px;'>ምንም የተገኘ መልእክት የለም!</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

</body>
</html>