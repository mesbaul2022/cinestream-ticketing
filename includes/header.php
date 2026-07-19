<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CineStream Ticketing</title>
    <link rel="stylesheet" href="/cinestream-ticketing/assets/css/style.css">
</head>
<body>

<header class="site-header">
    <div class="logo">CineStream</div>
    <nav>
        <a href="/cinestream-ticketing/index.php">Home</a>
        <a href="/cinestream-ticketing/movies/index.php">Movies</a>
        <?php if (isset($_SESSION['user_id'])): ?>
            <span class="welcome-text">Hi, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
            <a href="/cinestream-ticketing/booking/seats.php">My Bookings</a>
            <?php if (!empty($_SESSION['is_admin'])): ?>
    <a href="/cinestream-ticketing/admin/dashboard.php">Admin</a>
<?php endif; ?>

            <a href="/cinestream-ticketing/auth/logout.php">Logout</a>
        <?php else: ?>
            <a href="/cinestream-ticketing/auth/login.php">Login</a>
            <a href="/cinestream-ticketing/auth/register.php">Register</a>
        <?php endif; ?>
    </nav>
</header>

<main class="site-content">
