<?php
session_start();
/*
 * returns a list of objects, each representing information about an infirmiere and the tournees its working on for that day
 * [
 *  {"inf": infId1, "tournees":[{"abr":"M", "start": "07:00", "end":"12:00}, {"abr":"M", "start": "07:00", "end":"12:00}]}
 *  {"inf": infId2, "tournees":[...]}
 * ]
 */ 
function getInfTournees($date) {
 
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

  $res = array();
  $sql = "SELECT infId, horaires FROM ag_dispo WHERE date = '" . $date . "' AND compte = '" . $_SESSION["compte"] . "'";
  $result = $conn->query($sql);

  while($row = $result->fetch_assoc()) {
      $infId = $row["infId"];
      $tournees = explode(',', $row["horaires"]);
      $infObj = new stdClass();
      $infObj->infId=$infId;
      $infTournees = array();
      for ($i = 0; $i< count($tournees); $i++){
        $sql = "SELECT tournée, val_abr, start_time1, end_time1 FROM ag_tournée WHERE compte = '" . $_SESSION["compte"] . "' AND id = $tournees[$i]";
        $result2 = $conn->query($sql);
        
        while($row2 = $result2->fetch_assoc()) {
            $obj = new stdClass();
            $obj->tournee = $row2["tournée"];
            $obj->abr = $row2["val_abr"];
            $obj->start = $row2["start_time1"];
            $obj->end = $row2["end_time1"];            
            array_push($infTournees, $obj);
        } 
      }
      $infObj->tournees=$infTournees;
      array_push($res, $infObj);
  } 

  $conn->close();
  return json_encode($res);  
}

if (isset($_POST['date'])){
    $date = filter_input(INPUT_POST, 'date', FILTER_SANITIZE_STRING);
    echo getInfTournees($date);
}
else 
    echo "missing arguments for get_inf_tournees_for_date";?>