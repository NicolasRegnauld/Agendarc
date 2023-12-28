<?php
session_start();
function listTypes($selectedType) {
 
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

  $sql = "SELECT DISTINCT tournée FROM ag_tournée  where compte = '" . $_SESSION["compte"] . "'";
  $result = $conn->query($sql);

  if ($result->num_rows > 0) {
    // output data of each row
	
    $res = "";
    while($row = $result->fetch_assoc()) {
        if (($selectedType != null) && ($selectedType == $row["tournée"])){
            $res .= "<option value = \"" . $row["tournée"] . "\"selected=\"selected\">" . $row["tournée"] . "</option>";
        }
        else {
          $res .= "<option value = \"" . $row["tournée"] . "\">" . $row["tournée"] . "</option>";
        }
    } 
  } else {
      $res = "0 results"; 
  } 
 
  $conn->close(); 
  return $res;
}


if (isset($_POST['preselect'])){
  echo listTypes($_POST['preselect']);
}
  else{
      echo listTypes(null);
}
?>
