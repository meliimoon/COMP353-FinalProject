<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Include the database connection file
    require_once "../db_files/db_connection.php";

    // Collect POST data and sanitize it
    $teamName = htmlspecialchars($_POST["teamName"]);
    $locationID = intval($_POST["locationID"]);

    try {
        // Start a transaction
        $pdo->beginTransaction();

        // Fetch the current values to ensure the team and location exist in Formed_At
        $sqlCheckFormedAt = "SELECT * FROM Formed_At WHERE teamName = :teamName AND locationID = :locationID";
        $stmtCheckFormedAt = $pdo->prepare($sqlCheckFormedAt);
        $stmtCheckFormedAt->execute([':teamName' => $teamName, ':locationID' => $locationID]);
        $currentFormedAtValues = $stmtCheckFormedAt->fetch(PDO::FETCH_ASSOC);

        if (!$currentFormedAtValues) {
            throw new Exception("Team and Location ID do not match.");
        }

        // Delete related entries in the Formed_At table
        $sqlDeleteFormedAt = "DELETE FROM Formed_At WHERE teamName = :teamName AND locationID = :locationID";
        $stmtDeleteFormedAt = $pdo->prepare($sqlDeleteFormedAt);
        $stmtDeleteFormedAt->execute([
            ':teamName' => $teamName,
            ':locationID' => $locationID
        ]);

        // Delete related entries in the Plays table
        $sqlDeletePlays = "DELETE FROM Plays WHERE teamName1 = :teamName OR teamName2 = :teamName";
        $stmtDeletePlays = $pdo->prepare($sqlDeletePlays);
        $stmtDeletePlays->execute([':teamName' => $teamName]);

        // Delete related entries in the Apart_Of table
        $sqlDeleteApartOf = "DELETE FROM Apart_Of WHERE teamName = :teamName";
        $stmtDeleteApartOf = $pdo->prepare($sqlDeleteApartOf);
        $stmtDeleteApartOf->execute([':teamName' => $teamName]);

        // Finally, delete the team from the Team table
        $sqlDeleteTeam = "DELETE FROM Team WHERE teamName = :teamName";
        $stmtDeleteTeam = $pdo->prepare($sqlDeleteTeam);
        $stmtDeleteTeam->execute([':teamName' => $teamName]);

        // Commit the transaction
        $pdo->commit();

        // Close the statement and database connection
        $stmtDeleteTeam = null;
        $stmtDeleteFormedAt = null;
        $stmtDeletePlays = null;
        $stmtDeleteApartOf = null;
        $pdo = null;

        // Redirect to the success page with a message
        header("Location: ../success.php?message=Team+deleted+successfully");
        exit();

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
