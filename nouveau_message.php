<?php
session_start();
// Connect to our database (Step 2a)
include_once 'connexion.php';

function addMessage($messageContent){
$connectDetails = getConnectionDetails();
// Create connection
$conn = new mysqli($connectDetails[0], $connectDetails[1], $connectDetails[2], $connectDetails[3]);
$conn->query('SET NAMES utf8');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

$vals = '"' . $messageContent . '","' . $_SESSION["compte"]. '"';


$sql = "INSERT INTO ag_message (msg, compte) VALUES ($vals)";

$res = array();
if ($conn->query($sql) === TRUE) {
    $res["statut"] = "success";
    $res["message"] = "nouveau message ajouté avec success " . $sql;
} else {
    $res["statut"] = "Echec";
    $res["message"] = "Le message n'a pas pu être enregistrée." . $conn->error;        
}

$conn->close();
return $res;
}

if (isset($_POST['messageContent'])){
    $messageContent = filter_input(INPUT_POST, 'messageContent', FILTER_SANITIZE_STRING);
    echo trim(json_encode(addMessage($messageContent)));
    }
 else     
    echo "missing arguments for nouveau_message";

?>
