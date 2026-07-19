<?php
include '../includes/admin_check.php';
include '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $stmt = $conn->prepare("UPDATE bookings SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $_POST['status'], $_POST['booking_id']);
    $stmt->execute();
    header("Location: manage_bookings.php");
    exit;
}

include '../includes/header.php';

$bookings = $conn->query("
    SELECT b.id, b.total_price, b.status, b.created_at,
           u.name AS user_name, u.email,
           m.title AS movie_title, s.show_date, s.show_time
    FROM bookings b
    JOIN users u ON b.user_id = u.id
    JOIN showtimes s ON b.showtime_id = s.id
    JOIN movies m ON s.movie_id = m.id
    ORDER BY b.created_at DESC
");
?>

<h2>Manage Bookings</h2>

<table class="admin-table">
    <tr>
        <th>ID</th><th>Customer</th><th>Movie</th><th>Showtime</th><th>Total</th><th>Status</th><th>Action</th>
    </tr>
    <?php while ($b = $bookings->fetch_assoc()): ?>
    <tr>
        <td>#<?php echo $b['id']; ?></td>
        <td><?php echo htmlspecialchars($b['user_name']); ?><br><small><?php echo htmlspecialchars($b['email']); ?></small></td>
        <td><?php echo htmlspecialchars($b['movie_title']); ?></td>
        <td><?php echo $b['show_date']; ?> <?php echo $b['show_time']; ?></td>
        <td>Tk <?php echo number_format($b['total_price'], 2); ?></td>
        <td><?php echo htmlspecialchars($b['status']); ?></td>
        <td>
            <form method="POST" style="display:inline;">
                <input type="hidden" name="update_status" value="1">
                <input type="hidden" name="booking_id" value="<?php echo $b['id']; ?>">
                <input type="hidden" name="status" value="confirmed">
                <button type="submit" class="btn-small">Confirm</button>
            </form>
            <form method="POST" style="display:inline;">
                <input type="hidden" name="update_status" value="1">
                <input type="hidden" name="booking_id" value="<?php echo $b['id']; ?>">
                <input type="hidden" name="status" value="cancelled">
                <button type="submit" class="btn-small btn-delete">Cancel</button>
            </form>
        </td>
    </tr>
    <?php endwhile; ?>
</table>

<?php include '../includes/footer.php'; ?>