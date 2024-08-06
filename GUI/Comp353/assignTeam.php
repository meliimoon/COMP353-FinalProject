<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="css_Files/location.css">
    <title>Assigning</title>
</head>
<body>
    <div class="form-container">
        <div class="form-wrapper">
        <h3>Assign a Team Formation</h3>
<form action="db_files/db_assignTeamFormation.php" method="post">
    <label for="teamName1">Team Name:</label>
    <input type="text" id="teamName1" name="teamName1" required><br><br>

    <label for="clubMembershipID">Club Membership ID:</label>
    <input type="text" id="clubMembershipID" name="clubMembershipID" required><br><br>

    <label for="role">Player Role:</label>
    <select id="playerRole" name="playerRole" required>
        <option value="Goalkeeper">Goalkeeper</option>
        <option value="Defender">Defender</option>
        <option value="Midfielder">Midfielder</option>
        <option value="Forward">Forward</option>
    </select><br><br>

    <label for="formationDateTime">Formation Time:</label>
    <input type="datetime-local" id="formationDateTime" name="formationDateTime" required><br><br>

    <input type="submit" value="Assign to Team">
</form>
</div>

        <div class="form-wrapper">
            <h3>Delete an Assignment to a Team</h3>
            <form action="db_files/db_deleteAssignTeamFormation.php" method="post">

            <label for="teamName1">Confirm Team Name:</label>
            <input type="text" id="teamName1" name="teamName1" required><br><br>

            <label for="clubMembershipID">Confirm Club Membership ID:</label>
            <input type="text" id="clubMembershipID" name="clubMembershipID" required><br><br>
                
            <input type="submit" value="Delete Location">
            </form>
        </div>

        <div class="form-wrapper">
        <h3>Edit Assigning a Team Formation</h3>
<form action="db_files/db_editAssignTeamFormation.php" method="post">
    <label for="teamName1">Enter Team Name:</label>
    <input type="text" id="teamName1" name="teamName1" required><br><br>

    <label for="clubMembershipID">Enter Club Membership ID:</label>
    <input type="text" id="clubMembershipID" name="clubMembershipID" required><br><br>

    <label for="role">Change Player Role:</label>
    <select id="playerRole" name="playerRole" required>
        <option value="Goalkeeper">Goalkeeper</option>
        <option value="Defender">Defender</option>
        <option value="Midfielder">Midfielder</option>
        <option value="Forward">Forward</option>
    </select><br><br>

    <label for="formationDateTime">Chnage Formation Time:</label>
    <input type="datetime-local" id="formationDateTime" name="formationDateTime" required><br><br>

    <input type="submit" value="Edit Assignment to a Team">
</form>
        </div>
    </div>
</body>
</html>
