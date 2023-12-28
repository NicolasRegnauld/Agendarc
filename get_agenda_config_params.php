<?php
session_start();

function getSessionAgendaParams(){
    if (isset($_SESSION['agendaStartTime'])){
        $msg["agendaStartTime"] = $_SESSION['agendaStartTime'];
        $msg["agendaEndTime"] = $_SESSION['agendaEndTime'];
        $msg["agendaTimeInterval"] = $_SESSION['agendaTimeInter'];          

    }
    else {
        $res = getAgendaParams();
        if ($res["statut"] == "success"){
            $_SESSION['agendaStartTime'] = $res['message']['agendaStartTime'];
            $_SESSION['agendaEndTime'] = $res['message']['agendaEndTime'];   
            $_SESSION["agendaTimeInter"] = $res['message']['agendaTimeInterval'];   
            $msg["agendaStartTime"] = $_SESSION['agendaStartTime'];
            $msg["agendaEndTime"] = $_SESSION['agendaEndTime'];    
            $msg["agendaTimeInterval"] = $_SESSION['agendaTimeInter'];   
        }
        else {
            echo "Echec pour retrouver les paramètres d'heures de début et de fin pour l'agenda, chargement de valeurs par défaut (7h - 20h)";
            $msg["agendaStartTime"] = 7;
            $msg["agendaEndTime"] = 20;  
            $msg["agendaTimeInterval"] = 15;
        }
    }
    return $msg;    
          
}

function getAgendaParams() {
 
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

  $sql = "SELECT * FROM ag_config where compte = '" . $_SESSION["compte"] . "'";
  $result = $conn->query($sql);

  $res["statut"] = "success";
  if ($result->num_rows == 1) {
    // output data of each row
	
    $row = $result->fetch_assoc();
    $msg["agendaStartTime"] = $row["agenda_start_time"];
    $msg["agendaEndTime"] = $row["agenda_end_time"];
    $msg["agendaTimeInterval"] = $row["agenda_time_interval"];
    $res["message"] = $msg;
    $_SESSION["agendaStartTime"] = $row["agenda_start_time"];
    $_SESSION["agendaEndTime"] = $row["agenda_end_time"];    
    $_SESSION["agendaTimeInter"] = $row["agenda_time_interval"];
	
  } 
  else
  {
        $res["statut"] = "error";
        $res["message"]= "Config non trouvée";
  }

  $conn->close();
  return $res;  
}

if (isset($_POST['fromsession']))
    echo json_encode(getSessionAgendaParams());
else
    echo json_encode(getAgendaParams());
?>