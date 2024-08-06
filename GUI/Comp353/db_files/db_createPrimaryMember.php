<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require_once "../db_files/db_connection.php"; // Ensure correct path to your DB connection file

    // Sanitize and collect input data for primary family member
    $firstName = htmlspecialchars($_POST["firstName"]);
    $lastName = htmlspecialchars($_POST["lastName"]);
    $DOB = htmlspecialchars($_POST["DOB"]);
    $SSN = htmlspecialchars($_POST["SSN"]);
    $medicareNumber = htmlspecialchars($_POST["medicareNumber"]);
    $telephoneNumber = htmlspecialchars($_POST["telephoneNumber"]);
    $address = htmlspecialchars($_POST["address"]);
    $city = htmlspecialchars($_POST["city"]);
    $province = htmlspecialchars($_POST["province"]);
    $postalCode = htmlspecialchars($_POST["postalCode"]);
    $email = htmlspecialchars($_POST["email"]);
    $locationID = intval($_POST["locationID"]);
    $startDate = htmlspecialchars($_POST["startDate"]);
    $endDate = htmlspecialchars($_POST["endDate"]); // Can be empty

    // Sanitize and collect input data for secondary family member
    $S_firstName = htmlspecialchars($_POST["S_firstName"]);
    $S_lastName = htmlspecialchars($_POST["S_lastName"]);
    $S_telephoneNumber = htmlspecialchars($_POST["S_telephoneNumber"]);

    try {
        $pdo->beginTransaction();

        // Check if postalCode exists in LocationDetails
        $checkPostalCode = "SELECT COUNT(*) FROM LocationDetails WHERE postalCode = :postalCode";
        $stmtCheck = $pdo->prepare($checkPostalCode);
        $stmtCheck->execute([':postalCode' => $postalCode]);
        $exists = $stmtCheck->fetchColumn();

        if (!$exists) {
            // Insert postalCode into LocationDetails if it doesn't exist
            $insertPostalCode = "INSERT INTO LocationDetails (postalCode, city, province, address) VALUES (:postalCode, :city, :province, :address)";
            $stmtPostalCode = $pdo->prepare($insertPostalCode);
            $stmtPostalCode->execute([
                ':postalCode' => $postalCode,
                ':city' => $city,
                ':province' => $province,
                ':address' => $address
            ]);
        }

        // Insert into Person table
        $insertPerson = "INSERT INTO Person (SSN, firstName, lastName, medicareCardNumber, dateOfBirth, telephoneNumber, emailAddress)
                         VALUES (:SSN, :firstName, :lastName, :medicareNumber, :DOB, :telephoneNumber, :email)";
        $stmtPerson = $pdo->prepare($insertPerson);
        $stmtPerson->execute([
            ':SSN' => $SSN,
            ':firstName' => $firstName,
            ':lastName' => $lastName,
            ':medicareNumber' => $medicareNumber,
            ':DOB' => $DOB,
            ':telephoneNumber' => $telephoneNumber,
            ':email' => $email
        ]);
        $personID = $pdo->lastInsertId();

        // Insert into Lives_At table
        $insertLivesAt = "INSERT INTO Lives_At (personID, postalCode) VALUES (:personID, :postalCode)";
        $stmtLivesAt = $pdo->prepare($insertLivesAt);
        $stmtLivesAt->execute([
            ':personID' => $personID,
            ':postalCode' => $postalCode
        ]);

        // Insert into FamilyMember table
        $insertFamilyMember = "INSERT INTO FamilyMember (personID) VALUES (:personID)";
        $stmtFamilyMember = $pdo->prepare($insertFamilyMember);
        $stmtFamilyMember->execute([':personID' => $personID]);

        // Insert into Registered_At table
        $insertRegisteredAt = "INSERT INTO Registered_At (personID, startDate, endDate, locationID)
                               VALUES (:personID, :startDate, :endDate, :locationID)";
        $stmtRegisteredAt = $pdo->prepare($insertRegisteredAt);
        $stmtRegisteredAt->execute([
            ':personID' => $personID,
            ':startDate' => $startDate,
            ':endDate' => (empty($endDate) ? NULL : $endDate),
            ':locationID' => $locationID
        ]);

        // Insert into SecondaryFamilyMember table if secondary family member details are provided
        if (!empty($S_firstName) && !empty($S_lastName) && !empty($S_telephoneNumber)) {
            $insertSecondaryFamilyMember = "INSERT INTO SecondaryFamilyMember (primaryFamilyMemberID, secondaryFamilyMemberID, firstName, lastName, telephoneNumber)
                                            VALUES (:primaryFamilyMemberID, 1, :S_firstName, :S_lastName, :S_telephoneNumber)";
            $stmtSecondaryFamilyMember = $pdo->prepare($insertSecondaryFamilyMember);
            $stmtSecondaryFamilyMember->execute([
                ':primaryFamilyMemberID' => $personID,
                ':S_firstName' => $S_firstName,
                ':S_lastName' => $S_lastName,
                ':S_telephoneNumber' => $S_telephoneNumber
            ]);
        }

        $pdo->commit(); // Commit the transaction if all operations were successful

        header("Location: ../success.php?message=New+family+member+added+successfully"); // Redirect to a success page
        exit();
    } catch (PDOException $e) {
        $pdo->rollBack(); // Roll back the transaction on error
        die("Failed to add new family member: " . $e->getMessage());
    }
} else {
    header("Location: ../index.php"); // Redirect if the form is not submitted
    exit();
}
?>
