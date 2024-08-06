<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="css_Files/location.css">
    <title>Family Members</title>
</head>
<body>
<div class="form-container">
    <div class="form-wrapper">
    <h3>Add a Family Member</h3>
<form action="db_files/db_createPrimaryMember.php" method="post">
<h4 style="color: blue; text-decoration: underline; font-style: italic;">Primary Family Members</h4>
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

    <label for="locationID">Enroll to Location ID:</label>
    <input type="number" id="locationID" name="locationID" required><br><br>

    <label for="startDate">Start Date:</label>
    <input type="date" id="startDate" name="startDate" required><br><br>

    <label for="endDate">End Date:</label>
    <input type="date" id="endDate" name="endDate">

    <h4 style="color: blue; text-decoration: underline; font-style: italic;">Secondary Family Members</h4>

    <label for="S_firstName">First Name:</label>
    <input type="text" id="S_firstName" name="S_firstName" required><br><br>

    <label for="S_lastName">Last Name:</label>
    <input type="text" id="S_lastName" name="S_lastName" required><br><br>

    <label for="S_telephoneNumber">Telephone Number:</label>
    <input type="text" id="S_telephoneNumber" name="S_telephoneNumber" required><br><br>

    <input type="submit" value="Add Member">
</form>

    </div>

    <div class="form-wrapper">
        <h3>Remove a Family Member</h3>
                <form action="db_files/db_deleteMember.php" method="post">
            <label for="personnelID">Enter Primary Family ID:</label>
            <input type="number" id="personnelID" name="personnelID" required><br><br>
            
            <label for="SSN">Confirm SSN:</label>
            <input type="text" id="SSN" name="SSN" required>
            <p style="font-size: small; color: gray;">
            *Removing Primary Family Member will also remove any associated Secondary Member and Club Member.
        </p>
            <input type="submit" value="Remove Family Member">
        </form>
    </div>

    <div class="form-wrapper">
    <h3>Edit a Family Member</h3>
<form action="db_files/db_editMember.php" method="post">
    <label for="personID">Enter Family Member ID:</label>
    <input type="number" id="personID" name="personID" required>
    
    <h4 style="color: blue; text-decoration: underline; font-style: italic;">Primary Family Members</h4>
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

    <label for="locationID">Enroll to Location ID:</label>
    <input type="number" id="locationID" name="locationID"><br><br>

    <label for="startDate">Start Date:</label>
    <input type="date" id="startDate" name="startDate"><br><br>

    <label for="endDate">End Date:</label>
    <input type="date" id="endDate" name="endDate">

    <h4 style="color: blue; text-decoration: underline; font-style: italic;">Secondary Family Members</h4>

    <label for="S_firstName">First Name:</label>
    <input type="text" id="S_firstName" name="S_firstName"><br><br>

    <label for="S_lastName">Last Name:</label>
    <input type="text" id="S_lastName" name="S_lastName"><br><br>

    <label for="S_telephoneNumber">Telephone Number:</label>
    <input type="text" id="S_telephoneNumber" name="S_telephoneNumber"><br><br>

    <input type="submit" value="Edit Member">
</form>
    </div>

    <div class="form-wrapper">
        <h3>Display a Family Member</h3>
        <form action="db_files/db_displayMember.php" method="post">
            <label for="personID">Enter Family MemberID:</label>
            <input type="number" id="personID" name="personID" required><br><br>
            <input type="submit" value="Display Family Member Details">
        </form>
    </div>
</div>
</body>
</html>
