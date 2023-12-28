<?php
session_start();
function getInfsForTournee($date, $selTournees) {
 
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

      for ($i = 0; $i< count($tournees); $i++){
        $sql = "SELECT tournée, start_time1, end_time1 FROM ag_tournée WHERE compte = '" . $_SESSION["compte"] . "' AND id = $tournees[$i] AND tournée IN " . $selTournees ."";
        $result2 = $conn->query($sql);
        if ($result2 == false) 
            array_push($res, $sql);
        if ($result2->num_rows > 0)
        {
            array_push($res, $infId);
        } 
      }
  } 

  $conn->close();
  return json_encode($res);  
}

if (isset($_POST['date'])&&
    isset($_POST['tournees'])){
    $tournees = "(".implode(',', $_POST['tournees']).")";
    $date = filter_input(INPUT_POST, 'date', FILTER_SANITIZE_STRING);
    echo getInfsForTournee($date,$tournees);
}
else 
    echo "missing arguments for getAgenda";?>