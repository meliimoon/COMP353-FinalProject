<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Include the database connection file
    require_once "../db_files/db_connection.php";

    // Collect POST data and sanitize it
    $locationName = htmlspecialchars($_POST["name"]);
    $address = htmlspecialchars($_POST["address"]);
    $city = htmlspecialchars($_POST["city"]);
    $province = htmlspecialchars($_POST["province"]);
    $postalCode = htmlspecialchars($_POST["postalCode"]);
    $phoneNumber = htmlspecialchars($_POST["phoneNumber"]);
    $webAddress = htmlspecialchars($_POST["webAddress"]);
    $type = htmlspecialchars($_POST["type"]);
    $capacity = intval($_POST["capacity"]);

    try {
        // Start a transaction
        $pdo->beginTransaction();

        // Insert the new location into the Location table
        $insertLocation = "INSERT INTO Location (locationName, phoneNumber, webAddress, type, capacity) 
                           VALUES (:locationName, :phoneNumber, :webAddress, :type, :capacity)";
        $stmtLocation = $pdo->prepare($insertLocation);
        $stmtLocation->execute([
            ':locationName' => $locationName,
            ':phoneNumber' => $phoneNumber,
            ':webAddress' => $webAddress,
            ':type' => $type,
            ':capacity' => $capacity
        ]);

        // Get the last inserted locationID
        $locationID = $pdo->lastInsertId();

        // Insert the location details into the LocationDetails table
        $insertLocationDetails = "INSERT INTO LocationDetails (postalCode, city, province, address) 
                                  VALUES (:postalCode, :city, :province, :address)";
        $stmtLocationDetails = $pdo->prepare($insertLocationDetails);
        $stmtLocationDetails->execute([
            ':postalCode' => $postalCode,
            ':city' => $city,
            ':province' => $province,
            ':address' => $address
        ]);

        // Link the location with its details in the Found_At table
        $insertFoundAt = "INSERT INTO Found_At (locationID, postalCode) VALUES (:locationID, :postalCode)";
        $stmtFoundAt = $pdo->prepare($insertFoundAt);
        $stmtFoundAt->execute([
            ':locationID' => $locationID,
            ':postalCode' => $postalCode
        ]);

        // Commit the transaction
        $pdo->commit();

        // Close the statement and database connection
        $stmtLocation = null;
        $stmtLocationDetails = null;
        $stmtFoundAt = null;
        $pdo = null;

        // Redirect to the success page with a message
        header("Location: ../success.php?message=Location+added+successfully");
        exit();
        
    } catch (PDOException $e) {
        // Rollback the transaction if something failed
        $pdo->rollBack();
        die("Insertion Failed: " . $e->getMessage());
    }
} else {
    // Redirect to the index page if the request method is not POST
    header("Location: ../index.php");
    exit();
}
?>
