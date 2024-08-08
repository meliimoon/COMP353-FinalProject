<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="css_Files/location.css">
    <title>Club Members</title>
</head>
<body>
<div class="form-container">
    <div class="form-wrapper">
    <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add a Club Member</title>
</head>
<body>
<h3>Add a Club Member</h3>
    <form action="db_files/db_createClubMember.php" method="post">
        <label for="firstName">First Name:</label>
        <input type="text" id="firstName" name="firstName" required><br><br>

        <label for="lastName">Last Name:</label>
        <input type="text" id="lastName" name="lastName" required><br><br>

        <label for="gender">Gender:</label><br>
        <input type="radio" id="Male" name="gender" value="Male" required>
        <label for="Male" style="display: inline;">Male</label>
        <input type="radio" id="Female" name="gender" value="Female" required>
        <label for="Female" style="display: inline;">Female</label><br><br><br>

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
        <input type="number" id="locationID" name="locationID"><br><br>
        
        <label for="startDate">Start Date:</label>
        <input type="date" id="startDate" name="startDate" required><br><br>

        <label for="endDate">End Date:</label>
        <input type="date" id="endDate" name="endDate"><br><br>

        <label for="familyMemberID">Primary Family Person ID:</label>
        <input type="number" id="familyMemberID" name="familyMemberID" required><br><br>

        <label for="primaryRelation">Primary Relation:</label>
        <select id="primaryRelation" name="primaryRelation" required>
            <option value="Father">Father</option>
            <option value="Mother">Mother</option>
            <option value="GrandFather">GrandFather</option>
            <option value="GrandMother">GrandMother</option>
            <option value="Tutor">Tutor</option>
            <option value="Partner">Partner</option>
            <option value="Friend">Friend</option>
            <option value="Other">Other</option>
        </select><br><br>

        <label for="secondaryRelation">Secondary Relation:</label>
        <select id="secondaryRelation" name="secondaryRelation">
            <option value="Father">Father</option>
            <option value="Mother">Mother</option>
            <option value="GrandFather">GrandFather</option>
            <option value="GrandMother">GrandMother</option>
            <option value="Tutor">Tutor</option>
            <option value="Partner">Partner</option>
            <option value="Friend">Friend</option>
            <option value="Other">Other</option>
        </select><br><br>

        <input type="submit" value="Add Member">
    </form>
</body>
</html>

    </div>

    <div class="form-wrapper">
        <h3>Remove a Club Member</h3>
                <form action="db_files/db_deleteClubMember.php" method="post">
            <label for="clubMembershipID">Enter Club Member ID:</label>
            <input type="number" id="clubMembershipID" name="clubMembershipID" required><br><br>
            
            <label for="firstName">Confirm First Name:</label>
            <input type="text" id="firstName" name="firstName" required>
        
            <input type="submit" value="Remove Family Member">
        </form>
    </div>

    <div class="form-wrapper">
    <h3>Edit Club Member</h3>
    <form action="db_files/db_editClubMember.php" method="post">

         <label for="clubMembershipID">Enter ClubMember ID:</label>
        <input type="number" id="clubMembershipID" name="clubMembershipID" required><br><br>

        <label for="firstName">New First Name:</label>
        <input type="text" id="firstName" name="firstName"><br><br>

        <label for="lastName">New Last Name:</label>
        <input type="text" id="lastName" name="lastName"><br><br>

        <label for="gender">New Gender:</label><br>
        <input type="radio" id="Male" name="gender" value="Male">
        <label for="Male" style="display: inline;">Male</label>
        <input type="radio" id="Female" name="gender" value="Female">
        <label for="Female" style="display: inline;">Female</label><br><br><br>

        <label for="DOB">New Date of Birth:</label>
        <input type="date" id="DOB" name="DOB"><br><br>

        <label for="SSN">New SSN:</label>
        <input type="text" id="SSN" name="SSN"><br><br>

        <label for="medicareNumber">New Medicare Number:</label>
        <input type="text" id="medicareNumber" name="medicareNumber"><br><br>

        <label for="telephoneNumber">New Telephone Number:</label>
        <input type="text" id="telephoneNumber" name="telephoneNumber"><br><br>

        <label for="address">New Address:</label>
        <input type="text" id="address" name="address"><br><br>

        <label for="city">New City:</label>
        <input type="text" id="city" name="city"><br><br>

        <label for="province">New Province:</label>
        <input type="text" id="province" name="province"><br><br>

        <label for="postalCode">New Postal Code:</label>
        <input type="text" id="postalCode" name="postalCode"><br><br>

        <label for="email">New Email:</label>
        <input type="email" id="email" name="email"><br><br>

        <input type="submit" value="Add Member">
    </form>
    </div>

    <div class="form-wrapper">
    <h3>Display a Club Member</h3>
    <form action="db_files/db_displayClubMember.php" method="post">
        <label for="clubMembershipID">Enter Club MemberID:</label>
        <input type="number" id="clubMembershipID" name="clubMembershipID" required><br><br>
        <input type="submit" value="Display Club Member Details">
    </form>
    </div>
</div>
</body>
</html>
