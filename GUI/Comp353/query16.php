<?php
$results = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Include the database connection file
    require_once "db_files/db_connection.php";

    try {
        // Prepare the SQL query
        $query = "
            SELECT cm.clubMembershipID, p.firstName, p.lastName, 
                   TIMESTAMPDIFF(YEAR, p.dateOfBirth, CURDATE()) AS age, 
                   p.telephoneNumber, p.emailAddress, l.locationName
            FROM ClubMember cm
            JOIN Person p ON cm.personID = p.personID
            JOIN Assignment a ON cm.personID = a.clubMemberID
            JOIN Location l ON a.locationID = l.locationID
            JOIN Apart_Of ap ON cm.personID = ap.personID
            JOIN Team t ON ap.teamName = t.teamName
            JOIN Plays py ON ap.teamName = py.teamName1 OR ap.teamName = py.teamName2
            JOIN Sessions s ON py.sessionNum = s.sessionNum
            WHERE TIMESTAMPDIFF(YEAR, p.dateOfBirth, CURDATE()) BETWEEN 4 AND 10
              AND a.endDate IS NULL
              AND s.sessionType = 'Game'
              AND ((s.team1Score > s.team2Score AND ap.teamName = py.teamName1) 
                   OR (s.team2Score > s.team1Score AND ap.teamName = py.teamName2))
              AND NOT EXISTS 
                ( 
                    SELECT 1 
                    FROM Plays py2 
                    JOIN Sessions s2 ON py2.sessionNum = s2.sessionNum 
                    JOIN Apart_Of ap2 ON (ap2.teamName = py2.teamName1 OR ap2.teamName = py2.teamName2)             
                    WHERE ap2.personID = cm.personID 
                      AND ((s2.team1Score < s2.team2Score AND ap2.teamName = py2.teamName1) 
                           OR (s2.team2Score < s2.team1Score AND ap2.teamName = py2.teamName2))
                )
            ORDER BY l.locationName ASC, cm.clubMembershipID ASC;
        ";

        $stmt = $pdo->prepare($query);

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
    <title>Search Club Members</title>
</head>
<body>

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
                <th>Club Membership ID</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Age</th>
                <th>Telephone Number</th>
                <th>Email Address</th>
                <th>Location Name</th>
              </tr>";
        echo "</thead>";
        echo "<tbody>";
        foreach ($results as $row) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row["clubMembershipID"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["firstName"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["lastName"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["age"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["telephoneNumber"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["emailAddress"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["locationName"]) . "</td>";
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
