<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="css_Files/location.css">
    <title>Team Formation</title>
</head>
<body>
    <div class="form-container">
        <div class="form-wrapper">
        <h3>Create a Team Formation</h3>
<form action="db_files/db_createTeamFormation.php" method="post">
    <label for="teamName1">Team Name 1:</label>
    <input type="text" id="teamName1" name="teamName1" required><br><br>

    <label for="teamName2">Team Name 2:</label>
    <input type="text" id="teamName2" name="teamName2" required><br><br>

    <label for="headCoachID1">Head Coach ID for Team 1:</label>
    <input type="number" id="headCoachID1" name="headCoachID1" required><br><br>

    <label for="headCoachID2">Head Coach ID for Team 2:</label>
    <input type="number" id="headCoachID2" name="headCoachID2" required><br><br>

    <label for="gender">Team Gender:</label>
    <input type="radio" id="Male" name="gender" value="Male" required>
    <label for="Male" style="display: inline;">Male</label>
    <input type="radio" id="Female" name="gender" value="Female" required>
    <label for="Female" style="display: inline;">Female</label><br><br>

    <label for="locationID1">Location ID for Team 1:</label>
    <input type="number" id="locationID1" name="locationID1" required><br><br>

    <label for="locationID2">Location ID for Team 2:</label>
    <input type="number" id="locationID2" name="locationID2" required><br><br>

    <label for="sessionType">Session Type:</label>
    <select id="sessionType" name="sessionType" required>
        <option value="Training">Training</option>
        <option value="Game">Game</option>
    </select><br><br>

    <label for="locationID">Location ID of Session:</label>
    <input type="number" id="locationID" name="locationID" required><br><br>

    <label for="team1Score">Team 1 Score:</label>
    <input type="number" id="team1Score" name="team1Score" required><br><br>

    <label for="team2Score">Team 2 Score:</label>
    <input type="number" id="team2Score" name="team2Score" required><br><br>

    <label for="sessionStartDateTime">Session Start DateTime:</label>
    <input type="datetime-local" id="sessionStartDateTime" name="sessionStartDateTime" required><br><br>

    <input type="submit" value="Create Team Formation">
</form>

        </div>

        <div class="form-wrapper">
            <h3>Delete a Team</h3>
            <form action="db_files/db_deleteTeamFormation.php" method="post">

            <label for="teamName">Team Name:</label>
            <input type="text" id="teamName" name="teamName" required><br><br>
                
            <label for="locationID">Confirm Location ID related to the Team Name:</label>
            <input type="number" id="locationID" name="locationID" required><br><br>
                
            <input type="submit" value="Delete Location">
            </form>
        </div>

        <div class="form-wrapper">
        <h3>Edit Team Formation</h3>
<form action="db_files/db_EditTeamFormation.php" method="post">
    <label for="currentTeamName1">Enter Current Team 1 Name:</label>
    <input type="text" id="currentTeamName1" name="currentTeamName1" required><br><br>

    <label for="currentTeamName2">Enter Current Team 2 Name:</label>
    <input type="text" id="currentTeamName2" name="currentTeamName2"><br><br>

    <label for="newTeamName1">New Team 1 Name:</label>
    <input type="text" id="newTeamName1" name="newTeamName1"><br><br>

    <label for="newTeamName2">New Team 2 Name:</label>
    <input type="text" id="newTeamName2" name="newTeamName2"><br><br>

    <label for="newHeadCoachID1">New Head Coach ID for Team 1:</label>
    <input type="number" id="newHeadCoachID1" name="newHeadCoachID1"><br><br>

    <label for="newHeadCoachID2">New Head Coach ID for Team 2:</label>
    <input type="number" id="newHeadCoachID2" name="newHeadCoachID2"><br><br>

    <label for="gender">Team Gender:</label>
    <input type="radio" id="Male" name="gender" value="Male">
    <label for="Male" style="display: inline;">Male</label>
    <input type="radio" id="Female" name="gender" value="Female">
    <label for="Female" style="display: inline;">Female</label><br><br>

    <label for="newLocationID1">New Location ID for Team 1:</label>
    <input type="number" id="newLocationID1" name="newLocationID1"><br><br>

    <label for="newLocationID2">New Location ID for Team 2:</label>
    <input type="number" id="newLocationID2" name="newLocationID2"><br><br>

    <label for="sessionLocationID">Session Location ID:</label>
    <input type="number" id="sessionLocationID" name="sessionLocationID"><br><br>

    <label for="sessionType">New Session Type:</label>
    <select id="sessionType" name="sessionType">
        <option value="Training">Training</option>
        <option value="Game">Game</option>
    </select><br><br>

    <label for="newTeam1Score">New Team 1 Score:</label>
    <input type="number" id="newTeam1Score" name="newTeam1Score"><br><br>

    <label for="newTeam2Score">New Team 2 Score:</label>
    <input type="number" id="newTeam2Score" name="newTeam2Score"><br><br>

    <label for="sessionStartDateTime">New Session Start DateTime:</label>
    <input type="datetime-local" id="sessionStartDateTime" name="sessionStartDateTime"><br><br>

    <input type="submit" value="Edit Team Formation">
</form>

        </div>

        <div class="form-wrapper">
            <h3>Display a Team Formation</h3>
            <form action="db_files/db_displayTeamFormation.php" method="post">
                <label for="sessionNum">Enter Session Number:</label>
                <input type="number" id="sessionNum" name="sessionNum" required><br><br>  
                <input type="submit" value="Display Team">
            </form>
        </div>
    </div>
</body>
</html>
