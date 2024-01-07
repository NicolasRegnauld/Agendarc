<?php
session_start();
// Connect to our database (Step 2a)
include_once 'connexion.php';

function nouvelleHoraire($tournee, $abr,$desc, $start1, $end1){
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

        $vals = "'" . $_SESSION["compte"] . "','" . $tournee . "','" . $abr . "','" . $desc . "','" . $start1 . "','" . $end1 . "'";
        
        $sql = "INSERT INTO ag_tournée (compte, tournée, val_abr, val_full, start_time1, end_time1) VALUES ($vals)";

        
        if ($conn->query($sql) === TRUE) {
            $res["statut"] = "success";
            $res["message"] = "nouvelle horaire crée avec success " . $sql;           
        } else {
            $res["statut"] = "Echec";
            $res["message"] = "La tournée n'a pas pu être enregistrée." . $conn->error;
        }
    }
    $conn->close();
    return $res;
}


if (isset($_POST['abr'])&&
    isset($_POST['desc'])&&
    isset($_POST['start1'])&&
    isset($_POST['end1']) &&
    isset($_POST['tournee'])){

    $abr = filter_input(INPUT_POST, 'abr');
    $desc = filter_input(INPUT_POST, 'desc');
    $start1 = filter_input(INPUT_POST, 'start1');
    $end1 = filter_input(INPUT_POST, 'end1');
    $tournee = filter_input(INPUT_POST, 'tournee');
    echo trim(json_encode(nouvelleHoraire($tournee, $abr,$desc, $start1, $end1)));
}
else 
    echo "missing arguments for nouvelle tournee";

?>
