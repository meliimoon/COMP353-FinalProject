<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Include the database connection file
    require_once "../db_files/db_connection.php";

    // Collect POST data and sanitize it
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

    try {
        // Start a transaction
        $pdo->beginTransaction();

        // Prepare the SQL query to insert into the Person table
        $query = "INSERT INTO Person (firstName, lastName, DOB, SSN, medicareNumber, telephoneNumber, address, city, province, postalCode, email) 
                  VALUES (:firstName, :lastName, :DOB, :SSN, :medicareNumber, :telephoneNumber, :address, :city, :province, :postalCode, :email)";
        $stmt = $pdo->prepare($query);

        // Execute the prepared statement with the collected data
        $stmt->execute([
            ':firstName' => $firstName,
            ':lastName' => $lastName,
            ':DOB' => $DOB,
            ':SSN' => $SSN,
            ':medicareNumber' => $medicareNumber,
            ':telephoneNumber' => $telephoneNumber,
            ':address' => $address,
            ':city' => $city,
            ':province' => $province,
            ':postalCode' => $postalCode,
            ':email' => $email
        ]);

        // Retrieve the last inserted personID
        $personID = $pdo->lastInsertId();

        // Prepare the SQL query to insert into the Personnels table
        $query = "INSERT INTO Personnels (personID) VALUES (:personID)";
        $stmt = $pdo->prepare($query);

        // Execute the prepared statement with the personID
        $stmt->execute([':personID' => $personID]);

        // Commit the transaction
        $pdo->commit();

        // Close the statement and database connection
        $stmt = null;
        $pdo = null;

        // Redirect to the index page
        header("Location: ../index.php");
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
