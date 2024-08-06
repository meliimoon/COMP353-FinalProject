<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Include the database connection file
    require_once "../db_files/db_connection.php";

    // Collect POST data and sanitize it
    $sessionNum = htmlspecialchars($_POST["sessionNum"]);

    try {
        // Prepare the SQL query to select team formation details
        $query = "
        SELECT 
            s.sessionNum, s.sessionType, s.team1Score, s.team2Score, s.sessionStartDateTime, 
            l.locationName, l.phoneNumber, l.webAddress, l.type, l.capacity,
            t1.teamName AS team1Name, t1.gender AS team1Gender, p1.personID AS team1HeadCoachID, p1.firstName AS team1HeadCoachFirstName, p1.lastName AS team1HeadCoachLastName,
            t2.teamName AS team2Name, t2.gender AS team2Gender, p2.personID AS team2HeadCoachID, p2.firstName AS team2HeadCoachFirstName, p2.lastName AS team2HeadCoachLastName,
            a1.role AS team1Role, p3.firstName AS team1PlayerFirstName, p3.lastName AS team1PlayerLastName,
            a2.role AS team2Role, p4.firstName AS team2PlayerFirstName, p4.lastName AS team2PlayerLastName
        FROM 
            Sessions s
        JOIN 
            Plays pl ON s.sessionNum = pl.sessionNum
        JOIN 
            Team t1 ON pl.teamName1 = t1.teamName
        JOIN 
            Team t2 ON pl.teamName2 = t2.teamName
        JOIN 
            Location l ON s.locationID = l.locationID
        LEFT JOIN 
            Apart_Of a1 ON t1.teamName = a1.teamName
        LEFT JOIN 
            Apart_Of a2 ON t2.teamName = a2.teamName
        LEFT JOIN 
            ClubMember c1 ON a1.personID = c1.personID
        LEFT JOIN 
            ClubMember c2 ON a2.personID = c2.personID
        LEFT JOIN 
            Person p1 ON t1.headCoachID = p1.personID
        LEFT JOIN 
            Person p2 ON t2.headCoachID = p2.personID
        LEFT JOIN 
            Person p3 ON c1.personID = p3.personID
        LEFT JOIN 
            Person p4 ON c2.personID = p4.personID
        WHERE 
            s.sessionNum = :sessionNum
        GROUP BY 
            a1.role, p3.firstName, p3.lastName, a2.role, p4.firstName, p4.lastName
        ORDER BY 
            t1.teamName, t2.teamName, a1.role, a2.role";

        $stmt = $pdo->prepare($query);

        // Execute the prepared statement with the collected data
        $stmt->execute([':sessionNum' => $sessionNum]);

        // Fetch the result
        $teamFormation = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Close the statement and database connection
        $stmt = null;
        $pdo = null;
    } catch (PDOException $e) {
        // Handle database-related exceptions
        die("Query Failed: " . $e->getMessage());
    }
} else {
    // Redirect to the index page if the request method is not POST
    header("Location: ../index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Display Team Formation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5fa;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .content {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }
        .container {
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 800px;
        }
        p {
            margin-bottom: 10px;
        }
        a {
            color: #007bff;
            text-decoration: none;
        }
        .button {
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border: none;
            border-radius: 5px;
            cursor: pointer.
        }
        .button:hover {
            background-color: #0056b3.
        }
    </style>
</head>
<body>
    <div class="content">
        <div class="container">
        <?php if (isset($teamFormation) && $teamFormation): ?>
            <?php 
            // Display session and general details once
            $formation = $teamFormation[0];
            ?>
            <h3>Team Formation Details</h3>

            <p><strong>Session Number:</strong> <?php echo htmlspecialchars($formation["sessionNum"]); ?></p>
            <p><strong>Session Type:</strong> <?php echo htmlspecialchars($formation["sessionType"]); ?></p>
            <p><strong>Team 1 Name:</strong> <?php echo htmlspecialchars($formation["team1Name"]); ?></p>
            <p><strong>Team 1 Gender:</strong> <?php echo htmlspecialchars($formation["team1Gender"]); ?></p>
            <p><strong>Team 1 Head Coach:</strong> <?php echo htmlspecialchars($formation["team1HeadCoachFirstName"] . ' ' . $formation["team1HeadCoachLastName"]); ?></p>
            <p><strong>Team 2 Name:</strong> <?php echo htmlspecialchars($formation["team2Name"]); ?></p>
            <p><strong>Team 2 Gender:</strong> <?php echo htmlspecialchars($formation["team2Gender"]); ?></p>
            <p><strong>Team 2 Head Coach:</strong> <?php echo htmlspecialchars($formation["team2HeadCoachFirstName"] . ' ' . $formation["team2HeadCoachLastName"]); ?></p>
            <p><strong>Location:</strong> <?php echo htmlspecialchars($formation["locationName"]); ?></p>
            <p><strong>Location Phone Number:</strong> <?php echo htmlspecialchars($formation["phoneNumber"]); ?></p>
            <p><strong>Location Web Address:</strong> <?php echo htmlspecialchars($formation["webAddress"]); ?></p>
            <p><strong>Location Type:</strong> <?php echo htmlspecialchars($formation["type"]); ?></p>
            <p><strong>Location Capacity:</strong> <?php echo htmlspecialchars($formation["capacity"]); ?></p>
            <p><strong>Team 1 Score:</strong> <?php echo htmlspecialchars($formation["team1Score"]); ?></p>
            <p><strong>Team 2 Score:</strong> <?php echo htmlspecialchars($formation["team2Score"]); ?></p>
            <p><strong>Session Start Date and Time:</strong> <?php echo htmlspecialchars($formation["sessionStartDateTime"]); ?></p>
            <hr>
            
            <h4>Team 1 Players</h4>
            <?php
            $displayedPlayers = [];
            foreach ($teamFormation as $formation):
                if (!in_array($formation["team1PlayerFirstName"] . ' ' . $formation["team1PlayerLastName"], $displayedPlayers) && $formation["team1Role"]):
                    $displayedPlayers[] = $formation["team1PlayerFirstName"] . ' ' . $formation["team1PlayerLastName"];
            ?>
                <p><strong>Player Name:</strong> <?php echo htmlspecialchars($formation["team1PlayerFirstName"] . ' ' . $formation["team1PlayerLastName"]); ?></p>
                <p><strong>Role:</strong> <?php echo htmlspecialchars($formation["team1Role"]); ?></p>

            <?php
                endif;
            endforeach;
            ?>
            
            <hr>
            <h4>Team 2 Players</h4>
            <?php
            $displayedPlayers = [];
            foreach ($teamFormation as $formation):
                if (!in_array($formation["team2PlayerFirstName"] . ' ' . $formation["team2PlayerLastName"], $displayedPlayers) && $formation["team2Role"]):
                    $displayedPlayers[] = $formation["team2PlayerFirstName"] . ' ' . $formation["team2PlayerLastName"];
            ?>
                <p><strong>Player Name:</strong> <?php echo htmlspecialchars($formation["team2PlayerFirstName"] . ' ' . $formation["team2PlayerLastName"]); ?></p>
                <p><strong>Role:</strong> <?php echo htmlspecialchars($formation["team2Role"]); ?></p>

            <?php
                endif;
            endforeach;
            ?>
        <?php else: ?>
            <p>No matching session found.</p>
        <?php endif; ?>
        </div>
        <a href="../index.php" class="button">Return to main page</a>
    </div>
</body>
</html>
