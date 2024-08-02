<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Include the database connection file
    require_once "db_files/db_connection.php";

    try {
        // Prepare the SQL query
        $query = "
    SELECT
    L.address,
    L.city,
    L.province,
    L.postalCode,
    L.phoneNumber,
    L.webAddress,
    L.type,
    L.capacity,
    CONCAT(P.firstName, ' ', P.lastName) AS generalManagerName,
    IFNULL(CM.clubMemberCount, 0) AS numberOfClubMembers
FROM
    Location L
    LEFT JOIN Manages M ON L.locationID = M.locationID AND M.endDate IS NULL
    LEFT JOIN Person P ON M.personID = P.personID
    LEFT JOIN (
        SELECT
            LA.postalCode,
            COUNT(CM.personID) AS clubMemberCount
        FROM
            Lives_At LA
            JOIN ClubMember CM ON LA.personID = CM.personID
        GROUP BY
            LA.postalCode
    ) CM ON L.postalCode = CM.postalCode
ORDER BY
    L.province ASC,
    L.city ASC;
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
                <th>Location ID</th>
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
            echo "<td>" . htmlspecialchars($row["locationID"]) . "</td>";
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
