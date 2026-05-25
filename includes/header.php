<?php
// START SESSION SAFELY (NO DUPLICATE ERROR)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Event Ticketing Platform</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<!-- NAVBAR -->
<div class="navbar">
    <a href="index.php">🏠 Home</a>
    <a href="events.php">🎫 Events</a>

    <?php if(isset($_SESSION['user'])): ?>
        <span>👤 <?php echo $_SESSION['user']['name']; ?></span>
        <a href="logout.php">🚪 Logout</a>
    <?php else: ?>
        <a href="login.php">🔐 Login</a>
        <a href="register.php">📝 Register</a>
    <?php endif; ?>
</div>

<!-- MAIN CONTAINER -->
<div class="container">