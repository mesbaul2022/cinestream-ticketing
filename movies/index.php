<?php
include '../config/db.php';
include '../includes/header.php';

$result = $conn->query("SELECT * FROM movies ORDER BY title ASC");
?>

<h2>Now Showing</h2>

<div class="movie-grid">
<?php if ($result && $result->num_rows > 0): ?>
    <?php while ($movie = $result->fetch_assoc()): ?>
        <div class="movie-card">
            <img src="/cinestream-ticketing/<?php echo htmlspecialchars($movie['poster_url']); ?>" alt="<?php echo htmlspecialchars($movie['title']); ?>">
            <h3><?php echo htmlspecialchars($movie['title']); ?></h3>
            <p><?php echo htmlspecialchars($movie['genre']); ?> · <?php echo (int)$movie['duration_minutes']; ?> min</p>
            <a href="details.php?id=<?php echo (int)$movie['id']; ?>" class="btn">View Showtimes</a>
        </div>
    <?php endwhile; ?>
<?php else: ?>
    <p>No movies available right now.</p>
<?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
