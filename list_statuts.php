<?php
function listStatuts($selectedNom) {
 
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

  $sql = "SELECT id, libellé FROM ag_statut";
  $result = $conn->query($sql);


  $res = "";

  if ($result->num_rows > 0) {
    // output data of each row
	
    while($row = $result->fetch_assoc()) {
        if (($selectedNom != null) && ($selectedNom == $row["libellé"])){
            $res .= "<option value = \"" . $row["id"] . "\" selected=\"selected\">" . $row["libellé"] . "</option>";
        } 
        else {
            $res .= "<option value = \"" . $row["id"] . "\">" . $row["libellé"] . "</option>";
        }
    } 
  } else {
      $res = "0 results"; 
  } 
   
  $conn->close(); 
  return($res);  
}

if (isset($_POST['selected'])){
  echo listStatuts($_POST['selected']);
}
else {
  echo listStatuts(null);
}
?>
