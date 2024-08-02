<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Include the database connection file
    require_once "../db_files/db_connection.php";

    // Collect POST data and sanitize it
    $personnelID = htmlspecialchars($_POST["personnelID"]);
    $ssn = htmlspecialchars($_POST["SSN"]);

    try {
        // Start a transaction
        $pdo->beginTransaction();

        // Proceed to delete the `Personnel` record
        $deletePersonnel = "DELETE FROM Personnel WHERE personID = :personnelID AND personID IN (SELECT personID FROM Person WHERE SSN = :ssn)";
        $stmtPersonnel = $pdo->prepare($deletePersonnel);
        $stmtPersonnel->execute([
            ':personnelID' => $personnelID,
            ':ssn' => $ssn
        ]);

        // Check if any rows were affected
        if ($stmtPersonnel->rowCount() > 0) {
            // Commit the transaction
            $pdo->commit();

            // Close the statement and database connection
            $stmtPersonnel = null;
            $pdo = null;

            // Redirect to the success page with a message
            header("Location: ../success.php?message=Personnel+deleted+successfully");
            exit();
        } else {
            // No rows affected, meaning no matching record was found
            throw new Exception("No matching personnel found.");
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
