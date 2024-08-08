<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require_once "../db_files/db_connection.php"; // Ensure the correct path to your DB connection file

    // Collect and sanitize input data
    $clubMembershipID = intval($_POST['clubMembershipID']);

    try {
        // Prepare the SQL query to select from the relevant tables using clubMembershipID
        $query = "
        SELECT 
            cm.clubMembershipID, p.firstName, p.lastName, p.dateOfBirth, p.SSN, p.medicareCardNumber, 
            p.telephoneNumber, p.emailAddress, ld.address, ld.city, ld.province, ld.postalCode,
            fm.personID AS familyMemberID, a.primaryRelation,
            fp.firstName AS linkedFirstName, fp.lastName AS linkedLastName,
            sfm.firstName AS secondaryFirstName, sfm.lastName AS secondaryLastName, sfm.telephoneNumber AS secondaryTelephoneNumber,
            cm.gender
        FROM 
            ClubMember cm
        JOIN 
            Person p ON cm.personID = p.personID
        LEFT JOIN 
            Lives_At la ON p.personID = la.personID
        LEFT JOIN 
            LocationDetails ld ON la.postalCode = ld.postalCode
        LEFT JOIN 
            FamilyMember fm ON p.personID = fm.personID
        LEFT JOIN 
            Assignment a ON a.clubMemberID = p.personID
        LEFT JOIN 
            Person fp ON fp.personID = a.familyMemberID
        LEFT JOIN
            SecondaryFamilyMember sfm ON sfm.primaryFamilyMemberID = fm.personID
        WHERE 
            cm.clubMembershipID = :clubMembershipID";

        $stmt = $pdo->prepare($query);

        // Execute the prepared statement with the collected data
        $stmt->execute([':clubMembershipID' => $clubMembershipID]);

        // Fetch the result
        $person = $stmt->fetch(PDO::FETCH_ASSOC);

        // Close the statement and database connection
        $stmt = null;
        $pdo = null;
    } catch (PDOException $e) {
        // Handle database-related exceptions
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
    <title>Display Club Member</title>
    <style>
        /* Apply basic styling to the body */
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5fa;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        /* Center the content */
        .content {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        /* Container styling */
        .container {
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 600px;
        }

        /* Paragraph styling */
        p {
            margin-bottom: 10px;
        }

        /* Link styling */
        a {
            color: #007bff;
            text-decoration: none;
        }

        /* Button styling */
        .button {
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="content">
        <h3>Club Member Details</h3>
        <div class="container">
        <?php if (isset($person) && $person): ?>
            <p><strong>Club Membership ID:</strong> <?php echo htmlspecialchars($person["clubMembershipID"]); ?></p>
            <p><strong>First Name:</strong> <?php echo htmlspecialchars($person["firstName"]); ?></p>
            <p><strong>Last Name:</strong> <?php echo htmlspecialchars($person["lastName"]); ?></p>
            <p><strong>Gender:</strong> <?php echo htmlspecialchars($person["gender"]); ?></p>
            <p><strong>Date of Birth:</strong> <?php echo htmlspecialchars($person["dateOfBirth"]); ?></p>
            <p><strong>SSN:</strong> <?php echo htmlspecialchars($person["SSN"]); ?></p>
            <p><strong>Medicare Card Number:</strong> <?php echo htmlspecialchars($person["medicareCardNumber"]); ?></p>
            <p><strong>Telephone Number:</strong> <?php echo htmlspecialchars($person["telephoneNumber"]); ?></p>
            <p><strong>Email Address:</strong> <?php echo htmlspecialchars($person["emailAddress"]); ?></p>
            <p><strong>Address:</strong> <?php echo htmlspecialchars($person["address"]); ?></p>
            <p><strong>City:</strong> <?php echo htmlspecialchars($person["city"]); ?></p>
            <p><strong>Province:</strong> <?php echo htmlspecialchars($person["province"]); ?></p>
            <p><strong>Postal Code:</strong> <?php echo htmlspecialchars($person["postalCode"]); ?></p>
            <h4 style="color: blue; text-decoration: underline; font-style: italic;">Associated Family Members</h4>
            <p><strong>Family Member Name:</strong> <?php echo htmlspecialchars($person["linkedFirstName"] . ' ' . $person["linkedLastName"]); ?></p>
            <p><strong>Primary Relation:</strong> <?php echo htmlspecialchars($person["primaryRelation"]); ?></p>
        <?php else: ?>
            <p>No matching club member found.</p>
        <?php endif; ?>
        </div>
        <a href="../index.php" class="button">Return to main page</a>
    </div>
</body>
</html>
