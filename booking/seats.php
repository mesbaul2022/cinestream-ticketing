<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

$showtime_id = isset($_GET['showtime_id']) ? (int)$_GET['showtime_id'] : 0;

$stmt = $conn->prepare("SELECT s.*, m.title FROM showtimes s JOIN movies m ON s.movie_id = m.id WHERE s.id = ?");
$stmt->bind_param("i", $showtime_id);
$stmt->execute();
$showtime = $stmt->get_result()->fetch_assoc();

if (!$showtime) {
    echo "Showtime not found.";
    exit;
}

$taken_stmt = $conn->prepare("SELECT seat_number FROM booking_seats WHERE showtime_id = ?");
$taken_stmt->bind_param("i", $showtime_id);
$taken_stmt->execute();
$taken_result = $taken_stmt->get_result();
$taken_seats = [];
while ($row = $taken_result->fetch_assoc()) {
    $taken_seats[] = $row['seat_number'];
}

include '../includes/header.php';
?>

<h2><?php echo htmlspecialchars($showtime['title']); ?></h2>
<p><?php echo htmlspecialchars($showtime['hall_name']); ?> · <?php echo date("M d, Y h:i A", strtotime($showtime['show_date'] . ' ' . $showtime['show_time'])); ?> · Tk <?php echo number_format($showtime['price'], 2); ?> per seat</p>

<form method="POST" action="confirm.php">
    <input type="hidden" name="showtime_id" value="<?php echo (int)$showtime_id; ?>">

    <div class="seat-map">
        <?php
        $rows = ['A', 'B', 'C', 'D', 'E'];
        foreach ($rows as $row) {
            for ($num = 1; $num <= 10; $num++) {
                $seat = $row . $num;
                $is_taken = in_array($seat, $taken_seats);
                if ($is_taken) {
                    echo "<span class='seat taken'>{$seat}</span>";
                } else {
                    echo "<label class='seat available'><input type='checkbox' name='seats[]' value='{$seat}'>{$seat}</label>";
                }
            }
            echo "<br>";
        }
        ?>
    </div>

    <p><span class="seat available"></span> Available &nbsp; <span class="seat taken"></span> Taken</p>

    <button type="submit" class="btn">Confirm Booking</button>
</form>

<?php include '../includes/footer.php'; ?>