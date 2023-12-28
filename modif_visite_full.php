<?php
session_start();
// modifie tous les champs suivants: 
//  - date
//  - heure
//  - infirmière_id
//  - notes
//  - rapport
//  - titre
//  - adresse
//  - statut_visite
//  - type
//  - durée
//  - alerte
//  - alerte_message

// Connect to our database (Step 2a)
include_once 'connexion.php';
include_once 'dispo_functions.php';
include_once 'get_visit_details.php';

function modifVisite($visitId, $parentId, $infirmièreId, $notes, $titre, $statut, $adresse, $rapport, $date, $dateFin, $durée, $heure, $tournée, $alerte, $alerteMessage){

    $connectDetails = getConnectionDetails();
    // Create connection
    $conn = new mysqli($connectDetails[0], $connectDetails[1], $connectDetails[2], $connectDetails[3]);
    $conn->query('SET NAMES utf8');

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    
    $msg["statut"] = "OK";
    $msg["message"] = "";

    $vals = "";
    $vals .= "notes='" . $notes . "',";
    $vals .= "titre='" . $titre . "',";
    $vals .= "statut_visite='" . $statut . "',";
    $vals .= "adresse='" . $adresse . "',";
    $vals .= "rapport='" . $rapport . "',";
    $vals .= "durée='" . $durée . "',";
    $vals .= "heure='" . $heure . "',";
    $vals .= "tournée='" . $tournée . "',";
    $vals .= "alerte='" . $alerte . "',";
    $vals .= "alerte_message='" . $alerteMessage . "',";

    $vals = trim($vals, ",");

    $heures = (int)substr($heure, 0, 2);
    $minutes = (int)substr($heure, 3, 2);
    $heureIn = $heure.":00";
    $heure = $heures . ":" . $minutes;


    if ($parentId == null){
        $sql = "SELECT date, heure, durée, tournée, infirmière_id from ag_visite WHERE id = '" . $visitId . "'";
        $result = $conn->query($sql);
         if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();
            $oldDate = $row["date"];
            $oldHeure = $row["heure"];
            $oldDurée = $row["durée"];
            $oldTournée = $row["tournée"];
            $oldInfirmière = $row["infirmière_id"];
            $vals = "date='" . $date . "'," . $vals;
            // on ne fait une réaffectation automatique que si des changements suceptible d'affecter la disponibilité de l'infirmière actuelle ont eu lieu 
            if (($infirmièreId != "999")  && 
                ((($date == $oldDate) && ($heureIn == $oldHeure) && ($durée == $oldDurée) && ($tournée == $oldTournée)&& ($infirmièreId == $oldInfirmière)) ||
                (check_inf_dispo($conn, $infirmièreId, $tournée, $date, $heure, $durée, $_SESSION["compte"], $visitId) == true))){
                $vals = "infirmière_id='" . $infirmièreId . "'," . $vals;
            }
            else {
                    $inf = getInfDispo($conn, $tournée, $date, $heures, $minutes, $durée, $_SESSION["compte"], $visitId);
                    $vals = "infirmière_id='" . $inf . "'," . $vals;
            }
            $sql = "UPDATE ag_visite SET " . $vals . "WHERE id = '" . $visitId . "'";
            if ($conn->query($sql) === TRUE) {
                // une lace se libère, essayer d'y affecter une autre visite
                    affectVisitesForDate($conn, $oldDate, $_SESSION["compte"]);
            } else {
                $msg["statut"] = "Erreur";
                $msg["message"] = $conn->error;
            }
        }
        else {
            $msg["statut"] = "Erreur";
            $msg["message"] = "visite non trouvée, " . $conn->error;
        }

    }
    else {
        $sql = "SELECT id, date from ag_visite WHERE  parent_visite_id = '" . $parentId . "' AND (date BETWEEN '". $date . "' AND '" . $dateFin . "')";
        $result = $conn->query($sql);
        $msg["message"] = "";
        while($row = $result->fetch_assoc()) {
            $tmpVisiteId = $row["id"];
            $isDispo = check_inf_dispo($conn, $infirmièreId, $tournée, $row["date"], $heure, $durée, $_SESSION["compte"], $tmpVisiteId);

            if ($isDispo == true){
                $vals2 = "infirmière_id='" . $infirmièreId . "'," . $vals;
            }
            else {
                $inf = getInfDispo($conn, $tournée, $row["date"], $heures, $minutes, $durée, $_SESSION["compte"], $tmpVisiteId);
                $vals2 = "infirmière_id='" . $inf . "'," . $vals;
            }
            $sql = "UPDATE ag_visite SET " . $vals2 . "WHERE id = '" . $tmpVisiteId . "'";
            if ($conn->query($sql) === TRUE) {
                // une lace se libère, essayer d'y affecter une autre visite
                affectVisitesForDate($conn, $row["date"], $_SESSION["compte"]);

            } else {
                $msg["statut"] = "Erreur";
                $msg["message"] = $conn->error;
            }
        }

    }


    $conn->close();

    return trim(json_encode($msg));
}

if (isset($_POST["visiteId"]) &&
    isset($_POST["parentId"]) &&
    isset($_POST["infirmièreId"]) &&
    isset($_POST["notes"]) &&
    isset($_POST["statut"]) &&
    isset($_POST["titre"]) &&
    isset($_POST["adresse"]) &&
    isset($_POST["rapport"]) &&
    isset($_POST["date"]) &&
    isset($_POST["dateFin"]) &&
    isset($_POST["durée"]) &&
    isset($_POST["heure"]) &&
    isset($_POST["tournée"]) &&
    isset($_POST["alerte"]) &&
    isset($_POST["alerteMessage"])){
   
    $visitId = filter_input(INPUT_POST, 'visiteId', FILTER_SANITIZE_NUMBER_INT);
    $parentId = filter_input(INPUT_POST, 'parentId', FILTER_SANITIZE_NUMBER_INT);
    $infirmièreId = filter_input(INPUT_POST, 'infirmièreId', FILTER_SANITIZE_NUMBER_INT);
    $statut = filter_input(INPUT_POST, 'statut', FILTER_SANITIZE_NUMBER_INT);
    $notes = filter_input(INPUT_POST, 'notes', FILTER_SANITIZE_STRING);
    $titre = filter_input(INPUT_POST, 'titre', FILTER_SANITIZE_STRING);
    $adresse = filter_input(INPUT_POST, 'adresse', FILTER_SANITIZE_STRING);
    $rapport = filter_input(INPUT_POST, 'rapport', FILTER_SANITIZE_STRING);
    $date = filter_input(INPUT_POST, 'date', FILTER_SANITIZE_STRING);
    $dateFin = filter_input(INPUT_POST, 'dateFin', FILTER_SANITIZE_STRING);
    $durée = filter_input(INPUT_POST, 'durée', FILTER_SANITIZE_NUMBER_INT);
    $heure = filter_input(INPUT_POST, 'heure', FILTER_SANITIZE_STRING);
    $tournée = filter_input(INPUT_POST, 'tournée', FILTER_SANITIZE_STRING);
    $alerte = filter_input(INPUT_POST, 'alerte', FILTER_SANITIZE_NUMBER_INT);
    $alerteMessage = filter_input(INPUT_POST, 'alerteMessage', FILTER_SANITIZE_STRING);

    echo modifVisite($visitId, $parentId, $infirmièreId, $notes, $titre, $statut, $adresse, $rapport, $date, $dateFin, $durée, $heure, $tournée, $alerte, $alerteMessage);
 
}
else {
    echo "problème d'arguments pour modifVisiteFull";
}

?>
