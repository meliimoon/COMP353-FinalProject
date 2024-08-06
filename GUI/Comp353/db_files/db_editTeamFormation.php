<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Include the database connection file
    require_once "../db_files/db_connection.php";

    // Collect POST data and sanitize it
    $currentTeamName1 = htmlspecialchars($_POST["currentTeamName1"]);
    $currentTeamName2 = htmlspecialchars($_POST["currentTeamName2"]);

    try {
        // Start a transaction
        $pdo->beginTransaction();

        // Retrieve current values from the database
        $query = "
            SELECT 
                t1.teamName AS currentTeamName1, t2.teamName AS currentTeamName2,
                t1.headCoachID AS currentHeadCoachID1, t2.headCoachID AS currentHeadCoachID2,
                t1.gender, 
                f1.locationID AS currentLocationID1, f2.locationID AS currentLocationID2,
                s.sessionType, s.team1Score, s.team2Score, s.sessionStartDateTime, s.locationID AS sessionLocationID
            FROM 
                Plays p
            JOIN 
                Team t1 ON p.teamName1 = t1.teamName
            JOIN 
                Team t2 ON p.teamName2 = t2.teamName
            JOIN 
                Formed_At f1 ON t1.teamName = f1.teamName
            JOIN 
                Formed_At f2 ON t2.teamName = f2.teamName
            JOIN 
                Sessions s ON p.sessionNum = s.sessionNum
            WHERE 
                t1.teamName = :currentTeamName1 AND t2.teamName = :currentTeamName2";
        
        $stmt = $pdo->prepare($query);
        $stmt->execute([':currentTeamName1' => $currentTeamName1, ':currentTeamName2' => $currentTeamName2]);
        $currentValues = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$currentValues) {
            throw new PDOException("Current team names not found in the database.");
        }

        // Collect new values, or keep current if not provided
        $newTeamName1 = !empty($_POST["newTeamName1"]) ? htmlspecialchars($_POST["newTeamName1"]) : $currentValues['currentTeamName1'];
        $newTeamName2 = !empty($_POST["newTeamName2"]) ? htmlspecialchars($_POST["newTeamName2"]) : $currentValues['currentTeamName2'];
        $newHeadCoachID1 = !empty($_POST["newHeadCoachID1"]) ? intval($_POST["newHeadCoachID1"]) : $currentValues['currentHeadCoachID1'];
        $newHeadCoachID2 = !empty($_POST["newHeadCoachID2"]) ? intval($_POST["newHeadCoachID2"]) : $currentValues['currentHeadCoachID2'];
        $gender = !empty($_POST["gender"]) ? htmlspecialchars($_POST["gender"]) : $currentValues['gender'];
        $newLocationID1 = !empty($_POST["newLocationID1"]) ? intval($_POST["newLocationID1"]) : $currentValues['currentLocationID1'];
        $newLocationID2 = !empty($_POST["newLocationID2"]) ? intval($_POST["newLocationID2"]) : $currentValues['currentLocationID2'];
        $sessionLocationID = !empty($_POST["sessionLocationID"]) ? intval($_POST["sessionLocationID"]) : $currentValues['sessionLocationID'];
        $sessionType = !empty($_POST["sessionType"]) ? htmlspecialchars($_POST["sessionType"]) : $currentValues['sessionType'];
        $newTeam1Score = isset($_POST["newTeam1Score"]) ? intval($_POST["newTeam1Score"]) : $currentValues['team1Score'];
        $newTeam2Score = isset($_POST["newTeam2Score"]) ? intval($_POST["newTeam2Score"]) : $currentValues['team2Score'];
        $sessionStartDateTime = !empty($_POST["sessionStartDateTime"]) ? htmlspecialchars($_POST["sessionStartDateTime"]) : $currentValues['sessionStartDateTime'];

        // Update the team names and head coach IDs
        $updateTeam1 = "UPDATE Team SET teamName = :newTeamName1, headCoachID = :newHeadCoachID1, gender = :gender WHERE teamName = :currentTeamName1";
        $stmtUpdateTeam1 = $pdo->prepare($updateTeam1);
        $stmtUpdateTeam1->execute([
            ':newTeamName1' => $newTeamName1,
            ':newHeadCoachID1' => $newHeadCoachID1,
            ':gender' => $gender,
            ':currentTeamName1' => $currentTeamName1
        ]);

        $updateTeam2 = "UPDATE Team SET teamName = :newTeamName2, headCoachID = :newHeadCoachID2, gender = :gender WHERE teamName = :currentTeamName2";
        $stmtUpdateTeam2 = $pdo->prepare($updateTeam2);
        $stmtUpdateTeam2->execute([
            ':newTeamName2' => $newTeamName2,
            ':newHeadCoachID2' => $newHeadCoachID2,
            ':gender' => $gender,
            ':currentTeamName2' => $currentTeamName2
        ]);

        // Update the team locations
        $updateLocation1 = "UPDATE Formed_At SET locationID = :newLocationID1 WHERE teamName = :currentTeamName1";
        $stmtUpdateLocation1 = $pdo->prepare($updateLocation1);
        $stmtUpdateLocation1->execute([
            ':newLocationID1' => $newLocationID1,
            ':currentTeamName1' => $currentTeamName1
        ]);

        $updateLocation2 = "UPDATE Formed_At SET locationID = :newLocationID2 WHERE teamName = :currentTeamName2";
        $stmtUpdateLocation2 = $pdo->prepare($updateLocation2);
        $stmtUpdateLocation2->execute([
            ':newLocationID2' => $newLocationID2,
            ':currentTeamName2' => $currentTeamName2
        ]);

        // Update session details
        $updateSession = "UPDATE Sessions 
                          SET sessionType = :sessionType,
                              team1Score = :newTeam1Score,
                              team2Score = :newTeam2Score,
                              sessionStartDateTime = :sessionStartDateTime,
                              locationID = :sessionLocationID
                          WHERE sessionNum = (
                              SELECT sessionNum FROM Plays 
                              WHERE teamName1 = :currentTeamName1 AND teamName2 = :currentTeamName2
                          )";
        $stmtUpdateSession = $pdo->prepare($updateSession);
        $stmtUpdateSession->execute([
            ':sessionType' => $sessionType,
            ':newTeam1Score' => $newTeam1Score,
            ':newTeam2Score' => $newTeam2Score,
            ':sessionStartDateTime' => $sessionStartDateTime,
            ':sessionLocationID' => $sessionLocationID,
            ':currentTeamName1' => $currentTeamName1,
            ':currentTeamName2' => $currentTeamName2
        ]);

        // Commit the transaction
        $pdo->commit();

        // Close the statement and database connection
        $stmt = null;
        $stmtUpdateTeam1 = null;
        $stmtUpdateTeam2 = null;
        $stmtUpdateLocation1 = null;
        $stmtUpdateLocation2 = null;
        $stmtUpdateSession = null;
        $pdo = null;

        // Redirect to the success page with a message
        header("Location: ../success.php?message=Team+formation+updated+successfully");
        exit();

    } catch (PDOException $e) {
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
