<?php
session_start();
session_unset();
session_destroy();

// ከወጣ በኋላ ቀጥታ ወደ index.html ይመልሰዋል
header("Location: index.html");
exit();
?>