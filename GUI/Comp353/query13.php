<?php
$results = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require_once "db_files/db_connection.php";

    try {
        $query = "
            SELECT cm.clubMembershipID, p.firstName, p.lastName, 
                TIMESTAMPDIFF(YEAR, p.dateOfBirth, CURDATE()) AS age,
                p.telephoneNumber,
                p.emailAddress,
                l.locationName AS currentLocationName
            FROM Person p
            JOIN ClubMember cm ON p.personID = cm.personID
            JOIN Assignment a ON cm.personID = a.clubMemberID
            JOIN Location l ON a.locationID = l.locationID
            WHERE TIMESTAMPDIFF(YEAR, p.dateOfBirth, CURDATE()) BETWEEN 4 AND 10
            AND a.endDate IS NULL
            AND cm.personID IN (SELECT personID FROM Apart_Of 
                GROUP BY personID 
                HAVING COUNT(DISTINCT role) = 1 
                AND MIN(role) = 'Goalkeeper')
            ORDER BY l.locationName ASC, cm.clubMembershipID ASC;
        ";

        $stmt = $pdo->prepare($query);
        $stmt->execute();

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
    
<div class="button-container">
    <button onclick="window.history.back()">Back</button>
</div>

</body>
</html>
