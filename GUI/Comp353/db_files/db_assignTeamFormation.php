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

        // Check for conflicting assignments
        $query = "
            SELECT formationDateTime 
            FROM Apart_Of 
            WHERE personID = (
                SELECT personID 
                FROM ClubMember 
                WHERE clubMembershipID = :clubMembershipID
            ) 
            AND DATE(formationDateTime) = DATE(:formationDateTime)
        ";
        $stmt = $pdo->prepare($query);
        $stmt->execute([
            ':clubMembershipID' => $clubMembershipID,
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

        // Insert the team formation into the Apart_Of table
        $insertApartOf = "
            INSERT INTO Apart_Of (teamName, personID, role, formationDateTime)
            VALUES (:teamName, (
                SELECT personID 
                FROM ClubMember 
                WHERE clubMembershipID = :clubMembershipID
            ), :role, :formationDateTime)
        ";
        $stmtApartOf = $pdo->prepare($insertApartOf);
        $stmtApartOf->execute([
            ':teamName' => $teamName,
            ':clubMembershipID' => $clubMembershipID,
            ':role' => $playerRole,
            ':formationDateTime' => $formationDateTime
        ]);

        // Commit the transaction
        $pdo->commit();

        // Close the statement and database connection
        $stmtApartOf = null;
        $pdo = null;

        // Redirect to the success page with a message
        header("Location: ../success.php?message=Team+formation+created+successfully");
        exit();
    } catch (Exception $e) {
        // Rollback the transaction if something failed
        $pdo->rollBack();
        die("Insertion Failed: " . $e->getMessage());
    }
} else {
    // Redirect to the index page if the request method is not POST
    header("Location: ../index.php");
    exit();
}
?>
