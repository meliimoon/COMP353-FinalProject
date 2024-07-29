```php
<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "testing";
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_entry'])) {
        $name = $_POST['name'];
        $sql = "INSERT INTO entries (name) Values ('$name')";
        if ($conn->query($sql) == TRUE) {
            echo "Entry Added";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    } elseif (isset($_POST['show_entries'])) {
        $sql = "SELECT * FROM entries";
        $results = $conn->query($sql);
        echo"<table border='1'>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                </tr>";

        while($row = $results->fetch_assoc()){
            echo "<tr>
                    <td>".$row["id"]."</td>
                    <td>".$row["name"]."</td>
                    </tr>";
        }
        echo"</table>";
    }
}
$conn->close();
?>


<!DOCTYPE html>
<html>
<body>

<h2>Add Entry</h2>
<form method="post">
  Name: <input type="text" name="name">
  <input type="submit" name="add_entry" value="Add Entry">
</form>

<h2>Show Entries</h2>
<form method="post">
  <input type="submit" name="show_entries" value="Show Entries">
</form>

</body>
</html>
```