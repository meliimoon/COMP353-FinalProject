<?php
$results = [];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["familyMemberID"])) {
    // Include the database connection file
    require_once "db_files/db_connection.php";

    // Collect POST data and sanitize it
    $familyMemberID = intval($_POST["familyMemberID"]);

    try {
        // Prepare the SQL query
        $query = "
            SELECT DISTINCT
                SF.firstName AS secondaryFirstName,
                SF.lastName AS secondaryLastName,
                SF.telephoneNumber AS secondaryPhoneNumber,
                CM.clubMembershipID,
                P.firstName AS clubMemberFirstName,
                P.lastName AS clubMemberLastName,
                P.dateOfBirth,
                P.SSN,
                P.medicareCardNumber,
                P.telephoneNumber AS clubMemberPhoneNumber,
                LD.address,
                LD.city,
                LD.province,
                LD.postalCode,
                A.secondaryRelation AS secondaryRelation
            FROM
                Assignment A
            LEFT JOIN
                SecondaryFamilyMember SF ON A.familyMemberID = SF.primaryFamilyMemberID
                                        AND A.secondaryID = SF.secondaryFamilyMemberID
            LEFT JOIN
                ClubMember CM ON A.clubMemberID = CM.personID
            LEFT JOIN
                Person P ON CM.personID = P.personID
            LEFT JOIN
                Lives_At LA ON P.personID = LA.personID
            LEFT JOIN
                LocationDetails LD ON LA.postalCode = LD.postalCode
            WHERE
                A.familyMemberID = :familyMemberID;
        ";

        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':familyMemberID', $familyMemberID, PDO::PARAM_INT);

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
    <title>Search Secondary Family Members</title>
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
<h3>Search for Secondary Family Members</h3>

<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
    <label for="familyMemberID">Enter Family MemberID:</label>
    <input type="number" id="familyMemberID" name="familyMemberID" required><br><br>
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
                <th>Secondary First Name</th>
                <th>Secondary Last Name</th>
                <th>Secondary Phone Number</th>
                <th>Club Membership ID</th>
                <th>Club Member First Name</th>
                <th>Club Member Last Name</th>
                <th>Date of Birth</th>
                <th>SSN</th>
                <th>Medicare Card Number</th>
                <th>Club Member Phone Number</th>
                <th>Address</th>
                <th>City</th>
                <th>Province</th>
                <th>Postal Code</th>
                <th>Secondary Relation</th>
              </tr>";
        echo "</thead>";
        echo "<tbody>";
        foreach ($results as $row) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row["secondaryFirstName"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["secondaryLastName"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["secondaryPhoneNumber"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["clubMembershipID"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["clubMemberFirstName"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["clubMemberLastName"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["dateOfBirth"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["SSN"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["medicareCardNumber"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["clubMemberPhoneNumber"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["address"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["city"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["province"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["postalCode"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["secondaryRelation"]) . "</td>";
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
