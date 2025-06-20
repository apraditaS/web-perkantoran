<?php
session_start();

// Hapus semua data session
$_SESSION = [];
session_unset();    // Hapus semua data session (variabel session)
session_destroy();  // Hancurkan sesi (logout dan hapus session ID)

// Redirect ke login.php
header("Location: login.php");
exit();
?>