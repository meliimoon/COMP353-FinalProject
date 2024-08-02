<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Include the database connection file
    require_once "../db_files/db_connection.php";

    // Collect POST data and sanitize it
    $personID = htmlspecialchars($_POST["personID"]);

    try {
        // Prepare the SQL query to select from the Personnel table
        $query = "
        SELECT p.personID, p.SSN, p.firstName, p.lastName, p.medicareCardNumber, 
               p.dateOfBirth, p.telephoneNumber, p.emailAddress, 
               pe.role, pe.mandate
        FROM Person p
        JOIN Personnel pe ON p.personID = pe.personID
        WHERE p.personID = :personID";
        
        $stmt = $pdo->prepare($query);

        // Execute the prepared statement with the collected data
        $stmt->execute([':personID' => $personID]);

        // Fetch the result
        $personnel = $stmt->fetch(PDO::FETCH_ASSOC);

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
    <title>Display Personnel</title>
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
        <h3>Personnel Details</h3>
        <div class="container">
        <?php if (isset($personnel) && $personnel): ?>
            <p><strong>Personnel ID:</strong> <?php echo htmlspecialchars($personnel["personID"]); ?></p>
            <p><strong>SSN:</strong> <?php echo htmlspecialchars($personnel["SSN"]); ?></p>
            <p><strong>First Name:</strong> <?php echo htmlspecialchars($personnel["firstName"]); ?></p>
            <p><strong>Last Name:</strong> <?php echo htmlspecialchars($personnel["lastName"]); ?></p>
            <p><strong>Medicare Card Number:</strong> <?php echo htmlspecialchars($personnel["medicareCardNumber"]); ?></p>
            <p><strong>Date of Birth:</strong> <?php echo htmlspecialchars($personnel["dateOfBirth"]); ?></p>
            <p><strong>Telephone Number:</strong> <?php echo htmlspecialchars($personnel["telephoneNumber"]); ?></p>
            <p><strong>Email Address:</strong> <?php echo htmlspecialchars($personnel["emailAddress"]); ?></p>
            <p><strong>Role:</strong> <?php echo htmlspecialchars($personnel["role"]); ?></p>
            <p><strong>Mandate:</strong> <?php echo htmlspecialchars($personnel["mandate"]); ?></p>
        <?php else: ?>
            <p>No matching personnel found.</p>
        <?php endif; ?>
        </div>
        <a href="../index.php" class="button">Return to main page</a>
    </div>
</body>
</html>
