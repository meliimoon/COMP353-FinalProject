<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Include the database connection file
    require_once "../db_files/db_connection.php";

    // Collect POST data and sanitize it
    $teamName = htmlspecialchars($_POST["teamName1"]);
    $clubMembershipID = intval($_POST["clubMembershipID"]);

    try {
        // Start a transaction
        $pdo->beginTransaction();

        // Get the personID for the given clubMembershipID
        $queryPersonID = "SELECT personID FROM ClubMember WHERE clubMembershipID = :clubMembershipID";
        $stmtPersonID = $pdo->prepare($queryPersonID);
        $stmtPersonID->execute([':clubMembershipID' => $clubMembershipID]);
        $personID = $stmtPersonID->fetchColumn();

        if (!$personID) {
            throw new Exception("Club membership ID not found.");
        }

        // Check if the team name and person ID exist in the Apart_Of table
        $checkAssignment = "
            SELECT COUNT(*) 
            FROM Apart_Of 
            WHERE teamName = :teamName AND personID = :personID
        ";
        $stmtCheck = $pdo->prepare($checkAssignment);
        $stmtCheck->execute([
            ':teamName' => $teamName,
            ':personID' => $personID
        ]);
        $assignmentExists = $stmtCheck->fetchColumn();

        if ($assignmentExists == 0) {
            throw new Exception("No matching assignment found for the provided team name and club membership ID.");
        }

        // Delete the assignment from the Apart_Of table
        $deleteApartOf = "
            DELETE FROM Apart_Of 
            WHERE teamName = :teamName AND personID = :personID
        ";
        $stmtApartOf = $pdo->prepare($deleteApartOf);
        $stmtApartOf->execute([
            ':teamName' => $teamName,
            ':personID' => $personID
        ]);

        // Commit the transaction
        $pdo->commit();

        // Close the statement and database connection
        $stmtApartOf = null;
        $pdo = null;

        // Redirect to the success page with a message
        header("Location: ../success.php?message=Team+formation+deleted+successfully");
        exit();
    } catch (Exception $e) {
        // Rollback the transaction if something failed
        $pdo->rollBack();
        die("Deletion Failed: " . $e->getMessage());
    }
} else {
    // Redirect to the index page if the request method is not POST
    header("Location: ../index.php");
    exit();
}
?>
