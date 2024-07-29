<?php
$dsn = "mysql:host=doc353.encs.concordia.ca;dbname=doc353_1";
$dbusername = "doc353_1";
$dbpassword = "BMTV353";

try {
    $pdo = new PDO($dsn, $dbusername, $dbpassword);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Comment out the success message in production
    // echo "Connection success";
} catch (PDOException $e) {
    die("Connection Failed: " . $e->getMessage());
}
?>
