<?php
session_start();
include_once 'connexion.php';

function updateAgendaParams($startTime, $endTime, $timeInter, $compte){
	$connectDetails = getConnectionDetails();
	// Create connection
	$conn = new mysqli($connectDetails[0], $connectDetails[1], $connectDetails[2], $connectDetails[3]);
	$conn->query('SET NAMES utf8');

	$res[] = array();
	// Check connection
	if ($conn->connect_error){
            die("Connection failed: " . $conn->connect_error);
	} 

	$vals = "agenda_start_time='" . $startTime . "', agenda_end_time='" . $endTime . "', agenda_time_interval='" . $timeInter ."'";
		
	$sql = "UPDATE ag_config SET " . $vals . "WHERE compte = '" . $compte . "'";

	if ($conn->query($sql) === TRUE) {
		$res["statut"] = "success";
                $res["msg"] = "Paramètres de configuration modifiés";
                $_SESSION["agendaStartTime"] = $startTime;
                $_SESSION["agendaEndTime"] = $endTime;      
                $_SESSION["agendaTimeInter"] = $timeInter;      
	} else {
		$res["statut"] = "echec";
                $res["msg"] = "Echec lors de la sauvegarde des paramètres de configuration";
	}

	$conn->close();
	return json_encode($res);
}

if (isset($_POST['startTime'])&&
    isset($_POST['endTime'])&&
    isset($_POST['timeInterval'])){
		
    $startTime = filter_input(INPUT_POST, 'startTime', FILTER_SANITIZE_NUMBER_INT);
    $endTime = filter_input(INPUT_POST, 'endTime', FILTER_SANITIZE_NUMBER_INT);
    $timeInter = filter_input(INPUT_POST, 'timeInterval', FILTER_SANITIZE_NUMBER_INT);

    $compte = $_SESSION["compte"];
    if ($timeInter > 0)
        echo updateAgendaParams($startTime, $endTime, $timeInter, $compte);    
    else
        echo "60 doit être divisible par l'interval (valeurs possibles: 60, 30, 20, 15, 12, 10, 6, 5, 4, 3, 2, 1";
}
else 
    echo "missing arguments for updating agenda parameters";
?>
