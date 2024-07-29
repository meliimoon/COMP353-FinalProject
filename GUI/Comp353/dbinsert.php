
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Include the database connection file
    require_once "dbconnection.php";
    
    // Collect POST data and sanitize it
    $firstname = htmlspecialchars($_POST["firstname"]);
    $lastname = htmlspecialchars($_POST["lastname"]);
    $SSN = htmlspecialchars($_POST["SSN"]);

    try {
        // Prepare the SQL query using placeholders
        $query = "INSERT INTO person (firstname, lastname, SSN) VALUES (:firstname, :lastname, :SSN)";
        $stmt = $pdo->prepare($query);

        // Execute the prepared statement with the collected data
        $stmt->execute([
            ':firstname' => $firstname,
            ':lastname' => $lastname,
            ':SSN' => $SSN
        ]);

        // Close the statement and database connection
        $stmt = null;
        $pdo = null;

        // Redirect to the index page
        header("Location: ../index.php");
        exit();

    } catch (PDOException $e) {
        die("Insertion Failed: " . $e->getMessage());
    }
} else {
    // Redirect to the index page if the request method is not POST
    header("Location: ../index.php");
    exit();
}
?>
