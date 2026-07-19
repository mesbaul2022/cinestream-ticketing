<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['seats'])) {
    header("Location: ../movies/index.php");
    exit;
}

$showtime_id = (int)$_POST['showtime_id'];
$seats = $_POST['seats'];
$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT price FROM showtimes WHERE id = ?");
$stmt->bind_param("i", $showtime_id);
$stmt->execute();
$showtime = $stmt->get_result()->fetch_assoc();

$total_price = $showtime['price'] * count($seats);

$conn->begin_transaction();

try {
    $stmt = $conn->prepare("INSERT INTO bookings (user_id, showtime_id, total_price) VALUES (?, ?, ?)");
    $stmt->bind_param("iid", $user_id, $showtime_id, $total_price);
    $stmt->execute();
    $booking_id = $conn->insert_id;

    $seat_stmt = $conn->prepare("INSERT INTO booking_seats (booking_id, showtime_id, seat_number) VALUES (?, ?, ?)");
    foreach ($seats as $seat) {
        $seat_stmt->bind_param("iis", $booking_id, $showtime_id, $seat);
        $seat_stmt->execute();
    }

    $conn->commit();
} catch (Exception $e) {
    $conn->rollback();
    include '../includes/header.php';
    echo "<p>Sorry, one or more of your selected seats was just taken. Please go back and choose again.</p>";
    include '../includes/footer.php';
    exit;
}

include '../includes/header.php';
?>

<h2>Booking Confirmed!</h2>
<div class="ticket">
    <p><strong>Booking ID:</strong> #<?php echo $booking_id; ?></p>
    <p><strong>Seats:</strong> <?php echo htmlspecialchars(implode(', ', $seats)); ?></p>
    <p><strong>Total Paid:</strong> Tk <?php echo number_format($total_price, 2); ?></p>
    <p>Show this confirmation at the counter.</p>
</div>

<?php include '../includes/footer.php'; ?>
