<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Include the database connection file
    require_once "../db_files/db_connection.php";

    // Collect POST data and sanitize it
    $teamName = htmlspecialchars($_POST["teamName1"]);
    $clubMembershipID = intval($_POST["clubMembershipID"]);
    $playerRole = htmlspecialchars($_POST["playerRole"]);
    $formationDateTime = htmlspecialchars($_POST["formationDateTime"]);

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

        // Check for conflicting assignments
        $query = "
            SELECT formationDateTime 
            FROM Apart_Of 
            WHERE personID = :personID
            AND teamName != :teamName
            AND DATE(formationDateTime) = DATE(:formationDateTime)
        ";
        $stmt = $pdo->prepare($query);
        $stmt->execute([
            ':personID' => $personID,
            ':teamName' => $teamName,
            ':formationDateTime' => $formationDateTime
        ]);
        $conflicts = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($conflicts as $conflict) {
            $existingDateTime = new DateTime($conflict['formationDateTime']);
            $newDateTime = new DateTime($formationDateTime);
            $interval = $existingDateTime->diff($newDateTime);

            if ($interval->h < 3 && $interval->invert == 0) {
                throw new Exception("Assignment conflict: Less than 3 hours apart.");
            }
        }

        // Update the team formation in the Apart_Of table
        $updateApartOf = "
            UPDATE Apart_Of 
            SET role = :role, formationDateTime = :formationDateTime 
            WHERE teamName = :teamName AND personID = :personID
        ";
        $stmtApartOf = $pdo->prepare($updateApartOf);
        $stmtApartOf->execute([
            ':role' => $playerRole,
            ':formationDateTime' => $formationDateTime,
            ':teamName' => $teamName,
            ':personID' => $personID
        ]);

        // Commit the transaction
        $pdo->commit();

        // Close the statement and database connection
        $stmtApartOf = null;
        $pdo = null;

        // Redirect to the success page with a message
        header("Location: ../success.php?message=Team+formation+updated+successfully");
        exit();
    } catch (Exception $e) {
        // Rollback the transaction if something failed
        $pdo->rollBack();
        die("Update Failed: " . $e->getMessage());
    }
} else {
    // Redirect to the index page if the request method is not POST
    header("Location: ../index.php");
    exit();
}
?>
