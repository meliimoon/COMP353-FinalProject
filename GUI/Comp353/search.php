<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Include the database connection file
    require_once "db_files/db_connection.php";

    try {
        // Prepare the SQL query
        $query = "
            SELECT
                L.locationID,
                L.locationName,
                LD.address,
                LD.city,
                LD.province,
                LD.postalCode,
                L.phoneNumber,
                L.webAddress,
                L.type,
                L.capacity,
                COALESCE(CONCAT(P.firstName, ' ', P.lastName), 'No General Manager') AS generalManagerName,
                COUNT(DISTINCT A.clubMemberID) AS numOfClubMembers
            FROM
                Location L
            LEFT JOIN
                Found_At FA ON L.locationID = FA.locationID
            LEFT JOIN
                LocationDetails LD ON FA.postalCode = LD.postalCode
            LEFT JOIN
                Manages M ON L.locationID = M.locationID AND M.endDate IS NULL
            LEFT JOIN
                Personnel Per ON M.personID = Per.personID AND Per.role = 'Administrator'
            LEFT JOIN
                Person P ON Per.personID = P.personID 
            LEFT JOIN
                Assignment A ON L.locationID = A.locationID AND M.endDate IS NULL
            GROUP BY
                L.locationID, L.locationName, LD.address, LD.city, LD.province, LD.postalCode, L.phoneNumber, L.webAddress, L.type, L.capacity, P.firstName, P.lastName
            ORDER BY
                LD.province ASC, LD.city ASC;
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
                <th>Location Name</th>
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
            echo "<td>" . htmlspecialchars($row["locationName"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["address"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["city"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["province"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["postalCode"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["phoneNumber"]) . "</td>";
            echo "<td><a href='" . htmlspecialchars($row["webAddress"]) . "' target='_blank'>" . htmlspecialchars($row["webAddress"]) . "</a></td>";
            echo "<td>" . htmlspecialchars($row["type"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["capacity"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["generalManagerName"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["numOfClubMembers"]) . "</td>";
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
