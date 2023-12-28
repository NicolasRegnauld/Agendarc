<?php
session_start();
function listNomIdInfirmières() {
 
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

  $sql = "SELECT id, nom FROM ag_infirmière where compte = '" . $_SESSION["compte"] . "' AND statut = 'actif'";
  $result = $conn->query($sql);

  $res['999'] = "Non affecté";
  if ($result->num_rows > 0) {
    // output data of each row
	
	while($row = $result->fetch_assoc()) {
		
		  $res[$row["id"]] = $row["nom"];
		
	}
	
  } 

  $conn->close();
  return json_encode($res);  
}

echo listNomIdInfirmières();
?>