<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Include the database connection file
    require_once "../db_files/db_connection.php";

    // Collect POST data and sanitize it
    $teamName1 = htmlspecialchars($_POST["teamName1"]);
    $teamName2 = htmlspecialchars($_POST["teamName2"]);
    $headCoachID1 = intval($_POST["headCoachID1"]);
    $headCoachID2 = intval($_POST["headCoachID2"]);
    $gender = htmlspecialchars($_POST["gender"]);
    $locationID1 = intval($_POST["locationID1"]);
    $locationID2 = intval($_POST["locationID2"]);
    $sessionType = htmlspecialchars($_POST["sessionType"]);
    $locationID = intval($_POST["locationID"]);
    $team1Score = intval($_POST["team1Score"]);
    $team2Score = intval($_POST["team2Score"]);
    $sessionStartDateTime = htmlspecialchars($_POST["sessionStartDateTime"]);

    try {
        // Start a transaction
        $pdo->beginTransaction();

        // Insert the new teams into the Team table
        $insertTeam = "INSERT INTO Team (teamName, headCoachID, gender) VALUES (:teamName, :headCoachID, :gender)";
        $stmtTeam = $pdo->prepare($insertTeam);

        $stmtTeam->execute([
            ':teamName' => $teamName1,
            ':headCoachID' => $headCoachID1,
            ':gender' => $gender
        ]);

        $stmtTeam->execute([
            ':teamName' => $teamName2,
            ':headCoachID' => $headCoachID2,
            ':gender' => $gender
        ]);

        // Insert the new session into the Sessions table
        $insertSession = "INSERT INTO Sessions (sessionType, team1Score, team2Score, sessionStartDateTime, locationID) 
                          VALUES (:sessionType, :team1Score, :team2Score, :sessionStartDateTime, :locationID)";
        $stmtSession = $pdo->prepare($insertSession);
        $stmtSession->execute([
            ':sessionType' => $sessionType,
            ':team1Score' => $team1Score,
            ':team2Score' => $team2Score,
            ':sessionStartDateTime' => $sessionStartDateTime,
            ':locationID' => $locationID
        ]);

        // Get the last inserted sessionNum
        $sessionNum = $pdo->lastInsertId();

        // Insert the new play into the Plays table
        $insertPlay = "INSERT INTO Plays (sessionNum, teamName1, teamName2) VALUES (:sessionNum, :teamName1, :teamName2)";
        $stmtPlay = $pdo->prepare($insertPlay);
        $stmtPlay->execute([
            ':sessionNum' => $sessionNum,
            ':teamName1' => $teamName1,
            ':teamName2' => $teamName2
        ]);

        // Insert the team formation into the Formed_At table for both teams
        $insertFormedAt = "INSERT INTO Formed_At (teamName, locationID) VALUES (:teamName, :locationID)";
        $stmtFormedAt = $pdo->prepare($insertFormedAt);

        $stmtFormedAt->execute([
            ':teamName' => $teamName1,
            ':locationID' => $locationID1
        ]);

        $stmtFormedAt->execute([
            ':teamName' => $teamName2,
            ':locationID' => $locationID2
        ]);

        // Commit the transaction
        $pdo->commit();

        // Close the statement and database connection
        $stmtTeam = null;
        $stmtSession = null;
        $stmtPlay = null;
        $stmtFormedAt = null;
        $pdo = null;

        // Redirect to the success page with a message
        header("Location: ../success.php?message=Team+formation+created+successfully");
        exit();

    } catch (PDOException $e) {
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
