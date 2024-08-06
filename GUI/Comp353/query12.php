<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Include the database connection file
    require_once "db_files/db_connection.php";

    try {
        // Prepare the SQL query
        $query = "
            SELECT cm.clubMembershipID, p.firstName, p.lastName, 
                   TIMESTAMPDIFF(YEAR, p.dateOfBirth, CURDATE()) AS age,
                   p.telephoneNumber, p.emailAddress, 
                   l.locationName AS currentLocationName
            FROM ClubMember cm
            JOIN Person p ON cm.personID = p.personID
            JOIN Assignment a ON cm.personID = a.clubMemberID
            JOIN Location l ON a.locationID = l.locationID
            WHERE TIMESTAMPDIFF(YEAR, p.dateOfBirth, CURDATE()) BETWEEN 4 AND 10
              AND cm.personID NOT IN (SELECT Apart_Of.personID FROM Apart_Of)
              AND a.endDate IS NULL
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
    <link rel="stylesheet" type="text/css" href="css_Files/search2.css">
    <title>Search Results</title>
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
    
<!-- Back Button -->
<div class="button-container">
    <button onclick="window.history.back()">Back</button>
</div>

</body>
</html>
