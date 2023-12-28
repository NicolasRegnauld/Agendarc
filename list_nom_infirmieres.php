<?php
session_start();

function listNomInfirmières($selectedNom) {
 
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

  $sql = "SELECT id, nom FROM ag_infirmière where compte = '" . $_SESSION["compte"] . "'";
  $result = $conn->query($sql);

  if ($result->num_rows > 0) {
    // output data of each row
	
     $res = "<option value = \"" . "999" . "\">" . NULL . "</option>";
	while($row = $result->fetch_assoc()) {
		if (($selectedNom != null) && ($selectedNom == $row["nom"])){
		  $res .= "<option value = \"" . $row["id"] . "\" selected=\"selected\">" . $row["nom"] . "</option>";
		} 
		else {
          $res .= "<option value = \"" . $row["id"] . "\">" . $row["nom"] . "</option>";
		}
	}
        $conn->close(); 
	return($res);
  } else {
      echo "0 results"; 
  } 

  $conn->close(); 
}
if (isset($_POST['preselect'])){
  echo listNomInfirmières($_POST['preselect']);
}
else{
  echo listNomInfirmières(null);
}
?>
