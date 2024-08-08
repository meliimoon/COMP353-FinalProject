<?php
$results = [];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["sessionDate"]) && isset($_POST["locationID"])) {
    // Include the database connection file
    require_once "db_files/db_connection.php";

    // Collect POST data and sanitize it
    $sessionDate = htmlspecialchars($_POST["sessionDate"]);
    $locationID = intval($_POST["locationID"]);

    try {
        // Prepare the SQL query
        $query = "
            SELECT
               p.teamName1 AS 'Team Name 1',
               hc1.firstName AS 'Head Coach 1 First Name',
               hc1.lastName AS 'Head Coach 1 Last Name',
               p.teamName2 AS 'Team Name 2',
               hc2.firstName AS 'Head Coach 2 First Name',
               hc2.lastName AS 'Head Coach 2 Last Name',
               s.sessionStartDateTime AS 'Session Start Time',
               ld.address AS 'Session Address',
               s.sessionType AS 'Session Type',
               s.team1Score AS 'Team 1 Score',
               s.team2Score AS 'Team 2 Score',
               per.firstName AS 'Player First Name',
               per.lastName AS 'Player Last Name',
               ao.role AS 'Player Role'
            FROM
               Plays p
            JOIN
               Sessions s ON s.sessionNum = p.sessionNum
            LEFT JOIN
               Location l ON s.locationID = l.locationID
            LEFT JOIN
               Found_At fa ON l.locationID = fa.locationID
            LEFT JOIN
               LocationDetails ld ON fa.postalCode = ld.postalCode
            LEFT JOIN
               Team t1 ON p.teamName1 = t1.teamName
            LEFT JOIN
               Personnel pc1 ON t1.headCoachID = pc1.personID
            LEFT JOIN
               Person hc1 ON pc1.personID = hc1.personID
            LEFT JOIN
               Team t2 ON p.teamName2 = t2.teamName
            LEFT JOIN
               Personnel pc2 ON t2.headCoachID = pc2.personID
            LEFT JOIN
               Person hc2 ON pc2.personID = hc2.personID
            LEFT JOIN
               Apart_Of ao ON t1.teamName = ao.teamName OR t2.teamName = ao.teamName
            LEFT JOIN
               ClubMember cm ON ao.personID = cm.personID
            LEFT JOIN
               Person per ON cm.personID = per.personID
            WHERE
               DATE(s.sessionStartDateTime) = :sessionDate 
               AND l.locationID = :locationID 
            ORDER BY
               s.sessionStartDateTime ASC;
        ";

        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':sessionDate', $sessionDate, PDO::PARAM_STR);
        $stmt->bindParam(':locationID', $locationID, PDO::PARAM_INT);

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
    <title>Search Sessions</title>
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
</head>
<body>

<h3>Search for Sessions by Date and Location</h3>

<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
    <label for="sessionDate">Enter Session Date (YYYY-MM-DD):</label>
    <input type="date" id="sessionDate" name="sessionDate" required><br><br>
    <label for="locationID">Enter Location ID:</label>
    <input type="number" id="locationID" name="locationID" required><br><br>
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
                <th>Team Name 1</th>
                <th>Head Coach 1 First Name</th>
                <th>Head Coach 1 Last Name</th>
                <th>Team Name 2</th>
                <th>Head Coach 2 First Name</th>
                <th>Head Coach 2 Last Name</th>
                <th>Session Start Time</th>
                <th>Session Address</th>
                <th>Session Type</th>
                <th>Team 1 Score</th>
                <th>Team 2 Score</th>
                <th>Player First Name</th>
                <th>Player Last Name</th>
                <th>Player Role</th>
              </tr>";
        echo "</thead>";
        echo "<tbody>";
        foreach ($results as $row) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row["Team Name 1"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["Head Coach 1 First Name"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["Head Coach 1 Last Name"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["Team Name 2"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["Head Coach 2 First Name"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["Head Coach 2 Last Name"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["Session Start Time"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["Session Address"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["Session Type"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["Team 1 Score"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["Team 2 Score"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["Player First Name"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["Player Last Name"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["Player Role"]) . "</td>";
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
