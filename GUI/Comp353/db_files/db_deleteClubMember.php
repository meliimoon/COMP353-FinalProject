<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require_once "../db_files/db_connection.php"; // Ensure the correct path to your DB connection file

    // Collect POST data and sanitize it
    $clubMembershipID = htmlspecialchars($_POST['clubMembershipID']);
    $firstName = htmlspecialchars($_POST['firstName']);

    try {
        $pdo->beginTransaction();

        // Fetch the club member data
        $fetchQuery = "
            SELECT 
                p.personID, p.firstName
            FROM 
                ClubMember cm
            JOIN 
                Person p ON cm.personID = p.personID
            WHERE 
                cm.clubMembershipID = :clubMembershipID";
        $stmtFetch = $pdo->prepare($fetchQuery);
        $stmtFetch->execute([':clubMembershipID' => $clubMembershipID]);
        $currentData = $stmtFetch->fetch(PDO::FETCH_ASSOC);

        if (!$currentData) {
            throw new PDOException("Club member not found");
        }

        // Check if the first name matches
        if ($currentData['firstName'] !== $firstName) {
            throw new PDOException("First name does not match");
        }

        // Remove the family member
        $deleteQuery = "
            DELETE FROM Person 
            WHERE personID = :personID";
        $stmtDelete = $pdo->prepare($deleteQuery);
        $stmtDelete->execute([':personID' => $currentData['personID']]);

        $pdo->commit(); // Commit the transaction
        header("Location: ../success.php?message=Family+member+removed+successfully"); // Redirect to a success page
        exit();
    } catch (PDOException $e) {
        $pdo->rollBack(); // Roll back the transaction on error
        echo "Failed to remove family member: " . $e->getMessage();
    }
} else {
    echo "Please fill out the form to submit.";
}
?>
