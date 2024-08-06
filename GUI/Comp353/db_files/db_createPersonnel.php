<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require_once "../db_files/db_connection.php"; // Ensure correct path to your DB connection file

    // Sanitize and collect input data
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
    $role = htmlspecialchars($_POST["role"]);
    $mandate = htmlspecialchars($_POST["mandate"]);
    $locationID = intval($_POST["locationID"]);
    $startDate = htmlspecialchars($_POST["startDate"]);
    $endDate = htmlspecialchars($_POST["endDate"]); // Can be empty

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

        // Insert into Personnel table
        $insertPersonnel = "INSERT INTO Personnel (personID, role, mandate) VALUES (:personID, :role, :mandate)";
        $stmtPersonnel = $pdo->prepare($insertPersonnel);
        $stmtPersonnel->execute([
            ':personID' => $personID,
            ':role' => $role,
            ':mandate' => $mandate
        ]);

        // Insert into the appropriate table based on the role
        if ($role === 'Administrator') {
            $insertManages = "INSERT INTO Manages (personID, startDate, endDate, locationID) VALUES (:personID, :startDate, :endDate, :locationID)";
            $stmtManages = $pdo->prepare($insertManages);
            $stmtManages->execute([
                ':personID' => $personID,
                ':startDate' => $startDate,
                ':endDate' => (empty($endDate) ? NULL : $endDate),
                ':locationID' => $locationID
            ]);
        } else if ($role === 'Trainer' || $role === 'Other') {
            $insertOperates = "INSERT INTO Operates (personID, startDate, endDate, locationID) VALUES (:personID, :startDate, :endDate, :locationID)";
            $stmtOperates = $pdo->prepare($insertOperates);
            $stmtOperates->execute([
                ':personID' => $personID,
                ':startDate' => $startDate,
                ':endDate' => (empty($endDate) ? NULL : $endDate),
                ':locationID' => $locationID
            ]);
        }

        $pdo->commit(); // Commit the transaction if all operations were successful

        header("Location: ../success.php?message=New+personnel+added+successfully"); // Redirect to a success page
        exit();
    } catch (PDOException $e) {
        $pdo->rollBack(); // Roll back the transaction on error
        die("Failed to add new personnel: " . $e->getMessage());
    }
} else {
    header("Location: ../index.php"); // Redirect if the form is not submitted
    exit();
}
?>
