<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scan QR Code</title>
    <link rel="stylesheet" href="style.css">
    <script src="html5-qrcode.min.js"></script>
    <style>
        body {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            background-color: #f0f0f0;
        }

        #reader {
            width: 80%;
            max-width: 600px;
            border: 2px solid #333;
            border-radius: 8px;
            background-color: #fff;
            padding: 10px;
        }

        #result {
            margin-top: 20px;
            font-size: 1.2em;
            text-align: center;
        }

        h2 {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <h2>Scan QR Code</h2>
    <div id="reader"></div>
    <div id="result"></div>

    <script>
        const html5QrCode = new Html5Qrcode("reader");
        
        // Callback for successful QR code scan
        const qrCodeSuccessCallback = (decodedText, decodedResult) => {
            document.getElementById('result').innerText = "Processing QR Code...";
            const data = decodedText.split('\n').reduce((acc, line) => {
                const [key, value] = line.split(': ').map(item => item.trim());
                acc[key] = value;
                return acc;
            }, {});

            savePassword(data);
        };

        // Function to save password to the database
        const savePassword = (data) => {
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "save_password.php", true);
            xhr.setRequestHeader("Content-Type", "application/json;charset=UTF-8");

            xhr.onreadystatechange = function () {
                if (xhr.readyState === XMLHttpRequest.DONE) {
                    if (xhr.status === 200) {
                        window.location.href = 'dashboard.php'; // Redirect to dashboard after success
                    } else {
                        window.location.href = 'dashboard.php?error=scan_failed'; // Redirect to dashboard with error
                    }
                }
            };
            xhr.send(JSON.stringify(data));
        };

        // Start scanning for QR codes
        html5QrCode.start(
            { facingMode: "environment" }, // Use rear camera
            {
                fps: 10, 
                qrbox: { width: 300, height: 300 } // Adjust size as needed
            },
            qrCodeSuccessCallback
        ).catch(err => {
            console.error("Error starting QR code scanner: ", err);
            window.location.href = 'dashboard.php?error=scan_failed'; // Redirect to dashboard on scanner start error
        });
    </script>
</body>
</html>
