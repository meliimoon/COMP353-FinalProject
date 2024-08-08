<?php
$results = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Include the database connection file
    require_once "db_files/db_connection.php";

    try {
        // Prepare the SQL query
        $query = "
       SELECT p.firstName, p.lastName, p.telephoneNumber, p.emailAddress,
    psnl.role, l.locationName
FROM Person p 
JOIN Personnel psnl ON p.personID = psnl.personID
JOIN Operates o ON psnl.personID = o.personID
JOIN Location l ON o.locationID = l.locationID
WHERE psnl.mandate = 'Volunteer'
AND o.endDate IS NULL
AND psnl.personID NOT IN (SELECT FamilyMember.personID FROM FamilyMember)

UNION

SELECT p.firstName, p.lastName, p.telephoneNumber, p.emailAddress,
    psnl.role, l.locationName
FROM Person p 
JOIN Personnel psnl ON p.personID = psnl.personID
JOIN Manages m ON psnl.personID = m.personID
JOIN Location l ON m.locationID = l.locationID
WHERE psnl.mandate = 'Volunteer'
AND m.endDate IS NULL
AND psnl.personID NOT IN (SELECT FamilyMember.personID FROM FamilyMember)

ORDER BY locationName ASC, role ASC, firstName ASC, lastName ASC;

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
    <title>Search Volunteers</title>
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
                <th>First Name</th>
                <th>Last Name</th>
                <th>Telephone Number</th>
                <th>Email Address</th>
                <th>Role</th>
                <th>Location Name</th>
              </tr>";
        echo "</thead>";
        echo "<tbody>";
        foreach ($results as $row) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row["firstName"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["lastName"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["telephoneNumber"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["emailAddress"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["role"]) . "</td>";
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
