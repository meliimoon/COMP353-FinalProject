<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Include the database connection file
    require_once "../db_files/db_connection.php";

    // Collect POST data and sanitize it
    $personnelID = intval($_POST["personnelID"]);
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

    // Initialize the SQL query parts
    $setClause = [];
    $params = [];

    // Construct the SET clause dynamically based on provided fields
    if (!empty($firstName)) {
        $setClause[] = "firstName = :firstName";
        $params[':firstName'] = $firstName;
    }
    if (!empty($lastName)) {
        $setClause[] = "lastName = :lastName";
        $params[':lastName'] = $lastName;
    }
    if (!empty($DOB)) {
        $setClause[] = "dateOfBirth = :DOB";
        $params[':DOB'] = $DOB;
    }
    if (!empty($SSN)) {
        $setClause[] = "SSN = :SSN";
        $params[':SSN'] = $SSN;
    }
    if (!empty($medicareNumber)) {
        $setClause[] = "medicareCardNumber = :medicareNumber";
        $params[':medicareNumber'] = $medicareNumber;
    }
    if (!empty($telephoneNumber)) {
        $setClause[] = "telephoneNumber = :telephoneNumber";
        $params[':telephoneNumber'] = $telephoneNumber;
    }
    if (!empty($address)) {
        $setClause[] = "address = :address";
        $params[':address'] = $address;
    }
    if (!empty($city)) {
        $setClause[] = "city = :city";
        $params[':city'] = $city;
    }
    if (!empty($province)) {
        $setClause[] = "province = :province";
        $params[':province'] = $province;
    }
    if (!empty($postalCode)) {
        $setClause[] = "postalCode = :postalCode";
        $params[':postalCode'] = $postalCode;
    }
    if (!empty($email)) {
        $setClause[] = "emailAddress = :email";
        $params[':email'] = $email;
    }
    if (!empty($role)) {
        $setClause[] = "role = :role";
        $params[':role'] = $role;
    }
    if (!empty($mandate)) {
        $setClause[] = "mandate = :mandate";
        $params[':mandate'] = $mandate;
    }

    // Ensure at least one field is provided
    if (empty($setClause)) {
        die("No fields to update.");
    }

    try {
        // Start a transaction
        $pdo->beginTransaction();

        // Update the Personnel table
        $sql = "UPDATE Personnel SET " . implode(", ", $setClause) . " WHERE personID = :personnelID";
        $params[':personnelID'] = $personnelID;
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        // Retrieve the current locationID for the personnel
        $sql = "SELECT locationID FROM Operates WHERE personID = :personnelID ORDER BY startDate DESC LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':personnelID' => $personnelID]);
        $locationID = $stmt->fetchColumn();

        // Retrieve the current locationID for the personnel from Manages table if not found in Operates
        if (!$locationID) {
            $sql = "SELECT locationID FROM Manages WHERE personID = :personnelID ORDER BY startDate DESC LIMIT 1";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':personnelID' => $personnelID]);
            $locationID = $stmt->fetchColumn();
        }

        // Check the role and update the respective tables accordingly
        if (!empty($role)) {
            $currentDate = date('Y-m-d');
            if ($role == 'Administrator') {
                // Update Operates table endDate
                $sql = "UPDATE Operates SET endDate = :currentDate WHERE personID = :personnelID AND endDate IS NULL";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([':currentDate' => $currentDate, ':personnelID' => $personnelID]);

                // Insert or update Manages table
                $sql = "INSERT INTO Manages (personID, startDate, locationID) VALUES (:personnelID, :currentDate, :locationID)
                        ON DUPLICATE KEY UPDATE endDate = NULL";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([':currentDate' => $currentDate, ':personnelID' => $personnelID, ':locationID' => $locationID]);
            } else {
                // Update Manages table endDate
                $sql = "UPDATE Manages SET endDate = :currentDate WHERE personID = :personnelID AND endDate IS NULL";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([':currentDate' => $currentDate, ':personnelID' => $personnelID]);

                // Insert or update Operates table
                $sql = "INSERT INTO Operates (personID, startDate, locationID) VALUES (:personnelID, :currentDate, :locationID)
                        ON DUPLICATE KEY UPDATE endDate = NULL";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([':currentDate' => $currentDate, ':personnelID' => $personnelID, ':locationID' => $locationID]);
            }
        }

        // Commit the transaction
        $pdo->commit();

        // Close the statement and database connection
        $stmt = null;
        $pdo = null;

        // Redirect to the success page with a message
        header("Location: ../success.php?message=Personnel+updated+successfully");
        exit();

    } catch (PDOException $e) {
        // Rollback the transaction if something failed
        $pdo->rollBack();
        die("Update Failed: " . $e->getMessage());
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
