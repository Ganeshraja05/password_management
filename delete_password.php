<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Get the password ID from the request
if (isset($_GET['id'])) {
    $password_id = $_GET['id'];

    // Ensure the password belongs to the logged-in user
    $stmt = $pdo->prepare("DELETE FROM passwords WHERE id = ? AND user_id = ?");
    $result = $stmt->execute([$password_id, $_SESSION['user_id']]);

    if ($result) {
        header("Location: dashboard.php?delete=success");
    } else {
        header("Location: dashboard.php?delete=failed");
    }
} else {
    header("Location: dashboard.php");
}
exit;
?>
