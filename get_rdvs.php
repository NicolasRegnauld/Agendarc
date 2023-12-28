<?php
session_start();
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

$date = ($_POST["date"]);


$sql = "SELECT ag_visite.id, ag_visite.compte, ag_visite.titre, ag_visite.infirmière_id, ag_visite.date, ag_visite.heure, ag_visite.durée, ag_visite.alerte, ag_visite.alerte_message, ag_visite.tournée, ag_visite.statut_visite, ag_visite.adresse, ag_visite.notes, ag_visite.client_id, ag_client.nom, ag_client.prénom
  FROM ag_visite
  INNER JOIN ag_client on client_id = ag_client.id
  WHERE TO_DAYS(ag_visite.date) = TO_DAYS('" . $date . "') AND ag_visite.compte = '" . $_SESSION["compte"] . "'";

//  echo  "\n" . $sql . "\n";
  
  $result=mysqli_query($conn,$sql);

// Fetch all
$res = mysqli_fetch_all($result,MYSQLI_ASSOC);

// Free result set
mysqli_free_result($result);

echo json_encode($res);

$conn->close();

?>

