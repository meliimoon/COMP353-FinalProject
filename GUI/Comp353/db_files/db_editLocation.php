<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Include the database connection file
    require_once "../db_files/db_connection.php";

    // Collect POST data and sanitize it
    $locationID = intval($_POST["locationID"]);
    $locationName = htmlspecialchars($_POST["name"]);
    $address = htmlspecialchars($_POST["address"]);
    $city = htmlspecialchars($_POST["city"]);
    $province = htmlspecialchars($_POST["province"]);
    $postalCode = htmlspecialchars($_POST["postalCode"]);
    $phoneNumber = htmlspecialchars($_POST["phoneNumber"]);
    $webAddress = htmlspecialchars($_POST["webAddress"]);
    $type = htmlspecialchars($_POST["type"]);
    $capacity = intval($_POST["capacity"]);

    // Initialize the SQL query parts
    $setClause = [];
    $params = [];

    // Construct the SET clause dynamically based on provided fields
    if (!empty($locationName)) {
        $setClause[] = "locationName = :locationName";
        $params[':locationName'] = $locationName;
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
    if (!empty($phoneNumber)) {
        $setClause[] = "phoneNumber = :phoneNumber";
        $params[':phoneNumber'] = $phoneNumber;
    }
    if (!empty($webAddress)) {
        $setClause[] = "webAddress = :webAddress";
        $params[':webAddress'] = $webAddress;
    }
    if (!empty($type)) {
        $setClause[] = "type = :type";
        $params[':type'] = $type;
    }
    if (!empty($capacity)) {
        $setClause[] = "capacity = :capacity";
        $params[':capacity'] = $capacity;
    }

    // Ensure at least one field is provided
    if (empty($setClause)) {
        die("No fields to update.");
    }

    // Create the SQL query
    $sql = "UPDATE Location SET " . implode(", ", $setClause) . " WHERE locationID = :locationID";
    $params[':locationID'] = $locationID;

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
            header("Location: ../success.php?message=Location+updated+successfully");
            exit();
        } else {
            // No rows affected, meaning no matching record was found or no changes were made
            throw new Exception("No matching location found or no changes made.");
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
