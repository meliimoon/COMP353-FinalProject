<?php
$results = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Include the database connection file
    require_once "db_files/db_connection.php";

    function populateEmailLogForPastSessions($pdo) {
        try {
            $query = "
                SELECT
                    S.sessionNum,
                    S.sessionType,
                    S.sessionStartDateTime,
                    L.locationName AS locationName,
                    LD.address AS locationAddress,
                    LD.city AS locationCity,
                    LD.province AS locationProvince,
                    LD.postalCode AS locationPostalCode,
                    T1.teamName AS teamName1,
                    T2.teamName AS teamName2,
                    P1.firstName AS headCoach1FirstName,
                    P1.lastName AS headCoach1LastName,
                    P1.emailAddress AS headCoach1Email,
                    P2.firstName AS headCoach2FirstName,
                    P2.lastName AS headCoach2LastName,
                    P2.emailAddress AS headCoach2Email
                FROM
                    Sessions S
                JOIN
                    Location L ON S.locationID = L.locationID
                JOIN
                    Found_At FA ON L.locationID = FA.locationID
                JOIN
                    LocationDetails LD ON FA.postalCode = LD.postalCode
                JOIN
                    Plays ply ON ply.sessionNum = S.sessionNum
                JOIN
                    Team T1 ON ply.teamName1 = T1.teamName
                JOIN
                    Team T2 ON ply.teamName2 = T2.teamName
                JOIN
                    Person P1 ON T1.headCoachID = P1.personID
                JOIN
                    Person P2 ON T2.headCoachID = P2.personID
                WHERE
                    S.sessionStartDateTime < CURDATE()
                ORDER BY
                    S.sessionStartDateTime;
            ";

            $stmt = $pdo->prepare($query);
            $stmt->execute();
            $sessions = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($sessions as $session) {
                $sessionNum = $session['sessionNum'];
                $sessionType = $session['sessionType'];
                $sessionStartDateTime = $session['sessionStartDateTime'];
                $locationName = $session['locationName'];
                $locationAddress = $session['locationAddress'];
                $locationCity = $session['locationCity'];
                $locationProvince = $session['locationProvince'];
                $locationPostalCode = $session['locationPostalCode'];
                $teamName1 = $session['teamName1'];
                $teamName2 = $session['teamName2'];
                $headCoach1FirstName = $session['headCoach1FirstName'];
                $headCoach1LastName = $session['headCoach1LastName'];
                $headCoach1Email = $session['headCoach1Email'];
                $headCoach2FirstName = $session['headCoach2FirstName'];
                $headCoach2LastName = $session['headCoach2LastName'];
                $headCoach2Email = $session['headCoach2Email'];

                $memberQuery = "
                    SELECT
                        CM.clubMembershipID,
                        P.firstName AS clubMemberFirstName,
                        P.lastName AS clubMemberLastName,
                        P.emailAddress AS clubMemberEmail,
                        AO.role,
                        AO.teamName
                    FROM
                        Apart_Of AO
                    JOIN
                        ClubMember CM ON AO.personID = CM.personID
                    JOIN
                        Person P ON CM.personID = P.personID
                    WHERE
                        AO.teamName IN (?, ?);
                ";
                $memberStmt = $pdo->prepare($memberQuery);
                $memberStmt->execute([$teamName1, $teamName2]);
                $members = $memberStmt->fetchAll(PDO::FETCH_ASSOC);

                foreach ($members as $member) {
                    $clubMemberFirstName = $member['clubMemberFirstName'];
                    $clubMemberLastName = $member['clubMemberLastName'];
                    $clubMemberEmail = $member['clubMemberEmail'];
                    $role = $member['role'];
                    $memberTeamName = $member['teamName'];

                    $emailSubject = "{$teamName1} vs {$teamName2} on " . date('d-M-Y h:i A', strtotime($sessionStartDateTime)) . " {$sessionType} session";
                    $emailBody = "Dear {$clubMemberFirstName} {$clubMemberLastName},\n\nYou participated in a {$sessionType} session on " . date('l, F j, Y', strtotime($sessionStartDateTime)) . " at " . date('h:i A', strtotime($sessionStartDateTime)) . ".\nLocation: {$locationAddress}, {$locationCity}, {$locationProvince}, {$locationPostalCode}\n\nYour role: {$role}\nHead Coach: ";

                    if ($memberTeamName == $teamName1) {
                        $emailBody .= "{$headCoach1FirstName} {$headCoach1LastName} (Email: {$headCoach1Email})\n";
                    } else {
                        $emailBody .= "{$headCoach2FirstName} {$headCoach2LastName} (Email: {$headCoach2Email})\n";
                    }

                    $emailBody .= "Best regards,\nYour Team Management";

                    $receiver = $clubMemberEmail;

                    $logQuery = "INSERT INTO EmailLog (sendDate, emailSubject, emailBody, sender, receiver) VALUES (NOW(), ?, ?, ?, ?)";
                    $logStmt = $pdo->prepare($logQuery);
                    $logStmt->execute([$emailSubject, substr($emailBody, 0, 100), $locationName, $receiver]);

                    // Here, you would add the logic to send the email using your preferred method.
                }
            }
        } catch (PDOException $e) {
            die("Query Failed: " . $e->getMessage());
        }
    }

    function sendWeeklySessionEmails($pdo) {
        try {
            $query = "
                SELECT
                    S.sessionNum,
                    S.sessionType,
                    S.sessionStartDateTime,
                    L.locationName AS locationName,
                    LD.address AS locationAddress,
                    LD.city AS locationCity,
                    LD.province AS locationProvince,
                    LD.postalCode AS locationPostalCode,
                    T1.teamName AS teamName1,
                    T2.teamName AS teamName2,
                    P1.firstName AS headCoach1FirstName,
                    P1.lastName AS headCoach1LastName,
                    P1.emailAddress AS headCoach1Email,
                    P2.firstName AS headCoach2FirstName,
                    P2.lastName AS headCoach2LastName,
                    P2.emailAddress AS headCoach2Email
                FROM
                    Sessions S
                JOIN
                    Location L ON S.locationID = L.locationID
                JOIN
                    Found_At FA ON L.locationID = FA.locationID
                JOIN
                    LocationDetails LD ON FA.postalCode = LD.postalCode
                JOIN
                    Plays ply ON ply.sessionNum = S.sessionNum
                JOIN
                    Team T1 ON ply.teamName1 = T1.teamName
                JOIN
                    Team T2 ON ply.teamName2 = T2.teamName
                JOIN
                    Person P1 ON T1.headCoachID = P1.personID
                JOIN
                    Person P2 ON T2.headCoachID = P2.personID
                WHERE
                    S.sessionStartDateTime BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)
                ORDER BY
                    S.sessionStartDateTime;
            ";

            $stmt = $pdo->prepare($query);
            $stmt->execute();
            $sessions = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($sessions as $session) {
                $sessionNum = $session['sessionNum'];
                $sessionType = $session['sessionType'];
                $sessionStartDateTime = $session['sessionStartDateTime'];
                $locationName = $session['locationName'];
                $locationAddress = $session['locationAddress'];
                $locationCity = $session['locationCity'];
                $locationProvince = $session['locationProvince'];
                $locationPostalCode = $session['locationPostalCode'];
                $teamName1 = $session['teamName1'];
                $teamName2 = $session['teamName2'];
                $headCoach1FirstName = $session['headCoach1FirstName'];
                $headCoach1LastName = $session['headCoach1LastName'];
                $headCoach1Email = $session['headCoach1Email'];
                $headCoach2FirstName = $session['headCoach2FirstName'];
                $headCoach2LastName = $session['headCoach2LastName'];
                $headCoach2Email = $session['headCoach2Email'];

                $memberQuery = "
                    SELECT
                        CM.clubMembershipID,
                        P.firstName AS clubMemberFirstName,
                        P.lastName AS clubMemberLastName,
                        P.emailAddress AS clubMemberEmail,
                        AO.role,
                        AO.teamName
                    FROM
                        Apart_Of AO
                    JOIN
                        ClubMember CM ON AO.personID = CM.personID
                    JOIN
                        Person P ON CM.personID = P.personID
                    WHERE
                        AO.teamName IN (?, ?);
                ";
                $memberStmt = $pdo->prepare($memberQuery);
                $memberStmt->execute([$teamName1, $teamName2]);
                $members = $memberStmt->fetchAll(PDO::FETCH_ASSOC);

                foreach ($members as $member) {
                    $clubMemberFirstName = $member['clubMemberFirstName'];
                    $clubMemberLastName = $member['clubMemberLastName'];
                    $clubMemberEmail = $member['clubMemberEmail'];
                    $role = $member['role'];
                    $memberTeamName = $member['teamName'];

                    $emailSubject = "{$teamName1} vs {$teamName2} on " . date('d-M-Y h:i A', strtotime($sessionStartDateTime)) . " {$sessionType} session";
                    $emailBody = "Dear {$clubMemberFirstName} {$clubMemberLastName},\n\nYou participated in a {$sessionType} session on " . date('l, F j, Y', strtotime($sessionStartDateTime)) . " at " . date('h:i A', strtotime($sessionStartDateTime)) . ".\nLocation: {$locationAddress}, {$locationCity}, {$locationProvince}, {$locationPostalCode}\n\nYour role: {$role}\nHead Coach: ";

                    if ($memberTeamName == $teamName1) {
                        $emailBody .= "{$headCoach1FirstName} {$headCoach1LastName} (Email: {$headCoach1Email})\n";
                    } else {
                        $emailBody .= "{$headCoach2FirstName} {$headCoach2LastName} (Email: {$headCoach2Email})\n";
                    }

                    $emailBody .= "Best regards,\nYour Team Management";

                    $receiver = $clubMemberEmail;

                    $logQuery = "INSERT INTO EmailLog (sendDate, emailSubject, emailBody, sender, receiver) VALUES (NOW(), ?, ?, ?, ?)";
                    $logStmt = $pdo->prepare($logQuery);
                    $logStmt->execute([$emailSubject, substr($emailBody, 0, 100), $locationName, $receiver]);

                    // Here, you would add the logic to send the email using your preferred method.
                }
            }
        } catch (PDOException $e) {
            die("Query Failed: " . $e->getMessage());
        }
    }

    // Populate email logs for past sessions
    populateEmailLogForPastSessions($pdo);

    // Send emails for upcoming sessions in the next week
    sendWeeklySessionEmails($pdo);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="css_Files/search2.css">
    <title>Populate and Send Session Emails</title>
</head>
<body>
    
<h3>Session Email Operations</h3>

<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
    <input type="submit" value="Execute">
</form>

<h3>Operation Status</h3>

<?php
    if ($_SERVER["REQUEST_METHOD"] == "POST" && empty($results)) {
        echo "<div>";
        echo "<p>Operations executed successfully. Check the email log for details.</p>";
        echo "</div>";
    }
?>
    
<!-- Back Button -->
<div class="button-container">
    <button onclick="window.history.back()">Back</button>
</div>

</body>
</html>
<?php
$results = [];

// Include the database connection file
require_once "db_files/db_connection.php";

try {
    // Prepare the SQL query to fetch email logs
    $query = "
        SELECT sendDate, emailSubject, emailBody, sender, receiver
        FROM EmailLog
        ORDER BY sendDate DESC;
    ";

    $stmt = $pdo->prepare($query);

    // Execute the prepared statement
    $stmt->execute();

    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Close the statement and database connection
    $stmt = null;
    $pdo = null;

} catch (PDOException $e) {
    die("Query Failed: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="css_Files/search2.css">
    <title>Check Email Log</title>
</head>
<body>
<style>
input[type="submit"] {
    background-color: #007bff;
    color: white;
    border: none;
    padding: 10px 10px;
    text-align: center;
    text-decoration: none;
    display: inline-block;
    font-size: 16px;
    margin: 4px 2px;
    cursor: pointer;
    border-radius: 8px;
    transition-duration: 0.4s;
}

input[type="submit"]:hover {
    background-color: #0056b3;
    color: white;
}

.button-container {
    text-align: center;
    margin-top: 20px;
}

form {
    text-align: center;
}
</style>
<h3>Email Log</h3>

<?php
    if(empty($results)){
        echo "<div>";
        echo "<p>No email logs found.</p>";
        echo "</div>";
    } else {
        echo "<table>";
        echo "<thead>";
        echo "<tr>
                <th>Send Date</th>
                <th>Email Subject</th>
                <th>Email Body Preview</th>
                <th>Sender</th>
                <th>Receiver</th>
              </tr>";
        echo "</thead>";
        echo "<tbody>";
        foreach ($results as $row) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row["sendDate"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["emailSubject"]) . "</td>";
            echo "<td>" . htmlspecialchars(substr($row["emailBody"], 0, 100)) . "...</td>";
            echo "<td>" . htmlspecialchars($row["sender"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["receiver"]) . "</td>";
            echo "</tr>";
        }
        echo "</tbody>";
        echo "</table>";
    }
?>

<!-- Back Button -->
<div class="button-container">
    <button onclick="window.history.back()">Back</button>
</div>

</body>
</html>
