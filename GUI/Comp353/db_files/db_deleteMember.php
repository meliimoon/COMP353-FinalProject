<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require_once "../db_files/db_connection.php"; // Ensure correct path to your DB connection file

    // Collect and sanitize input data
    $personID = intval($_POST["personnelID"]);
    $SSN = htmlspecialchars($_POST["SSN"]);

    try {
        $pdo->beginTransaction();

        // Check if the provided SSN matches the personID
        $query = "SELECT SSN FROM Person WHERE personID = :personID";
        $stmt = $pdo->prepare($query);
        $stmt->execute([':personID' => $personID]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result && $result['SSN'] === $SSN) {
            // Delete from SecondaryFamilyMember table
            $deleteSecondary = "DELETE FROM SecondaryFamilyMember WHERE primaryFamilyMemberID = :primaryFamilyMemberID";
            $stmtDeleteSecondary = $pdo->prepare($deleteSecondary);
            $stmtDeleteSecondary->execute([':primaryFamilyMemberID' => $personID]);

            // Delete from Registered_At table
            $deleteRegisteredAt = "DELETE FROM Registered_At WHERE personID = :personID";
            $stmtDeleteRegisteredAt = $pdo->prepare($deleteRegisteredAt);
            $stmtDeleteRegisteredAt->execute([':personID' => $personID]);

            // Delete from Lives_At table
            $deleteLivesAt = "DELETE FROM Lives_At WHERE personID = :personID";
            $stmtDeleteLivesAt = $pdo->prepare($deleteLivesAt);
            $stmtDeleteLivesAt->execute([':personID' => $personID]);

            // Delete from FamilyMember table
            $deleteFamilyMember = "DELETE FROM FamilyMember WHERE personID = :personID";
            $stmtDeleteFamilyMember = $pdo->prepare($deleteFamilyMember);
            $stmtDeleteFamilyMember->execute([':personID' => $personID]);

            // Delete from Person table
            $deletePerson = "DELETE FROM Person WHERE personID = :personID";
            $stmtDeletePerson = $pdo->prepare($deletePerson);
            $stmtDeletePerson->execute([':personID' => $personID]);

            $pdo->commit(); // Commit the transaction if all deletions were successful

            header("Location: ../success.php?message=Family+member+removed+successfully"); // Redirect to a success page
            exit();
        } else {
            // Rollback the transaction if SSN does not match
            $pdo->rollBack();
            die("SSN does not match the provided family member ID.");
        }
    } catch (PDOException $e) {
        $pdo->rollBack(); // Roll back the transaction on error
        die("Failed to remove family member: " . $e->getMessage());
    }
} else {
    header("Location: ../index.php"); // Redirect if the form is not submitted
    exit();
}
?>
