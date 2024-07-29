<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Include the database connection file
    require_once "dbconnection.php";

    try {
        // Prepare the SQL query
        $query = "
            SELECT
                l.address,
                l.city,
                l.province,
                l.postalCode,
                l.phoneNumber,
                l.webAddress,
                l.type,
                l.capacity,
                (
                SELECT
                    CONCAT(p.firstName, ' ', p.lastName)
                FROM 
                    Person p,
                    Personnels pl,
                    Manages m
                WHERE 
                    l.locationID = m.locationID
                    AND ISNULL(m.endDate)
                    AND m.personID = pl.personID
                    AND pl.personID = p.personID
                ) AS 'General Manager Name',
                (
                SELECT
                    COUNT(aw.clubMemberID)
                FROM 
                    AssociatedWith aw,
                    Person p,
                    ClubMembers cm
                WHERE 
                    l.locationID = aw.locationID
                    AND aw.clubMemberID = cm.personID
                    AND cm.personID = p.personID
                    AND ISNULL(aw.endDate)
                        AND TIMESTAMPDIFF(YEAR,
                        p.DOB,
                        CURDATE()) BETWEEN 4 AND 10
                ) AS 'Club Members Count'
            FROM
                Location l
            ORDER BY
                l.province,
                l.city;
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
    <link rel="stylesheet" type="text/css" href="search2.css">
    <title>Search</title>
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
                <th>Address</th>
                <th>City</th>
                <th>Province</th>
                <th>Postal Code</th>
                <th>Phone Number</th>
                <th>Web Address</th>
                <th>Type</th>
                <th>Capacity</th>
                <th>General Manager Name</th>
                <th>Club Members Count</th>
              </tr>";
        echo "</thead>";
        echo "<tbody>";
        foreach ($results as $row) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row["address"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["city"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["province"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["postalCode"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["phoneNumber"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["webAddress"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["type"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["capacity"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["General Manager Name"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["Club Members Count"]) . "</td>";
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
