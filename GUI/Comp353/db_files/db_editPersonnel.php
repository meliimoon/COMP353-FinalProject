<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Include the database connection file
    require_once "../db_files/db_connection.php";

    // Collect POST data and sanitize it
    $personnelID = intval($_POST["personnelID"]);
    $firstName = htmlspecialchars($_POST["firstName"]);
    $lastName = htmlspecialchars($_POST["lastName"]);
    $dateOfBirth = htmlspecialchars($_POST["DOB"]);
    $SSN = htmlspecialchars($_POST["SSN"]);
    $medicareNumber = htmlspecialchars($_POST["medicareNumber"]);
    $telephoneNumber = htmlspecialchars($_POST["telephoneNumber"]);
    $address = htmlspecialchars($_POST["address"]);
    $city = htmlspecialchars($_POST["city"]);
    $province = htmlspecialchars($_POST["province"]);
    $postalCode = htmlspecialchars($_POST["postalCode"]);
    $email = htmlspecialchars($_POST["email"]);

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
    if (!empty($dateOfBirth)) {
        $setClause[] = "dateOfBirth = :dateOfBirth";
        $params[':dateOfBirth'] = $dateOfBirth;
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

    // Ensure at least one field is provided
    if (empty($setClause)) {
        die("No fields to update.");
    }

    // Create the SQL query
    $sql = "UPDATE Person SET " . implode(", ", $setClause) . " WHERE personID = :personnelID";
    $params[':personnelID'] = $personnelID;

    try {
        // Start a transaction
        $pdo->beginTransaction();

        // Prepare and execute the update statement
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        // Check if any rows were affected
        if ($stmt->rowCount() > 0) {
            // Commit the transaction
            $pdo->commit();

            // Close the statement and database connection
            $stmt = null;
            $pdo = null;

            // Redirect to the success page with a message
            header("Location: ../success.php?message=Personnel+updated+successfully");
            exit();
        } else {
            // No rows affected, meaning no matching record was found or no changes were made
            throw new Exception("No matching personnel found or no changes made.");
        }

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
