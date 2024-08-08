<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Include the database connection file
    require_once "../db_files/db_connection.php";

    // Collect POST data and sanitize it
    $currentTeamName1 = htmlspecialchars($_POST["currentTeamName1"]);
    $currentTeamName2 = htmlspecialchars($_POST["currentTeamName2"]);
    $newTeamName1 = htmlspecialchars($_POST["newTeamName1"]);
    $newTeamName2 = htmlspecialchars($_POST["newTeamName2"]);
    $sessionNum = intval($_POST["sessionnum"]);
    $sessionLocationID = isset($_POST["sessionLocationID"]) ? intval($_POST["sessionLocationID"]) : null;
    $sessionType = htmlspecialchars($_POST["sessionType"]);
    $newTeam1Score = isset($_POST["newTeam1Score"]) ? intval($_POST["newTeam1Score"]) : null;
    $newTeam2Score = isset($_POST["newTeam2Score"]) ? intval($_POST["newTeam2Score"]) : null;
    $sessionStartDateTime = htmlspecialchars($_POST["sessionStartDateTime"]);

    try {
        // Start a transaction
        $pdo->beginTransaction();

        // Fetch the current values from Team table
        $sqlFetchTeam1 = "SELECT teamName FROM Team WHERE teamName = :teamName";
        $stmtFetchTeam1 = $pdo->prepare($sqlFetchTeam1);
        $stmtFetchTeam1->execute([':teamName' => $currentTeamName1]);
        $currentTeam1 = $stmtFetchTeam1->fetch(PDO::FETCH_ASSOC);

        $sqlFetchTeam2 = "SELECT teamName FROM Team WHERE teamName = :teamName";
        $stmtFetchTeam2 = $pdo->prepare($sqlFetchTeam2);
        $stmtFetchTeam2->execute([':teamName' => $currentTeamName2]);
        $currentTeam2 = $stmtFetchTeam2->fetch(PDO::FETCH_ASSOC);

        if (!$currentTeam1) {
            throw new Exception("Team 1 not found.");
        }

        if ($currentTeamName2 && !$currentTeam2) {
            throw new Exception("Team 2 not found.");
        }

        // Retain original team names if input is blank
        $newTeamName1 = !empty($newTeamName1) ? $newTeamName1 : $currentTeam1['teamName'];
        $newTeamName2 = !empty($newTeamName2) ? $newTeamName2 : ($currentTeam2['teamName'] ?? null);

        // Update the Team table
        $sqlUpdateTeam1 = "UPDATE Team SET teamName = :newTeamName WHERE teamName = :currentTeamName";
        $stmtUpdateTeam1 = $pdo->prepare($sqlUpdateTeam1);
        $stmtUpdateTeam1->execute([
            ':newTeamName' => $newTeamName1,
            ':currentTeamName' => $currentTeamName1
        ]);

        if ($currentTeamName2) {
            $sqlUpdateTeam2 = "UPDATE Team SET teamName = :newTeamName WHERE teamName = :currentTeamName";
            $stmtUpdateTeam2 = $pdo->prepare($sqlUpdateTeam2);
            $stmtUpdateTeam2->execute([
                ':newTeamName' => $newTeamName2,
                ':currentTeamName' => $currentTeamName2
            ]);
        }

        // Fetch the current values from Sessions table
        $sqlFetchSession = "SELECT sessionNum, sessionType, team1Score, team2Score, sessionStartDateTime, locationID FROM Sessions WHERE sessionNum = :sessionNum";
        $stmtFetchSession = $pdo->prepare($sqlFetchSession);
        $stmtFetchSession->execute([':sessionNum' => $sessionNum]);
        $currentSession = $stmtFetchSession->fetch(PDO::FETCH_ASSOC);

        if (!$currentSession) {
            throw new Exception("Session not found.");
        }

        // Retain original session values if input is blank
        $sessionLocationID = !empty($sessionLocationID) ? $sessionLocationID : $currentSession['locationID'];
        $sessionType = !empty($sessionType) ? $sessionType : $currentSession['sessionType'];
        $newTeam1Score = !empty($newTeam1Score) ? $newTeam1Score : $currentSession['team1Score'];
        $newTeam2Score = !empty($newTeam2Score) ? $newTeam2Score : $currentSession['team2Score'];
        $sessionStartDateTime = !empty($sessionStartDateTime) ? $sessionStartDateTime : $currentSession['sessionStartDateTime'];

        // Update the Sessions table
        $sqlUpdateSession = "UPDATE Sessions 
                             SET sessionType = :sessionType, team1Score = :team1Score, 
                                 team2Score = :team2Score, sessionStartDateTime = :sessionStartDateTime, 
                                 locationID = :locationID 
                             WHERE sessionNum = :sessionNum";
        $stmtUpdateSession = $pdo->prepare($sqlUpdateSession);
        $stmtUpdateSession->execute([
            ':sessionType' => $sessionType,
            ':team1Score' => $newTeam1Score,
            ':team2Score' => $newTeam2Score,
            ':sessionStartDateTime' => $sessionStartDateTime,
            ':locationID' => $sessionLocationID,
            ':sessionNum' => $sessionNum
        ]);

        // Update the Plays table
        $sqlUpdatePlays = "UPDATE Plays 
                           SET teamName1 = :teamName1, teamName2 = :teamName2 
                           WHERE sessionNum = :sessionNum";
        $stmtUpdatePlays = $pdo->prepare($sqlUpdatePlays);
        $stmtUpdatePlays->execute([
            ':teamName1' => $newTeamName1,
            ':teamName2' => $newTeamName2,
            ':sessionNum' => $sessionNum
        ]);

        // Commit the transaction
        $pdo->commit();

        // Close the statement and database connection
        $stmtUpdateTeam1 = null;
        $stmtUpdateTeam2 = null;
        $stmtUpdateSession = null;
        $stmtUpdatePlays = null;
        $pdo = null;

        // Redirect to the success page with a message
        header("Location: ../success.php?message=Team+and+Session+updated+successfully");
        exit();

    } catch (PDOException $e) {
        // Rollback the transaction if something failed
        $pdo->rollBack();
        die("Update Failed: " . $e->getMessage());
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
