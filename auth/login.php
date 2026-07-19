<?php
include '../includes/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $errors[] = "Both fields are required.";
    } else {
$stmt = $conn->prepare("SELECT id, name, password, is_admin FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['is_admin'] = $user['is_admin'];

                header("Location: ../index.php");
                exit;
            } else {
                $errors[] = "Incorrect email or password.";
            }
        } else {
            $errors[] = "Incorrect email or password.";
        }
    }
}

include '../includes/header.php';
?>

<h2>Login</h2>

<?php if (isset($_GET['registered'])): ?>
    <p class="success-box">Account created! Please log in.</p>
<?php endif; ?>

<?php if (!empty($errors)): ?>
    <div class="error-box">
        <?php foreach ($errors as $error): ?>
            <p><?php echo htmlspecialchars($error); ?></p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<form method="POST" action="login.php" class="auth-form">
    <label>Email</label>
    <input type="email" name="email" required>

    <label>Password</label>
    <input type="password" name="password" required>

    <button type="submit">Login</button>
</form>

<p>Don't have an account? <a href="register.php">Register here</a></p>

<?php include '../includes/footer.php'; ?>