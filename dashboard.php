<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT * FROM passwords WHERE user_id = ?");
$stmt->execute([$user_id]);
$passwords = $stmt->fetchAll();

// Function to decrypt the password
function decryptPassword($encrypted_password, $secret_key) {
    return openssl_decrypt($encrypted_password, 'aes-256-cbc', $secret_key, 0, substr(hash('sha256', $secret_key), 0, 16));
}

// Set your secret key here
$secret_key = "your_secret_key"; // Must match the key used during encryption
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f4f4f9;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        th {
            background-color: #01257d;
            color: white;
        }
        .delete-btn {
            background-color: #ff4b5c;
            color: white;
            padding: 8px 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .delete-btn:hover {
            background-color: #ff0000;
        }
        a.button {
            display: inline-block;
            background-color: #01257d;
            color: white;
            padding: 10px 15px;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        a.button:hover {
            background-color: #0a3c99;
        }
        .button-container {
            text-align: center;
            margin-bottom: 20px; /* Space between button and table */
        }
        .password {
            cursor: pointer;
            display: inline-block;
            background-color: #f4f4f4;
            padding: 5px;
            border-radius: 5px;
            user-select: none;
        }
        .password.hidden {
            font-family: 'Courier New', Courier, monospace;
            letter-spacing: 3px;
        }
        @media (max-width: 600px) {
            table, th, td {
                display: block;
                width: 100%;
            }
            th {
                position: absolute;
                top: -9999px;
                left: -9999px;
            }
            tr {
                margin-bottom: 15px;
                border: 1px solid #ddd;
            }
            td {
                text-align: right;
                padding-left: 50%;
                position: relative;
            }
            td:before {
                position: absolute;
                left: 10px;
                width: 45%;
                padding-right: 10px;
                white-space: nowrap;
                font-weight: bold;
                text-align: left;
            }
        }
    </style>
</head>
<body>
    <h2>Your Passwords</h2>

    <!-- Move the Scan QR button to the top -->
    <div class="button-container">
        <a href="scan.php" class="button">Scan QR Code</a>
    </div>

    <!-- Password table -->
    <table>
        <tr>
            <th>Username</th>
            <th>Password</th>
            <th>URL</th>
            <th>Timestamp</th>
            <th>Action</th>
        </tr>
        <?php foreach ($passwords as $password): ?>
            <tr>
                <td><?= htmlspecialchars($password['username']) ?></td>
                <td>
                    <span class="password hidden" onclick="togglePasswordVisibility(this)" data-password="<?= htmlspecialchars(decryptPassword($password['password'], $secret_key)) ?>">
                        ***** <!-- Default hidden password -->
                    </span>
                </td>
                <td><?= htmlspecialchars($password['url']) ?></td>
                <td><?= htmlspecialchars($password['timestamp']) ?></td>
                <td>
                    <a href="delete_password.php?id=<?= $password['id'] ?>" class="delete-btn">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

    <script>
        function togglePasswordVisibility(element) {
            const hidden = element.classList.contains('hidden');
            if (hidden) {
                // Show the decrypted password
                element.textContent = element.getAttribute('data-password');
                element.classList.remove('hidden');
            } else {
                // Hide the password
                element.textContent = '*****';
                element.classList.add('hidden');
            }
        }
    </script>
</body>
</html>
