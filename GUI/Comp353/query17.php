<?php
$results = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Include the database connection file
    require_once "db_files/db_connection.php";

    try {
        // Prepare the SQL query
        $query = "
            SELECT p.firstName, p.lastName, m.startDate AS presidentStartDate, m.endDate AS presidentEndDate
            FROM Person p
            JOIN Personnel psnl ON p.personID = psnl.personID 
            JOIN Manages m ON psnl.personID = m.personID
            JOIN Location l ON m.locationID = l.locationID
            WHERE l.type = 'Head'
            ORDER BY p.firstName ASC, p.lastName ASC, m.startDate ASC;
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
    <title>Search Presidents</title>
</head>
<body>

<h3>Search for Presidents by Location Type</h3>

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
                <th>President Start Date</th>
                <th>President End Date</th>
              </tr>";
        echo "</thead>";
        echo "<tbody>";
        foreach ($results as $row) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row["firstName"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["lastName"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["presidentStartDate"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["presidentEndDate"]) . "</td>";
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

