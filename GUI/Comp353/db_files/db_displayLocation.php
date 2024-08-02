<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Include the database connection file
    require_once "../db_files/db_connection.php";

    // Collect POST data and sanitize it
    $locationID = htmlspecialchars($_POST["locationID"]);

    try {
        // Prepare the SQL query to select from the Location table
        $query = "SELECT * FROM Location WHERE locationID = :locationID";
        $stmt = $pdo->prepare($query);

        // Execute the prepared statement with the collected data
        $stmt->execute([':locationID' => $locationID]);

        // Fetch the result
        $location = $stmt->fetch(PDO::FETCH_ASSOC);

        // Close the statement and database connection
        $stmt = null;
        $pdo = null;
    } catch (PDOException $e) {
        // Handle database-related exceptions
        die("Query Failed: " . $e->getMessage());
    }
} else {
    // Redirect to the index page if the request method is not POST
    header("Location: ../index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Display Location</title>
    <style>
        /* Apply basic styling to the body */
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5fa;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        /* Center the content */
        .content {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        /* Container styling */
        .container {
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 600px;
        }

        /* Paragraph styling */
        p {
            margin-bottom: 10px;
        }

        /* Link styling */
        a {
            color: #007bff;
            text-decoration: none;
        }

        /* Button styling */
        .button {
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="content">
        <h3>Location Details</h3>
        <div class="container">
        <?php if (isset($location) && $location): ?>
            <p><strong>Name:</strong> <?php echo htmlspecialchars($location["locationName"]); ?></p>
            <p><strong>Address:</strong> <?php echo htmlspecialchars($location["address"]); ?></p>
            <p><strong>City:</strong> <?php echo htmlspecialchars($location["city"]); ?></p>
            <p><strong>Province:</strong> <?php echo htmlspecialchars($location["province"]); ?></p>
            <p><strong>Postal Code:</strong> <?php echo htmlspecialchars($location["postalCode"]); ?></p>
            <p><strong>Phone Number:</strong> <?php echo htmlspecialchars($location["phoneNumber"]); ?></p>
            <p><strong>Web Address:</strong> <a href="<?php echo htmlspecialchars($location["webAddress"]); ?>" target="_blank"><?php echo htmlspecialchars($location["webAddress"]); ?></a></p>
            <p><strong>Type:</strong> <?php echo htmlspecialchars($location["type"]); ?></p>
            <p><strong>Capacity:</strong> <?php echo htmlspecialchars($location["capacity"]); ?></p>
        <?php else: ?>
            <p>No matching location found.</p>
        <?php endif; ?>
        </div>
        <a href="../index.php" class="button">Return to main page</a>
    </div>
</body>
</html>
