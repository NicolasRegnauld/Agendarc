<?php
session_start();

function listMsgs() {
 
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

  $sql = "SELECT id, msg FROM ag_message where compte = '" . $_SESSION["compte"] . "'";
  $result = $conn->query($sql);


  $res = "";

  if ($result->num_rows > 0) {
    // output data of each row
    $res = $result->num_rows  . ' messages   <button id="addMessage" type="button">+</button><ul>';
    while($row = $result->fetch_assoc()) {
        $res .= "<li><span hidden>" . $row["id"] . "</span>" . $row["msg"] . "   <button onclick=deleteMessage(".$row["id"] . ") style='font-size: 1em; background:none; border:none' class='glyphicon glyphicon-trash'</button></li>";
    } 
    $res .= "</ul>";
  } else {
      $res = 'Aucun message - <button id="addMessage" type="button">+</button>'; 
  } 
   
  $conn->close(); 
  return($res);  
}

echo listMsgs();

?>
