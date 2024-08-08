<?php
$results = [];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["locationID"])) {
    // Include the database connection file
    require_once "db_files/db_connection.php";

    // Collect POST data and sanitize it
    $locationID = intval($_POST["locationID"]);

    try {
        // Prepare the SQL query
        $query = "
            SELECT DISTINCT p1.firstName, p1.lastName, p1.telephoneNumber
            FROM Person p1
            JOIN FamilyMember fm ON p1.personID = fm.personID
            JOIN Assignment a ON fm.personID = a.familyMemberID
            JOIN ClubMember cm ON a.clubMemberID = cm.personID
            JOIN Person p2 ON cm.personID = p2.personID
            JOIN Location l ON a.locationID = l.locationID
            JOIN Formed_At fa ON l.locationID = fa.locationID
            JOIN Team t ON fa.teamName = t.teamName
            WHERE l.locationID = ?
            AND TIMESTAMPDIFF(YEAR, p2.dateOfBirth, CURDATE()) BETWEEN 4 AND 10
            AND a.endDate IS NULL
            AND t.headCoachID = p1.personID;
        ";

        $stmt = $pdo->prepare($query);
        $stmt->bindParam(1, $locationID, PDO::PARAM_INT);

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
    <title>Search Family Members</title>
</head>
<body>
<style>
input[type="submit"] {
    background-color: #007bff;
    color: white;
    border: none;
    padding: 10px 10px;
    text-align: center;
    text-decoration: none;
    display: inline-block;
    font-size: 16px;
    margin: 4px 2px;
    cursor: pointer;
    border-radius: 8px;
    transition-duration: 0.4s;
}

input[type="submit"]:hover {
    background-color: #0056b3;
    color: white;
}

.button-container {
    text-align: center;
    margin-top: 20px;
}

form {
    text-align: center;
}
</style>
<h3>Search for Family Members by Location</h3>

<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
    <label for="locationID">Enter Location ID:</label>
    <input type="number" id="locationID" name="locationID" required><br><br>
    <input type="submit" value="Search">
</form>

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
              </tr>";
        echo "</thead>";
        echo "<tbody>";
        foreach ($results as $row) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row["firstName"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["lastName"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["telephoneNumber"]) . "</td>";
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
