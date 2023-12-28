<?php
session_start();
function listNomClients() {
 
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

  $sql = "SELECT id, nom FROM ag_client where compte = '" . $_SESSION["compte"] . "'";
  $result = $conn->query($sql);



  if ($result->num_rows > 0) {
    // output data of each row
	
    $res = "";
	while($row = $result->fetch_assoc()) {

          $res .= "<option value = \"" . $row["id"] . "\">" . $row["nom"] . "</option>";
		
	} 

        $conn->close(); 
	return $res;
  } else {
      $conn->close(); 
      echo "0 results"; 
  } 

  
}
if (isset($_POST['action'])){
  echo listNomClients();
}
?>
