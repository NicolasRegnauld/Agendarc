<?php
session_start();

function getHoraires() {
 
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

  $sql = "SELECT id, tournée, val_abr, val_full, start_time1, end_time1 FROM ag_tournée where compte = '" . $_SESSION["compte"] . "'";
  $result = $conn->query($sql);

 

  if ($result->num_rows > 0) {
    // output data of each row
	
    $res = "<form>";
    $cpt = 0;
    while($row = $result->fetch_assoc()) {
        $startTime = date("h:i", strtotime($row['start_time1'])); 
        $endTime = date("h:i", strtotime($row['end_time1']));
        if ($cpt == 0){
            $res .= "<label  class=\"checkbox-inline horaireCheckboxOption\" data-toggle=\"tooltip\" title=\"". $row['tournée'] . ": " .  $row['val_full']. " - " . $startTime . " à " . $endTime ."\"><input type=\"checkbox\" value = \"" . $row['id'] . "\" name=\"horaireOption\" checked>" . $row['val_abr'] ."</label>";	        
            $cpt ++;
        }
        else {
            $res .= "<label  class=\"checkbox-inline horaireCheckboxOption\" data-toggle=\"tooltip\" title=\"". $row['tournée'] . ": " .  $row['val_full']. " - " . $startTime . " à " . $endTime . "\"><input type=\"checkbox\" value = \"" . $row['id'] . "\" name=\"horaireOption\">" . $row['val_abr'] ."</label>";	        
        }
    } 
    $conn->close();
    $res .= "</form>";
    return $res;
  } else {
      $conn->close(); 
      return "0 types de horaires"; 
  } 

  
}
  echo getHoraires();

?>
