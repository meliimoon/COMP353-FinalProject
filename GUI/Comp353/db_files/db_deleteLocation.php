<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Include the database connection file
    require_once "../db_files/db_connection.php";

    // Collect POST data and sanitize it
    $locationID = intval($_POST["locationID"]);
    $postalCode = htmlspecialchars($_POST["postalCode"]);

    try {
        // Start a transaction
        $pdo->beginTransaction();

        // Ensure the postal code matches the location ID
        $sqlCheck = "SELECT fa.postalCode
                     FROM Location l
                     JOIN Found_At fa ON l.locationID = fa.locationID
                     WHERE l.locationID = :locationID";
        $stmtCheck = $pdo->prepare($sqlCheck);
        $stmtCheck->execute([':locationID' => $locationID]);
        $currentPostalCode = $stmtCheck->fetchColumn();

        if ($currentPostalCode !== $postalCode) {
            throw new Exception("Postal code does not match the location.");
        }

        // Delete the `Location` record (this will also delete related records in Found_At due to ON DELETE CASCADE)
        $deleteLocation = "DELETE FROM Location WHERE locationID = :locationID";
        $stmtLocation = $pdo->prepare($deleteLocation);
        $stmtLocation->execute([':locationID' => $locationID]);

        // Check if any rows were affected
        if ($stmtLocation->rowCount() > 0) {
            // Check if the postal code is still referenced by any other location
            $sqlCheckPostalCodeUsage = "SELECT COUNT(*) FROM Found_At WHERE postalCode = :postalCode";
            $stmtCheckPostalCodeUsage = $pdo->prepare($sqlCheckPostalCodeUsage);
            $stmtCheckPostalCodeUsage->execute([':postalCode' => $postalCode]);
            $postalCodeUsageCount = $stmtCheckPostalCodeUsage->fetchColumn();

            if ($postalCodeUsageCount == 0) {
                // If no other location is using this postal code, delete it from LocationDetails
                $deleteLocationDetails = "DELETE FROM LocationDetails WHERE postalCode = :postalCode";
                $stmtLocationDetails = $pdo->prepare($deleteLocationDetails);
                $stmtLocationDetails->execute([':postalCode' => $postalCode]);
            }

            // Commit the transaction
            $pdo->commit();

            // Close the statement and database connection
            $stmtLocation = null;
            $stmtLocationDetails = null;
            $pdo = null;

            // Redirect to the success page with a message
            header("Location: ../success.php?message=Location+deleted+successfully");
            exit();
        } else {
            // No rows affected, meaning no matching record was found
            throw new Exception("No matching location found.");
        }

    } catch (PDOException $e) {
        // Rollback the transaction if something failed
        $pdo->rollBack();
        die("Deletion Failed: " . $e->getMessage());
    } catch (Exception $e) {
        // Handle other exceptions
        die("Error: " . $e->getMessage());
    }
} else {
    // Redirect to the index page if the request method is not POST
    header("Location: ../index.php");
    exit();
}
?>
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Include the database connection file
    require_once "../db_files/db_connection.php";

    // Collect POST data and sanitize it
    $locationID = intval($_POST["locationID"]);
    $postalCode = htmlspecialchars($_POST["postalCode"]);

    try {
        // Start a transaction
        $pdo->beginTransaction();

        // Ensure the postal code matches the location ID
        $sqlCheck = "SELECT fa.postalCode
                     FROM Location l
                     JOIN Found_At fa ON l.locationID = fa.locationID
                     WHERE l.locationID = :locationID";
        $stmtCheck = $pdo->prepare($sqlCheck);
        $stmtCheck->execute([':locationID' => $locationID]);
        $currentPostalCode = $stmtCheck->fetchColumn();

        if ($currentPostalCode !== $postalCode) {
            throw new Exception("Postal code does not match the location.");
        }

        // Delete the `Location` record (this will also delete related records in Found_At due to ON DELETE CASCADE)
        $deleteLocation = "DELETE FROM Location WHERE locationID = :locationID";
        $stmtLocation = $pdo->prepare($deleteLocation);
        $stmtLocation->execute([':locationID' => $locationID]);

        // Check if any rows were affected
        if ($stmtLocation->rowCount() > 0) {
            // Check if the postal code is still referenced by any other location
            $sqlCheckPostalCodeUsage = "SELECT COUNT(*) FROM Found_At WHERE postalCode = :postalCode";
            $stmtCheckPostalCodeUsage = $pdo->prepare($sqlCheckPostalCodeUsage);
            $stmtCheckPostalCodeUsage->execute([':postalCode' => $postalCode]);
            $postalCodeUsageCount = $stmtCheckPostalCodeUsage->fetchColumn();

            if ($postalCodeUsageCount == 0) {
                // If no other location is using this postal code, delete it from LocationDetails
                $deleteLocationDetails = "DELETE FROM LocationDetails WHERE postalCode = :postalCode";
                $stmtLocationDetails = $pdo->prepare($deleteLocationDetails);
                $stmtLocationDetails->execute([':postalCode' => $postalCode]);
            }

            // Commit the transaction
            $pdo->commit();

            // Close the statement and database connection
            $stmtLocation = null;
            $stmtLocationDetails = null;
            $pdo = null;

            // Redirect to the success page with a message
            header("Location: ../success.php?message=Location+deleted+successfully");
            exit();
        } else {
            // No rows affected, meaning no matching record was found
            throw new Exception("No matching location found.");
        }

    } catch (PDOException $e) {
        // Rollback the transaction if something failed
        $pdo->rollBack();
        die("Deletion Failed: " . $e->getMessage());
    } catch (Exception $e) {
        // Handle other exceptions
        die("Error: " . $e->getMessage());
    }
} else {
    // Redirect to the index page if the request method is not POST
    header("Location: ../index.php");
    exit();
}
?>
