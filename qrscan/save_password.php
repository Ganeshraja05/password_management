<?php
session_start();
include 'db.php';

// Get the JSON data sent from the client
$data = json_decode(file_get_contents("php://input"), true);

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "User not logged in"]);
    exit;
}

$user_id = $_SESSION['user_id'];
$username = $data['Username'];
$password = $data['Password'];
$url = $data['URL'];

// Encrypt the password
$secret_key = "your_secret_key"; // Change this to a secure key
$encrypted_password = openssl_encrypt($password, 'aes-256-cbc', $secret_key, 0, substr(hash('sha256', $secret_key), 0, 16));

// Prepare SQL statement to insert into database
$stmt = $pdo->prepare("INSERT INTO passwords (user_id, username, password, url) VALUES (?, ?, ?, ?)");
$result = $stmt->execute([$user_id, $username, $encrypted_password, $url]);

if ($result) {
    echo json_encode(["status" => "success"]);
} else {
    echo json_encode(["status" => "error", "message" => "Failed to save password"]);
}
?>
