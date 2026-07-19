<?php
include '../config/db.php';
include '../includes/header.php';

$movie_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $conn->prepare("SELECT * FROM movies WHERE id = ?");
$stmt->bind_param("i", $movie_id);
$stmt->execute();
$movie = $stmt->get_result()->fetch_assoc();

if (!$movie) {
    echo "<p>Movie not found.</p>";
    include '../includes/footer.php';
    exit;
}

$stmt2 = $conn->prepare("SELECT * FROM showtimes WHERE movie_id = ? ORDER BY show_date, show_time");
$stmt2->bind_param("i", $movie_id);
$stmt2->execute();
$showtimes = $stmt2->get_result();
?>

<h2><?php echo htmlspecialchars($movie['title']); ?></h2>
<p><?php echo htmlspecialchars($movie['description']); ?></p>
<p><strong>Genre:</strong> <?php echo htmlspecialchars($movie['genre']); ?> &nbsp; <strong>Duration:</strong> <?php echo (int)$movie['duration_minutes']; ?> min</p>

<h3>Showtimes</h3>
<div class="showtime-list">
<?php if ($showtimes->num_rows > 0): ?>
    <?php while ($show = $showtimes->fetch_assoc()): ?>
        <div class="showtime-card">
            <span><?php echo htmlspecialchars($show['hall_name']); ?></span>
            <span><?php echo date("M d, Y", strtotime($show['show_date'])); ?></span>
            <span><?php echo date("h:i A", strtotime($show['show_time'])); ?></span>
            <span>Tk <?php echo number_format($show['price'], 2); ?></span>
            <a href="/cinestream-ticketing/booking/seats.php?showtime_id=<?php echo (int)$show['id']; ?>" class="btn">Book</a>
        </div>
    <?php endwhile; ?>
<?php else: ?>
    <p>No showtimes scheduled yet.</p>
<?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>

