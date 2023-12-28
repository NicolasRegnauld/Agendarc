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

$sql = "SELECT id FROM ag_visite WHERE client_id='" . $_POST["data"] . "'";
$result = $conn->query($sql); 
if ($result->num_rows > 0)
    echo "Impossible de supprimer le client, car des visitres lui sont associées";
else {
    $sql = "DELETE FROM ag_client WHERE id = '" . $_POST["data"] . "'";


    if ($conn->query($sql) === TRUE) {
        echo "Client supprimé avec success";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();

?>