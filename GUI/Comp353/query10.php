<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Include the database connection file
    require_once "db_files/db_connection.php";

    try {
        // Prepare the SQL query
        $query = "
            SELECT DISTINCT cm.clubMembershipID, p.firstName, p.lastName
            FROM ClubMember cm
            JOIN Person p ON cm.personID = p.personID
            JOIN Assignment a ON cm.personID = a.clubMemberID
            WHERE TIMESTAMPDIFF(YEAR, p.dateOfBirth, CURDATE()) BETWEEN 4 AND 10
              AND DATEDIFF(CURDATE(), (
                  SELECT MIN(a1.startDate)
                  FROM Assignment a1
                  WHERE a1.clubMemberID = cm.personID)
              ) <= 2 * 365
              AND cm.personID IN (
                  SELECT a2.clubMemberID
                  FROM Assignment a2
                  GROUP BY a2.clubMemberID
                  HAVING COUNT(DISTINCT a2.locationID) >= 4
              )
            ORDER BY cm.clubMembershipID ASC;
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
              </tr>";
        echo "</thead>";
        echo "<tbody>";
        foreach ($results as $row) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row["clubMembershipID"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["firstName"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["lastName"]) . "</td>";
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
