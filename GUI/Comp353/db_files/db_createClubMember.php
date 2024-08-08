<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require_once "../db_files/db_connection.php"; // Ensure the correct path to your DB connection file

    // Sanitize and collect input data
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
    $locationID = htmlspecialchars($_POST['locationID']);
    $startDate = htmlspecialchars($_POST['startDate']);
    $endDate = htmlspecialchars($_POST['endDate']);
    $primaryFamilyMemberID = htmlspecialchars($_POST['familyMemberID']); // Use this variable explicitly
    $primaryRelation = htmlspecialchars($_POST['primaryRelation']);
    $secondaryRelation = htmlspecialchars($_POST['secondaryRelation']);

    try {
        $pdo->beginTransaction();

        // Insert new person into the database
        $sqlPerson = "INSERT INTO Person (firstName, lastName, dateOfBirth, SSN, medicareCardNumber, telephoneNumber, emailAddress) 
                      VALUES (:firstName, :lastName, :dob, :ssn, :medicareNumber, :telephoneNumber, :email)";
        $stmtPerson = $pdo->prepare($sqlPerson);
        $stmtPerson->execute([
            ':firstName' => $firstName,
            ':lastName' => $lastName,
            ':dob' => $dob,
            ':ssn' => $ssn,
            ':medicareNumber' => $medicareNumber,
            ':telephoneNumber' => $telephoneNumber,
            ':email' => $email
        ]);
        $personID = $pdo->lastInsertId();

        // Insert club member with gender
        $sqlClubMember = "INSERT INTO ClubMember (personID, gender) VALUES (:personID, :gender)";
        $stmtClubMember = $pdo->prepare($sqlClubMember);
        $stmtClubMember->execute([':personID' => $personID, ':gender' => $gender]);

        // Insert address details into LocationDetails
        $sqlLocationDetails = "INSERT INTO LocationDetails (postalCode, city, province, address) 
                               VALUES (:postalCode, :city, :province, :address)
                               ON DUPLICATE KEY UPDATE city=:city, province=:province, address=:address";
        $stmtLocationDetails = $pdo->prepare($sqlLocationDetails);
        $stmtLocationDetails->execute([
            ':postalCode' => $postalCode,
            ':city' => $city,
            ':province' => $province,
            ':address' => $address
        ]);

        // Link person to address in Lives_At
        $sqlLivesAt = "INSERT INTO Lives_At (personID, postalCode) VALUES (:personID, :postalCode)";
        $stmtLivesAt = $pdo->prepare($sqlLivesAt);
        $stmtLivesAt->execute([':personID' => $personID, ':postalCode' => $postalCode]);

        // Check for a secondary family member
        $sqlSecondary = "SELECT secondaryFamilyMemberID FROM SecondaryFamilyMember WHERE primaryFamilyMemberID = :primaryFamilyMemberID LIMIT 1";
        $stmtSecondary = $pdo->prepare($sqlSecondary);
        $stmtSecondary->execute([':primaryFamilyMemberID' => $primaryFamilyMemberID]);
        $secondaryFetch = $stmtSecondary->fetch(PDO::FETCH_ASSOC);
        $secondaryID = $secondaryFetch ? 1 : null;  // Set secondaryID to 1 if exists, otherwise null
        $finalSecondaryRelation = $secondaryID ? $secondaryRelation : null;

        // Insert into Assignment
        $sqlAssignment = "INSERT INTO Assignment (familyMemberID, clubMemberID, secondaryID, locationID, startDate, endDate, primaryRelation, secondaryRelation)
                          VALUES (:familyMemberID, :clubMemberID, :secondaryID, :locationID, :startDate, :endDate, :primaryRelation, :secondaryRelation)";
        $stmtAssignment = $pdo->prepare($sqlAssignment);
        $stmtAssignment->execute([
            ':familyMemberID' => $primaryFamilyMemberID, // Correctly use the primaryFamilyMemberID from the form
            ':clubMemberID' => $personID, // Use personID instead of clubMembershipID
            ':secondaryID' => $secondaryID,
            ':locationID' => $locationID,
            ':startDate' => $startDate,
            ':endDate' => $endDate,
            ':primaryRelation' => $primaryRelation,
            ':secondaryRelation' => $finalSecondaryRelation
        ]);

        $pdo->commit(); // Commit the transaction
        header("Location: ../success.php?message=New+club+member+added+successfully"); // Redirect to a success page
        exit();
    } catch (PDOException $e) {
        $pdo->rollBack(); // Roll back the transaction on error
        echo "Failed to add new club member: " . $e->getMessage();
    }
} else {
    echo "Please fill out the form to submit.";
}
?>
