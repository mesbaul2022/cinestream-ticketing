<?php
include '../includes/admin_check.php';
include '../config/db.php';
include '../includes/header.php';

$movie_count = $conn->query("SELECT COUNT(*) as c FROM movies")->fetch_assoc()['c'];
$showtime_count = $conn->query("SELECT COUNT(*) as c FROM showtimes")->fetch_assoc()['c'];
$booking_count = $conn->query("SELECT COUNT(*) as c FROM bookings")->fetch_assoc()['c'];
$user_count = $conn->query("SELECT COUNT(*) as c FROM users")->fetch_assoc()['c'];
$revenue = $conn->query("SELECT SUM(total_price) as total FROM bookings WHERE status = 'confirmed'")->fetch_assoc()['total'] ?? 0;
?>

<h2>Admin Dashboard</h2>

<div class="admin-stats">
    <div class="stat-card"><h3><?php echo $movie_count; ?></h3><p>Movies</p></div>
    <div class="stat-card"><h3><?php echo $showtime_count; ?></h3><p>Showtimes</p></div>
    <div class="stat-card"><h3><?php echo $booking_count; ?></h3><p>Bookings</p></div>
    <div class="stat-card"><h3><?php echo $user_count; ?></h3><p>Users</p></div>
    <div class="stat-card"><h3>Tk <?php echo number_format($revenue, 2); ?></h3><p>Revenue</p></div>
</div>

<div class="admin-links">
    <a href="manage_movies.php" class="btn">Manage Movies & Showtimes</a>
    <a href="manage_bookings.php" class="btn">Manage Bookings</a>
</div>

<?php include '../includes/footer.php'; ?>