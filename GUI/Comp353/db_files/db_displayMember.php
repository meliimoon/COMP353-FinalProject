<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Include the database connection file
    require_once "../db_files/db_connection.php";

    // Collect POST data and sanitize it
    $personID = htmlspecialchars($_POST["personID"]);

    try {
        // Prepare the SQL query to select from the Person table and related tables
        $query = "
        SELECT 
            p.personID, p.SSN, p.firstName, p.lastName, p.medicareCardNumber, 
            p.dateOfBirth, p.telephoneNumber, p.emailAddress, 
            ld.address, ld.city, ld.province, ld.postalCode,
            sfm.firstName AS secondaryFirstName, sfm.lastName AS secondaryLastName, 
            sfm.telephoneNumber AS secondaryTelephoneNumber,
            ra.locationID, ra.startDate, ra.endDate
        FROM 
            Person p
        LEFT JOIN 
            Lives_At la ON p.personID = la.personID
        LEFT JOIN 
            LocationDetails ld ON la.postalCode = ld.postalCode
        LEFT JOIN 
            FamilyMember fm ON p.personID = fm.personID
        LEFT JOIN 
            SecondaryFamilyMember sfm ON fm.personID = sfm.primaryFamilyMemberID
        LEFT JOIN 
            Registered_At ra ON p.personID = ra.personID
        WHERE 
            p.personID = :personID
        ORDER BY 
            ra.startDate DESC
        LIMIT 1";

        $stmt = $pdo->prepare($query);

        // Execute the prepared statement with the collected data
        $stmt->execute([':personID' => $personID]);

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
    <title>Display Family Member</title>
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
        <h3>Family Member Details</h3>
        <div class="container">
        <?php if (isset($person) && $person): ?>
            <p><strong>Person ID:</strong> <?php echo htmlspecialchars($person["personID"]); ?></p>
            <p><strong>SSN:</strong> <?php echo htmlspecialchars($person["SSN"]); ?></p>
            <p><strong>First Name:</strong> <?php echo htmlspecialchars($person["firstName"]); ?></p>
            <p><strong>Last Name:</strong> <?php echo htmlspecialchars($person["lastName"]); ?></p>
            <p><strong>Medicare Card Number:</strong> <?php echo htmlspecialchars($person["medicareCardNumber"]); ?></p>
            <p><strong>Date of Birth:</strong> <?php echo htmlspecialchars($person["dateOfBirth"]); ?></p>
            <p><strong>Telephone Number:</strong> <?php echo htmlspecialchars($person["telephoneNumber"]); ?></p>
            <p><strong>Email Address:</strong> <?php echo htmlspecialchars($person["emailAddress"]); ?></p>
            <p><strong>Address:</strong> <?php echo htmlspecialchars($person["address"]); ?></p>
            <p><strong>City:</strong> <?php echo htmlspecialchars($person["city"]); ?></p>
            <p><strong>Province:</strong> <?php echo htmlspecialchars($person["province"]); ?></p>
            <p><strong>Postal Code:</strong> <?php echo htmlspecialchars($person["postalCode"]); ?></p>
            <p><strong>Registered At Location ID:</strong> <?php echo htmlspecialchars($person["locationID"]); ?></p>
            <p><strong>Start Date:</strong> <?php echo htmlspecialchars($person["startDate"]); ?></p>
            <p><strong>End Date:</strong> <?php echo htmlspecialchars($person["endDate"]); ?></p>
            <h4 style="color: blue; text-decoration: underline; font-style: italic;">Secondary Family Members</h4>
            <?php if (!empty($person["secondaryFirstName"])): ?>
                <p><strong>Secondary First Name:</strong> <?php echo htmlspecialchars($person["secondaryFirstName"]); ?></p>
                <p><strong>Secondary Last Name:</strong> <?php echo htmlspecialchars($person["secondaryLastName"]); ?></p>
                <p><strong>Secondary Telephone Number:</strong> <?php echo htmlspecialchars($person["secondaryTelephoneNumber"]); ?></p>
            <?php else: ?>
                <p>No secondary family members found.</p>
            <?php endif; ?>
        <?php else: ?>
            <p>No matching family member found.</p>
        <?php endif; ?>
        </div>
        <a href="../index.php" class="button">Return to main page</a>
    </div>
</body>
</html>
