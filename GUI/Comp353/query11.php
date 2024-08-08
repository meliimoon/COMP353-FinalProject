<?php
$results = [];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["startDate"]) && isset($_POST["endDate"])) {
    // Include the database connection file
    require_once "db_files/db_connection.php";

    // Collect POST data and sanitize it
    $startDate = htmlspecialchars($_POST["startDate"]);
    $endDate = htmlspecialchars($_POST["endDate"]);

    try {
        // Prepare the SQL query
        $query = "
            SELECT
                l.locationName,
                COUNT(CASE WHEN s.sessionType = 'Training' THEN 1 END) AS totalTrainingSessions,
                SUM(CASE WHEN s.sessionType = 'Training' THEN p.playerCount ELSE 0 END) AS totalPlayersInTrainingSessions,
                COUNT(CASE WHEN s.sessionType = 'Game' THEN 1 END) AS totalGameSessions,
                SUM(CASE WHEN s.sessionType = 'Game' THEN p.playerCount ELSE 0 END) AS totalPlayersInGameSessions
            FROM
                Location l
            JOIN
                Sessions s ON l.locationID = s.locationID
            JOIN
               (
                    SELECT
                        s.locationID,
                        s.sessionNum,
                        (COUNT(a1.personID) + COUNT(a2.personID)) AS playerCount
                    FROM
                        Sessions s
                    JOIN
                        Plays pl ON s.sessionNum = pl.sessionNum
                    JOIN
                        Apart_Of a1 ON pl.teamName1 = a1.teamName
                    JOIN
                        Apart_Of a2 ON pl.teamName2 = a2.teamName
                    WHERE
                        s.sessionStartDateTime BETWEEN :startDate AND :endDate
                    GROUP BY
                        s.locationID,
                        s.sessionNum
               ) p ON s.sessionNum = p.sessionNum
            WHERE
                s.sessionStartDateTime BETWEEN :startDate AND :endDate
            GROUP BY
                l.locationName
            HAVING
                COUNT(CASE WHEN s.sessionType = 'Game' THEN 1 END) >= 3
            ORDER BY
                totalGameSessions DESC;
        ";

        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':startDate', $startDate, PDO::PARAM_STR);
        $stmt->bindParam(':endDate', $endDate, PDO::PARAM_STR);

        // Execute the prepared statement
        $stmt->execute();

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Close the statement and database connection
        $stmt = null;
        $pdo = null;

    } catch (PDOException $e) {
        die("Query Failed: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="css_Files/search2.css">
    <title>Session Statistics by Location</title>
</head>
<body>
<style>
input[type="submit"] {
    background-color: #007bff;
    color: white;
    border: none;
    padding: 10px 10px;
    text-align: center;
    text-decoration: none;
    display: inline-block;
    font-size: 16px;
    margin: 4px 2px;
    cursor: pointer;
    border-radius: 8px;
    transition-duration: 0.4s;
}

input[type="submit"]:hover {
    background-color: #0056b3;
    color: white;
}

.button-container {
    text-align: center;
    margin-top: 20px;
}

form {
    text-align: center;
}
</style>
<h3>Search Session Statistics by Location</h3>

<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
    <label for="startDate">Enter Start Date (YYYY-MM-DD):</label>
    <input type="date" id="startDate" name="startDate" required><br><br>
    <label for="endDate">Enter End Date (YYYY-MM-DD):</label>
    <input type="date" id="endDate" name="endDate" required><br><br>
    <input type="submit" value="Search">
</form>

<h3>Search Results</h3>

<?php
    if(empty($results)){
        echo "<div>";
        echo "<p>There are no results</p>";
        echo "</div>";
    } else {
        echo "<table>";
        echo "<thead>";
        echo "<tr>
                <th>Location Name</th>
                <th>Total Training Sessions</th>
                <th>Total Players in Training Sessions</th>
                <th>Total Game Sessions</th>
                <th>Total Players in Game Sessions</th>
              </tr>";
        echo "</thead>";
        echo "<tbody>";
        foreach ($results as $row) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row["locationName"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["totalTrainingSessions"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["totalPlayersInTrainingSessions"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["totalGameSessions"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["totalPlayersInGameSessions"]) . "</td>";
            echo "</tr>";
        }
        echo "</tbody>";
        echo "</table>";
    }
?>
    
<!-- Back Button -->
<div class="button-container">
    <button onclick="window.history.back()">Back</button>
</div>

</body>
</html>
