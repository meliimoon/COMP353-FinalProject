<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Include the database connection file
    require_once "../db_files/db_connection.php";

    // Collect POST data and sanitize it
    $locationID = htmlspecialchars($_POST["locationID"]);
    $postalCode = htmlspecialchars($_POST["postalCode"]);

    try {
        // Start a transaction
        $pdo->beginTransaction();

        // Proceed to delete the `Location` record
        $deleteLocation = "DELETE FROM Location WHERE locationID = :locationID AND postalCode = :postalCode";
        $stmtLocation = $pdo->prepare($deleteLocation);
        $stmtLocation->execute([
            ':locationID' => $locationID,
            ':postalCode' => $postalCode
        ]);

        // Check if any rows were affected
        if ($stmtLocation->rowCount() > 0) {
            // Commit the transaction
            $pdo->commit();

            // Close the statement and database connection
            $stmtLocation = null;
            $pdo = null;

            // Redirect to the success page with a message
            header("Location: ../success.php?message=Location+deleted+successfully");
            exit();
        } else {
            // No rows affected, meaning no matching record was found
            throw new Exception("No matching location found.");
        }

    } catch (PDOException $e) {
        // Rollback the transaction if something failed
        $pdo->rollBack();
        die("Deletion Failed: " . $e->getMessage());
    } catch (Exception $e) {
        // Handle other exceptions
        die("Error: " . $e->getMessage());
    }
} else {
    // Redirect to the index page if the request method is not POST
    header("Location: ../index.php");
    exit();
}
?>
