<!DOCTYPE html>
<html lang="fr">
<body>


<?php
// Connect to our database (Step 2a)
include_once 'connexion.php';

$connectDetails = getConnectionDetails();
// Create connection
$conn = new mysqli($connectDetails[0], $connectDetails[1], $connectDetails[2], $connectDetails[3]);
$conn->query('SET NAMES utf8');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

$sql = "DELETE FROM ag_dispo_date WHERE id = '" . $_POST["expId"] . "'";


if ($conn->query($sql) === TRUE) {
    echo "Exception supprimée avec success";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();

?>

</body>
</html>
