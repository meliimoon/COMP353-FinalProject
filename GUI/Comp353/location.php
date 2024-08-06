<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="css_Files/location.css">
    <title>Location</title>
</head>
<body>
    <div class="form-container">
        <div class="form-wrapper">
        <h3>Add new Location</h3>
<form action="db_files/db_createLocation.php" method="post">
    <label for="name">Name:</label>
    <input type="text" id="name" name="name" required><br><br>

    <label for="address">Address:</label>
    <input type="text" id="address" name="address" required><br><br>

    <label for="city">City:</label>
    <input type="text" id="city" name="city" required><br><br>

    <label for="province">Province:</label>
    <input type="text" id="province" name="province" required><br><br>

    <label for="postalCode">Postal Code:</label>
    <input type="text" id="postalCode" name="postalCode" required><br><br>

    <label for="phoneNumber">Phone Number:</label>
    <input type="text" id="phoneNumber" name="phoneNumber" required><br><br>

    <label for="webAddress">Web Address:</label>
    <input type="url" id="webAddress" name="webAddress" required><br><br>

    <label for="type">Type:</label>
    <select id="type" name="type" required>
        <option value="Head">Head</option>
        <option value="Branch">Branch</option>
    </select><br><br>

    <label for="capacity">Capacity:</label>
    <input type="number" id="capacity" name="capacity" required><br><br>

    <input type="submit" value="Add Location">
</form>

        </div>

        <div class="form-wrapper">
            <h3>Delete a Location</h3>
            
            <form action="db_files/db_deleteLocation.php" method="post">
            <label for="locationID">Location ID:</label>
            <input type="number" id="locationID" name="locationID" required><br><br>
                
                <label for="postalCode">Confirm Postal Code:</label>
                <input type="text" id="postalCode" name="postalCode" required><br><br>
                
                
                <input type="submit" value="Delete Location">
            </form>
        </div>

        <div class="form-wrapper">
            <h3>Edit a Location</h3>
            <form action="db_files/db_editLocation.php" method="post">
                <label for="locationID">Enter Location ID:</label>
                <input type="number" id="locationID" name="locationID" required><br><br>

                <label for="name">Edit Name:</label>
                <input type="text" id="name" name="name"><br><br>

                <label for="address">Edit Address:</label>
                <input type="text" id="address" name="address"><br><br>
                
                <label for="city">Edit City:</label>
                <input type="text" id="city" name="city"><br><br>
                
                <label for="province">Edit Province:</label>
                <input type="text" id="province" name="province"><br><br>
                
                <label for="postalCode">Edit Postal Code:</label>
                <input type="text" id="postalCode" name="postalCode"><br><br>
                
                <label for="phoneNumber">Edit Phone Number:</label>
                <input type="text" id="phoneNumber" name="phoneNumber"><br><br>
                
                <label for="webAddress">Edit Web Address:</label>
                <input type="url" id="webAddress" name="webAddress"><br><br>
                
                <label for="type">Type:</label>
    <select id="type" name="type" required>
        <option value="Head">Head</option>
        <option value="Branch">Branch</option>
    </select><br><br>
                
                <label for="capacity">Edit Capacity:</label>
                <input type="number" id="capacity" name="capacity"><br><br>
                
                <input type="submit" value="Edit Location">
            </form>
        </div>

        <div class="form-wrapper">
            <h3>Display a Location</h3>
            <form action="db_files/db_displayLocation.php" method="post">
                <label for="locationID">Location ID:</label>
                <input type="number" id="locationID" name="locationID" required><br><br>  
                <input type="submit" value="Display Location">
            </form>
        </div>
    </div>
</body>
</html>
