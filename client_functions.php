<?php
include_once 'connexion.php';

function addClient($nom, $prenom, $adr, $tel){
$connectDetails = getConnectionDetails();
// Create connection
$conn = new mysqli($connectDetails[0], $connectDetails[1], $connectDetails[2], $connectDetails[3]);
$conn->query('SET NAMES utf8');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

$vals = '"' . $nom . '","' . $prenom . '","' .$_SESSION["compte"]. '","' . $tel . 
        '","' . $adr . '","actif"';


$sql = "INSERT INTO ag_client (nom, prénom, compte, tel_fixe, adresse, statut) 
VALUES ($vals)";

$res = "";
if ($conn->query($sql) === TRUE) {
    $res =   mysqli_insert_id($conn);
} else {
    $res = "Error: compte: " . $_SESSION["compte"]. ", msg: " . $sql . "<br>" . $conn->error;
}

$conn->close();
return $res;
}

?>