<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require_once "../db_files/db_connection.php"; // Ensure the correct path to your DB connection file

    // Collect POST data and sanitize it
    $clubMembershipID = htmlspecialchars($_POST['clubMembershipID']);
    $firstName = htmlspecialchars($_POST['firstName']);
    $lastName = htmlspecialchars($_POST['lastName']);
    $gender = htmlspecialchars($_POST['gender']);
    $dob = htmlspecialchars($_POST['DOB']);
    $ssn = htmlspecialchars($_POST['SSN']);
    $medicareNumber = htmlspecialchars($_POST['medicareNumber']);
    $telephoneNumber = htmlspecialchars($_POST['telephoneNumber']);
    $email = htmlspecialchars($_POST['email']);
    $address = htmlspecialchars($_POST['address']);
    $city = htmlspecialchars($_POST['city']);
    $province = htmlspecialchars($_POST['province']);
    $postalCode = htmlspecialchars($_POST['postalCode']);

    try {
        $pdo->beginTransaction();

        // Fetch current data of the club member
        $fetchQuery = "
            SELECT 
                p.*, ld.address, ld.city, ld.province, ld.postalCode, cm.gender, cm.personID
            FROM 
                ClubMember cm
            JOIN 
                Person p ON cm.personID = p.personID
            LEFT JOIN 
                Lives_At la ON p.personID = la.personID
            LEFT JOIN 
                LocationDetails ld ON la.postalCode = ld.postalCode
            WHERE 
                cm.clubMembershipID = :clubMembershipID";
        $stmtFetch = $pdo->prepare($fetchQuery);
        $stmtFetch->execute([':clubMembershipID' => $clubMembershipID]);
        $currentData = $stmtFetch->fetch(PDO::FETCH_ASSOC);

        if (!$currentData) {
            throw new PDOException("Club member not found");
        }

        // Use existing data if input is empty
        $firstName = !empty($firstName) ? $firstName : $currentData['firstName'];
        $lastName = !empty($lastName) ? $lastName : $currentData['lastName'];
        $gender = !empty($gender) ? $gender : $currentData['gender'];
        $dob = !empty($dob) ? $dob : $currentData['dateOfBirth'];
        $ssn = !empty($ssn) ? $ssn : $currentData['SSN'];
        $medicareNumber = !empty($medicareNumber) ? $medicareNumber : $currentData['medicareCardNumber'];
        $telephoneNumber = !empty($telephoneNumber) ? $telephoneNumber : $currentData['telephoneNumber'];
        $email = !empty($email) ? $email : $currentData['emailAddress'];
        $address = !empty($address) ? $address : $currentData['address'];
        $city = !empty($city) ? $city : $currentData['city'];
        $province = !empty($province) ? $province : $currentData['province'];
        $postalCode = !empty($postalCode) ? $postalCode : $currentData['postalCode'];

        // Update person details
        $updatePerson = "
            UPDATE Person 
            SET firstName = :firstName, lastName = :lastName, dateOfBirth = :dob, SSN = :ssn, 
                medicareCardNumber = :medicareNumber, telephoneNumber = :telephoneNumber, emailAddress = :email
            WHERE personID = :personID";
        $stmtUpdatePerson = $pdo->prepare($updatePerson);
        $stmtUpdatePerson->execute([
            ':firstName' => $firstName,
            ':lastName' => $lastName,
            ':dob' => $dob,
            ':ssn' => $ssn,
            ':medicareNumber' => $medicareNumber,
            ':telephoneNumber' => $telephoneNumber,
            ':email' => $email,
            ':personID' => $currentData['personID']
        ]);

        // Update club member gender
        $updateClubMember = "
            UPDATE ClubMember 
            SET gender = :gender 
            WHERE clubMembershipID = :clubMembershipID";
        $stmtUpdateClubMember = $pdo->prepare($updateClubMember);
        $stmtUpdateClubMember->execute([
            ':gender' => $gender,
            ':clubMembershipID' => $clubMembershipID
        ]);

        // Update address details in LocationDetails
        $updateLocationDetails = "
            INSERT INTO LocationDetails (postalCode, city, province, address) 
            VALUES (:postalCode, :city, :province, :address)
            ON DUPLICATE KEY UPDATE city = :city, province = :province, address = :address";
        $stmtUpdateLocationDetails = $pdo->prepare($updateLocationDetails);
        $stmtUpdateLocationDetails->execute([
            ':postalCode' => $postalCode,
            ':city' => $city,
            ':province' => $province,
            ':address' => $address
        ]);

        // Link person to address in Lives_At
        $updateLivesAt = "
            INSERT INTO Lives_At (personID, postalCode) 
            VALUES (:personID, :postalCode)
            ON DUPLICATE KEY UPDATE postalCode = :postalCode";
        $stmtUpdateLivesAt = $pdo->prepare($updateLivesAt);
        $stmtUpdateLivesAt->execute([
            ':personID' => $currentData['personID'],
            ':postalCode' => $postalCode
        ]);

        $pdo->commit(); // Commit the transaction
        header("Location: ../success.php?message=Club+member+updated+successfully"); // Redirect to a success page
        exit();
    } catch (PDOException $e) {
        $pdo->rollBack(); // Roll back the transaction on error
        echo "Failed to update club member: " . $e->getMessage();
    }
} else {
    echo "Please fill out the form to submit.";
}
?>
