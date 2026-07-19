<?php
include '../includes/admin_check.php';
include '../config/db.php';

// Handle adding a movie
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_movie'])) {
    $stmt = $conn->prepare("INSERT INTO movies (title, description, genre, duration_minutes, poster_url) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssis", $_POST['title'], $_POST['description'], $_POST['genre'], $_POST['duration'], $_POST['poster_url']);
    $stmt->execute();
    header("Location: manage_movies.php");
    exit;
}

// Handle adding a showtime
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_showtime'])) {
    $stmt = $conn->prepare("INSERT INTO showtimes (movie_id, hall_name, show_date, show_time, price, total_seats) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issssi", $_POST['movie_id'], $_POST['hall_name'], $_POST['show_date'], $_POST['show_time'], $_POST['price'], $_POST['total_seats']);
    $stmt->execute();
    header("Location: manage_movies.php");
    exit;
}

// Handle deleting a movie
if (isset($_GET['delete_movie'])) {
    $stmt = $conn->prepare("DELETE FROM movies WHERE id = ?");
    $stmt->bind_param("i", $_GET['delete_movie']);
    $stmt->execute();
    header("Location: manage_movies.php");
    exit;
}

include '../includes/header.php';

$movies = $conn->query("SELECT * FROM movies ORDER BY title");
?>

<h2>Manage Movies & Showtimes</h2>

<h3>Add New Movie</h3>
<form method="POST" class="admin-form">
    <input type="hidden" name="add_movie" value="1">
    <input type="text" name="title" placeholder="Title" required>
    <textarea name="description" placeholder="Description"></textarea>
    <input type="text" name="genre" placeholder="Genre">
    <input type="number" name="duration" placeholder="Duration (minutes)">
    <input type="text" name="poster_url" placeholder="Poster path (e.g. assets/images/placeholder.jpg)">
    <button type="submit" class="btn">Add Movie</button>
</form>

<h3>Existing Movies</h3>
<?php while ($movie = $movies->fetch_assoc()): ?>
    <div class="admin-movie-block">
        <h4><?php echo htmlspecialchars($movie['title']); ?>
            <a href="?delete_movie=<?php echo $movie['id']; ?>" onclick="return confirm('Delete this movie and all its showtimes?');" class="btn-delete">Delete</a>
        </h4>

        <?php
        $st = $conn->prepare("SELECT * FROM showtimes WHERE movie_id = ? ORDER BY show_date, show_time");
        $st->bind_param("i", $movie['id']);
        $st->execute();
        $shows = $st->get_result();
        while ($show = $shows->fetch_assoc()):
        ?>
            <p><?php echo htmlspecialchars($show['hall_name']); ?> — <?php echo $show['show_date']; ?> <?php echo $show['show_time']; ?> — Tk <?php echo $show['price']; ?> — <?php echo $show['total_seats']; ?> seats</p>
        <?php endwhile; ?>
        

        <form method="POST" class="admin-form-inline">
            <input type="hidden" name="add_showtime" value="1">
            <input type="hidden" name="movie_id" value="<?php echo $movie['id']; ?>">
            <input type="text" name="hall_name" placeholder="Hall" required>
            <input type="date" name="show_date" required>
            <input type="time" name="show_time" required>
            <input type="number" step="0.01" name="price" placeholder="Price" required>
            <input type="number" name="total_seats" placeholder="Seats" value="50" required>
            <button type="submit" class="btn">Add Showtime</button>
        </form>
    </div>
<?php endwhile; ?>

<?php include '../includes/footer.php'; ?>
