<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require_once "../db_files/db_connection.php"; // Ensure correct path to your DB connection file

    $personID = intval($_POST["personID"]);

    // Collect input data for primary family member
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

    // Collect input data for secondary family member
    $S_firstName = htmlspecialchars($_POST["S_firstName"]);
    $S_lastName = htmlspecialchars($_POST["S_lastName"]);
    $S_telephoneNumber = htmlspecialchars($_POST["S_telephoneNumber"]);

    try {
        $pdo->beginTransaction();

        // Fetch the current values from the database for the primary family member
        $query = "
            SELECT p.*, ld.*, ra.locationID AS currentLocationID, ra.startDate AS currentStartDate, ra.endDate AS currentEndDate
            FROM Person p
            LEFT JOIN Lives_At la ON p.personID = la.personID
            LEFT JOIN LocationDetails ld ON la.postalCode = ld.postalCode
            LEFT JOIN Registered_At ra ON p.personID = ra.personID
            WHERE p.personID = :personID
            ORDER BY ra.startDate DESC
            LIMIT 1";
        $stmt = $pdo->prepare($query);
        $stmt->execute([':personID' => $personID]);
        $current = $stmt->fetch(PDO::FETCH_ASSOC);

        // Fetch the current values for the secondary family member
        $querySecondary = "
            SELECT * 
            FROM SecondaryFamilyMember
            WHERE primaryFamilyMemberID = :primaryFamilyMemberID AND secondaryFamilyMemberID = 1";
        $stmtSecondary = $pdo->prepare($querySecondary);
        $stmtSecondary->execute([':primaryFamilyMemberID' => $personID]);
        $currentSecondary = $stmtSecondary->fetch(PDO::FETCH_ASSOC);

        // If any input is empty, retain the old value
        $firstName = !empty($firstName) ? $firstName : $current['firstName'];
        $lastName = !empty($lastName) ? $lastName : $current['lastName'];
        $DOB = !empty($DOB) ? $DOB : $current['dateOfBirth'];
        $SSN = !empty($SSN) ? $SSN : $current['SSN'];
        $medicareNumber = !empty($medicareNumber) ? $medicareNumber : $current['medicareCardNumber'];
        $telephoneNumber = !empty($telephoneNumber) ? $telephoneNumber : $current['telephoneNumber'];
        $address = !empty($address) ? $address : $current['address'];
        $city = !empty($city) ? $city : $current['city'];
        $province = !empty($province) ? $province : $current['province'];
        $postalCode = !empty($postalCode) ? $postalCode : $current['postalCode'];
        $email = !empty($email) ? $email : $current['emailAddress'];
        $locationID = !empty($locationID) ? $locationID : $current['currentLocationID'];
        $startDate = !empty($startDate) ? $startDate : $current['currentStartDate'];
        $endDate = !empty($endDate) ? $endDate : $current['currentEndDate'];

        // Update Person table only if primary family member details are provided
        if (!empty($firstName) || !empty($lastName) || !empty($DOB) || !empty($SSN) || !empty($medicareNumber) || !empty($telephoneNumber) || !empty($address) || !empty($city) || !empty($province) || !empty($postalCode) || !empty($email)) {
            $updatePerson = "UPDATE Person SET SSN = :SSN, firstName = :firstName, lastName = :lastName, medicareCardNumber = :medicareNumber, 
                             dateOfBirth = :DOB, telephoneNumber = :telephoneNumber, emailAddress = :email WHERE personID = :personID";
            $stmtPerson = $pdo->prepare($updatePerson);
            $stmtPerson->execute([
                ':SSN' => $SSN,
                ':firstName' => $firstName,
                ':lastName' => $lastName,
                ':medicareNumber' => $medicareNumber,
                ':DOB' => $DOB,
                ':telephoneNumber' => $telephoneNumber,
                ':email' => $email,
                ':personID' => $personID
            ]);

            // Update Lives_At and LocationDetails tables
            $updateLocationDetails = "UPDATE LocationDetails SET address = :address, city = :city, province = :province, postalCode = :postalCode 
                                      WHERE postalCode = :currentPostalCode";
            $stmtLocationDetails = $pdo->prepare($updateLocationDetails);
            $stmtLocationDetails->execute([
                ':address' => $address,
                ':city' => $city,
                ':province' => $province,
                ':postalCode' => $postalCode,
                ':currentPostalCode' => $current['postalCode']
            ]);

            $updateLivesAt = "UPDATE Lives_At SET postalCode = :postalCode WHERE personID = :personID";
            $stmtLivesAt = $pdo->prepare($updateLivesAt);
            $stmtLivesAt->execute([
                ':postalCode' => $postalCode,
                ':personID' => $personID
            ]);
        }

        // Find the latest registration entry
        $queryLatestRegistration = "
            SELECT startDate 
            FROM Registered_At 
            WHERE personID = :personID 
            ORDER BY startDate DESC 
            LIMIT 1";
        $stmtLatestRegistration = $pdo->prepare($queryLatestRegistration);
        $stmtLatestRegistration->execute([':personID' => $personID]);
        $latestRegistration = $stmtLatestRegistration->fetch(PDO::FETCH_ASSOC);

        // Update Registered_At table if locationID or dates are provided
        if ($latestRegistration && (!empty($locationID) || !empty($startDate) || !empty($endDate))) {
            $updateRegisteredAt = "UPDATE Registered_At 
                                   SET startDate = IFNULL(NULLIF(:startDate, ''), startDate), 
                                       endDate = IFNULL(NULLIF(:endDate, ''), endDate), 
                                       locationID = IFNULL(NULLIF(:locationID, 0), locationID)
                                   WHERE personID = :personID 
                                   AND startDate = :latestStartDate";
            $stmtRegisteredAt = $pdo->prepare($updateRegisteredAt);
            $stmtRegisteredAt->execute([
                ':startDate' => $startDate,
                ':endDate' => (empty($endDate) ? NULL : $endDate),
                ':locationID' => $locationID,
                ':personID' => $personID,
                ':latestStartDate' => $latestRegistration['startDate']
            ]);
        }

        // Check if secondary family member details are provided
        if (!empty($S_firstName) && !empty($S_lastName) && !empty($S_telephoneNumber)) {
            if ($currentSecondary) {
                // Update SecondaryFamilyMember table
                $updateSecondaryFamilyMember = "UPDATE SecondaryFamilyMember SET firstName = :S_firstName, lastName = :S_lastName, telephoneNumber = :S_telephoneNumber
                                                WHERE primaryFamilyMemberID = :primaryFamilyMemberID AND secondaryFamilyMemberID = 1";
                $stmtSecondaryFamilyMember = $pdo->prepare($updateSecondaryFamilyMember);
                $stmtSecondaryFamilyMember->execute([
                    ':S_firstName' => $S_firstName,
                    ':S_lastName' => $S_lastName,
                    ':S_telephoneNumber' => $S_telephoneNumber,
                    ':primaryFamilyMemberID' => $personID
                ]);
            } else {
                // Insert into SecondaryFamilyMember table
                $insertSecondaryFamilyMember = "INSERT INTO SecondaryFamilyMember (primaryFamilyMemberID, secondaryFamilyMemberID, firstName, lastName, telephoneNumber)
                                                VALUES (:primaryFamilyMemberID, 1, :S_firstName, :S_lastName, :S_telephoneNumber)";
                $stmtInsertSecondaryFamilyMember = $pdo->prepare($insertSecondaryFamilyMember);
                $stmtInsertSecondaryFamilyMember->execute([
                    ':primaryFamilyMemberID' => $personID,
                    ':S_firstName' => $S_firstName,
                    ':S_lastName' => $S_lastName,
                    ':S_telephoneNumber' => $S_telephoneNumber
                ]);
            }
        }

        $pdo->commit(); // Commit the transaction if all operations were successful

        header("Location: ../success.php?message=Family+member+updated+successfully"); // Redirect to a success page
        exit();
    } catch (PDOException $e) {
        $pdo->rollBack(); // Roll back the transaction on error
        die("Failed to update family member: " . $e->getMessage());
    }
} else {
    header("Location: ../index.php"); // Redirect if the form is not submitted
    exit();
}
?>
