<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="css_Files/location.css">
    <title>Personnel</title>
</head>
<body>
<div class="form-container">
    <div class="form-wrapper">
    <h3>Add new Personnel</h3>
<form action="db_files/db_createPersonnel.php" method="post">
    <label for="firstName">First Name:</label>
    <input type="text" id="firstName" name="firstName" required><br><br>

    <label for="lastName">Last Name:</label>
    <input type="text" id="lastName" name="lastName" required><br><br>

    <label for="DOB">Date of Birth:</label>
    <input type="date" id="DOB" name="DOB" required><br><br>

    <label for="SSN">SSN:</label>
    <input type="text" id="SSN" name="SSN" required><br><br>

    <label for="medicareNumber">Medicare Number:</label>
    <input type="text" id="medicareNumber" name="medicareNumber"><br><br>

    <label for="telephoneNumber">Telephone Number:</label>
    <input type="text" id="telephoneNumber" name="telephoneNumber"><br><br>

    <label for="address">Address:</label>
    <input type="text" id="address" name="address" required><br><br>

    <label for="city">City:</label>
    <input type="text" id="city" name="city" required><br><br>

    <label for="province">Province:</label>
    <input type="text" id="province" name="province"><br><br>

    <label for="postalCode">Postal Code:</label>
    <input type="text" id="postalCode" name="postalCode" required><br><br>

    <label for="email">Email:</label>
    <input type="email" id="email" name="email" required><br><br>

    <label for="role">Role:</label>
    <select id="role" name="role" required>
        <option value="Other">Other</option>
        <option value="Administrator">Administrator</option>
        <option value="Trainer">Trainer</option>
    </select><br><br>

    <label for="mandate">Mandate:</label>
    <select id="mandate" name="mandate" required>
        <option value="Volunteer">Volunteer</option>
        <option value="Salaried">Salaried</option>
    </select><br><br>

    <label for="locationID">Location ID:</label>
    <input type="number" id="locationID" name="locationID" required><br><br>

    <label for="startDate">Start Date:</label>
    <input type="date" id="startDate" name="startDate" required><br><br>

    <label for="endDate">End Date:</label>
    <input type="date" id="endDate" name="endDate"><br><br>

    <input type="submit" value="Add Personnel">
</form>
    </div>

    <div class="form-wrapper">
        <h3>Remove a Personnel</h3>
        
        <form action="db_files/db_deletePersonnel.php" method="post">
            <label for="personnelID">Enter Personnel ID:</label>
            <input type="number" id="personnelID" name="personnelID" required><br><br>
            
            <label for="SSN">Confirm SSN:</label>
            <input type="text" id="SSN" name="SSN" required><br><br>
            
            <input type="submit" value="Remove Personnel">
        </form>
    </div>

    <div class="form-wrapper">
    <h3>Edit a Personnel Information</h3>
<form action="db_files/db_editPersonnel.php" method="post">
    <label for="personnelID">Enter Personnel ID:</label>
    <input type="number" id="personnelID" name="personnelID" required><br><br>

    <label for="firstName">First Name:</label>
    <input type="text" id="firstName" name="firstName"><br><br>
    
    <label for="lastName">Last Name:</label>
    <input type="text" id="lastName" name="lastName"><br><br>
    
    <label for="DOB">Date of Birth:</label>
    <input type="date" id="DOB" name="DOB"><br><br>
    
    <label for="SSN">SSN:</label>
    <input type="text" id="SSN" name="SSN"><br><br>
    
    <label for="medicareNumber">Medicare Number:</label>
    <input type="text" id="medicareNumber" name="medicareNumber"><br><br>
    
    <label for="telephoneNumber">Telephone Number:</label>
    <input type="text" id="telephoneNumber" name="telephoneNumber"><br><br>
    
    <label for="address">Address:</label>
    <input type="text" id="address" name="address"><br><br>
    
    <label for="city">City:</label>
    <input type="text" id="city" name="city"><br><br>
    
    <label for="province">Province:</label>
    <input type="text" id="province" name="province"><br><br>

    <label for="postalCode">Postal Code:</label>
    <input type="text" id="postalCode" name="postalCode"><br><br>

    <label for="email">Email:</label>
    <input type="email" id="email" name="email"><br><br>

    <label for="role">Role:</label>
    <select id="role" name="role">
        <option value="Other">Other</option>
        <option value="Administrator">Administrator</option>
        <option value="Trainer">Trainer</option>
    </select><br><br>

    <label for="mandate">Mandate:</label>
    <select id="mandate" name="mandate">
        <option value="Volunteer">Volunteer</option>
        <option value="Salaried">Salaried</option>
    </select><br><br>
    <input type="submit" value="Edit Personnel">
</form>

    </div>

    <div class="form-wrapper">
        <h3>Display Personnel</h3>
        <form action="db_files/db_displayPersonnel.php" method="post">
            <label for="personID">Personnel ID:</label>
            <input type="number" id="personID" name="personID" required><br><br>
            <input type="submit" value="Display Personnel">
        </form>
    </div>
</div>
</body>
</html>
