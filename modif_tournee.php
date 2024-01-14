<?php
session_start();
// Connect to our database (Step 2a)
include_once 'connexion.php';

function modifTournee($tournee, $horaireId,$abr,$desc, $start1, $end1){
    $connectDetails = getConnectionDetails();
    // Create connection
    $conn = new mysqli($connectDetails[0], $connectDetails[1], $connectDetails[2], $connectDetails[3]);
    $conn->query('SET NAMES utf8');


    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    } 
    
    $res[] = array();

    if (strtotime($start1) > strtotime($end1)){
        $res["statut"] = "Echec";
        $res["message"] = "l'heure de début pour le premier créneau est postérieure à l'heure de fin ";
    }
    else {

        $vals = "tournée='" . $tournee . "', val_abr='" . $abr . "', val_full='" . $desc . "', start_time1='" . $start1 . "', end_time1='" . $end1 .  "'";

        $sql = "UPDATE ag_tournée SET " . $vals . "WHERE id = '" . $horaireId . "'";


        if ($conn->query($sql) === TRUE) {
            $res["statut"] = "success";
            $res["message"] = "tournée modifiée avec success ";
            
        } else {
            $res["statut"] = "Echec";
            $res["message"] = "La tournee n'a pas pu être modifiée." . $conn->error;
        }
    }
    $conn->close();
    return $res;
}


if (isset($_POST['id'])&&
    isset($_POST['tournee'])&&
    isset($_POST['abr'])&&
    isset($_POST['desc'])&&
    isset($_POST['start1'])&&
    isset($_POST['end1'])){

    $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
    $tournee = filter_input(INPUT_POST, 'tournee');
    $abr = filter_input(INPUT_POST, 'abr');
    $desc = filter_input(INPUT_POST, 'desc');
    $start1 = filter_input(INPUT_POST, 'start1');
    $end1 = filter_input(INPUT_POST, 'end1');
    echo trim(json_encode(modifTournee($tournee, $id,$abr,$desc, $start1, $end1)));
}
else 
    echo "missing arguments for modif tournée";

?>
