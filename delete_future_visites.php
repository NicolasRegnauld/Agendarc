<?php
session_start();
include_once 'connexion.php';
require_once 'dispo_functions.php';


function deleteFutureVisites ($clientId, $date){
    $connectDetails = getConnectionDetails();
    // Create connection
    $conn = new mysqli($connectDetails[0], $connectDetails[1], $connectDetails[2], $connectDetails[3]);
    $conn->query('SET NAMES utf8');

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    } 
    $compte = $_SESSION["compte"];
    
    // d'abord, récupérer les dates des visites à supprimer, pour pouvoir réaffecter des visites sur les créneaux libérés
    $sql = "SELECT date FROM ag_visite WHERE client_id = '" . $clientId . "' AND date > '" . $date . "' AND compte = '" . $compte . "'";
    $datesToRefresh = $conn->query($sql);
    
    $res[] = array();

    $sql = "DELETE FROM ag_visite WHERE client_id = '" . $clientId . "' AND date > '" . $date . "' AND compte = '" . $compte . "'";

    if ($conn->query($sql) === TRUE) {
        $res["satut"] = 'succes';
        $res["message"] = "Visites supprimée avec success";
        // des visites ont été supprimées, mettre à jour les visites correspondant à ces jours pour utiliser les créneaux horaires libérés
        $cpt = 0;
        if ($datesToRefresh->num_rows > 0) {
            
            while($row = $datesToRefresh->fetch_assoc()) {
                $cpt ++;
                affectVisitesForDate($conn, $row["date"], $compte);
        }
        $res["satut"] = 'success';
        $res["message"] = $cpt . " visites supprimée avec success";
    }

    } else {
        $res["satut"] = 'failed';
        $res["message"] = "Les vidiest n'ont pas pu être effacées. " . $conn->error;
    }
            

        


    $conn->close();
    return $res;

}

if (isset($_POST['clientId']) &&
    isset($_POST['date'])){
    $clientId = filter_input(INPUT_POST, 'clientId', FILTER_SANITIZE_NUMBER_INT);
    $date = filter_input(INPUT_POST, 'date');
    echo json_encode(deleteFutureVisites($clientId, $date));
}
else 
    echo "missing arguments for delete future visites";

?>
