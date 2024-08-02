<?php
// Get the message from the query parameter
$message = isset($_GET['message']) ? htmlspecialchars($_GET['message']) : 'Operation completed.';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Success</title>
    <style>
        /* Basic styling for the success page */
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5fa;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            text-align: center;
        }

        .container {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .message {
            font-size: 18px;
            margin-bottom: 20px;
        }

        .countdown {
            font-size: 16px;
            color: #00796b;
        }
    </style>
    <script>
        // JavaScript to handle the redirect after a delay
        function redirectAfterDelay() {
            setTimeout(function() {
                window.location.href = 'index.php';
            }, 3000); // 5 seconds delay
        }
        window.onload = redirectAfterDelay;
    </script>
</head>
<body>
    <div class="container">
        <div class="message"><?php echo $message; ?></div>
        <div class="countdown">Returning to the main page in 3 seconds...</div>
    </div>
</body>
</html>
