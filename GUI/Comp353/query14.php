<?php
$results = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Include the database connection file
    require_once "db_files/db_connection.php";

    try {
        // Prepare the SQL query
        $query = "
     SELECT DISTINCT cm.clubMembershipID, p.firstName, p.lastName, 
    TIMESTAMPDIFF(YEAR, p.dateOfBirth, CURDATE()) AS age,
    p.telephoneNumber,
    p.emailAddress,
    l.locationName AS currentLocationName
FROM Person p
JOIN ClubMember cm ON p.personID = cm.personID
JOIN Assignment a ON cm.personID = a.clubMemberID
JOIN Location l ON a.locationID = l.locationID
JOIN Apart_Of ao ON cm.personID = ao.personID
JOIN Plays py ON ao.teamName = py.teamName1 OR ao.teamName = py.teamName2
JOIN Sessions s ON py.sessionNum = s.sessionNum
WHERE TIMESTAMPDIFF(YEAR, p.dateOfBirth, CURDATE()) BETWEEN 4 AND 10
	AND s.sessionType = 'Game'
AND cm.personID IN (SELECT personID FROM Apart_Of 
GROUP BY personID 
HAVING COUNT(DISTINCT role) = 4)
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

<h3>Search for Club Members</h3>

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
                <th>Current Location Name</th>
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
            echo "<td>" . htmlspecialchars($row["currentLocationName"]) . "</td>";
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
