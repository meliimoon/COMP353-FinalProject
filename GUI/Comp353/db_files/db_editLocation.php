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

    try {
        // Start a transaction
        $pdo->beginTransaction();

        // Fetch the current values
        $sqlFetch = "SELECT l.locationName, l.phoneNumber, l.webAddress, l.type, l.capacity, 
                            ld.address, ld.city, ld.province, ld.postalCode
                     FROM Location l
                     LEFT JOIN Found_At fa ON l.locationID = fa.locationID
                     LEFT JOIN LocationDetails ld ON fa.postalCode = ld.postalCode
                     WHERE l.locationID = :locationID";
        $stmtFetch = $pdo->prepare($sqlFetch);
        $stmtFetch->execute([':locationID' => $locationID]);
        $currentValues = $stmtFetch->fetch(PDO::FETCH_ASSOC);

        if (!$currentValues) {
            throw new Exception("Location not found.");
        }

        // Retain original values if input is blank
        $locationName = !empty($locationName) ? $locationName : $currentValues['locationName'];
        $address = !empty($address) ? $address : $currentValues['address'];
        $city = !empty($city) ? $city : $currentValues['city'];
        $province = !empty($province) ? $province : $currentValues['province'];
        $postalCode = !empty($postalCode) ? $postalCode : $currentValues['postalCode'];
        $phoneNumber = !empty($phoneNumber) ? $phoneNumber : $currentValues['phoneNumber'];
        $webAddress = !empty($webAddress) ? $webAddress : $currentValues['webAddress'];
        $type = !empty($type) ? $type : $currentValues['type'];
        $capacity = !empty($capacity) ? $capacity : $currentValues['capacity'];

        // Check if type is 'Head' and if there is already a 'Head' in the database
        if ($type === 'Head') {
            $sqlCheckHead = "SELECT COUNT(*) FROM Location WHERE type = 'Head' AND locationID != :locationID";
            $stmtCheckHead = $pdo->prepare($sqlCheckHead);
            $stmtCheckHead->execute([':locationID' => $locationID]);
            $headCount = $stmtCheckHead->fetchColumn();

            if ($headCount > 0) {
                throw new Exception("A 'Head' location already exists. Only one 'Head' location is allowed.");
            }
        }

        // Update the Location table
        $sqlLocation = "UPDATE Location 
                        SET locationName = :locationName, phoneNumber = :phoneNumber, 
                            webAddress = :webAddress, type = :type, capacity = :capacity
                        WHERE locationID = :locationID";
        $stmtLocation = $pdo->prepare($sqlLocation);
        $stmtLocation->execute([
            ':locationName' => $locationName,
            ':phoneNumber' => $phoneNumber,
            ':webAddress' => $webAddress,
            ':type' => $type,
            ':capacity' => $capacity,
            ':locationID' => $locationID
        ]);

        // Ensure the postal code is provided for updating LocationDetails
        if (!empty($postalCode)) {
            // Check if the postal code exists in the LocationDetails table
            $sqlCheckPostalCode = "SELECT postalCode FROM LocationDetails WHERE postalCode = :postalCode";
            $stmtCheckPostalCode = $pdo->prepare($sqlCheckPostalCode);
            $stmtCheckPostalCode->execute([':postalCode' => $postalCode]);
            $postalCodeExists = $stmtCheckPostalCode->fetchColumn();

            if (!$postalCodeExists) {
                // Insert the postal code if it doesn't exist
                $insertLocationDetails = "INSERT INTO LocationDetails (postalCode, city, province, address) 
                                          VALUES (:postalCode, :city, :province, :address)";
                $stmtInsertLocationDetails = $pdo->prepare($insertLocationDetails);
                $stmtInsertLocationDetails->execute([
                    ':postalCode' => $postalCode,
                    ':city' => $city,
                    ':province' => $province,
                    ':address' => $address
                ]);
            } else {
                // Retain original values if input is blank
                $address = !empty($address) ? $address : $currentValues['address'];
                $city = !empty($city) ? $city : $currentValues['city'];
                $province = !empty($province) ? $province : $currentValues['province'];

                // Update the LocationDetails table if the postal code exists
                $sqlLocationDetails = "UPDATE LocationDetails 
                                       SET address = :address, city = :city, province = :province
                                       WHERE postalCode = :postalCode";
                $stmtLocationDetails = $pdo->prepare($sqlLocationDetails);
                $stmtLocationDetails->execute([
                    ':address' => $address,
                    ':city' => $city,
                    ':province' => $province,
                    ':postalCode' => $postalCode
                ]);
            }

            // Update the Found_At table with the new postal code
            $sqlUpdateFoundAt = "UPDATE Found_At 
                                 SET postalCode = :postalCode 
                                 WHERE locationID = :locationID";
            $stmtUpdateFoundAt = $pdo->prepare($sqlUpdateFoundAt);
            $stmtUpdateFoundAt->execute([
                ':postalCode' => $postalCode,
                ':locationID' => $locationID
            ]);
        }

        // Commit the transaction
        $pdo->commit();

        // Close the statement and database connection
        $stmtLocation = null;
        $stmtLocationDetails = null;
        $stmtUpdateFoundAt = null;
        $pdo = null;

        // Redirect to the success page with a message
        header("Location: ../success.php?message=Location+updated+successfully");
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
