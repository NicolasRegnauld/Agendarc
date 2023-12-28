<?php
session_start();
// Connect to our database (Step 2a)
include_once 'connexion.php';
include_once 'dispo_functions.php';
include_once 'client_functions.php';
        
function createParentVisite ($conn, $titre,$tournée,$adresse,$infId, $clientId, $dateDébut, $dateEnd, $time, $durée, $alerte, $alerte_message, $notes, $statut){
    $vals = "'" . $_SESSION["compte"] . "','". $titre . "','" . $tournée . "','" . $adresse . "','" . $infId . "','" . 
           $notes . "','" . $dateEnd . "','" . $time . "','". $durée . "','". $alerte . "','". $alerte_message . "','" .  $clientId . "'," . $statut;

    $sql = "INSERT INTO ag_visite (compte, titre, tournée, adresse, infirmière_id, notes, date_fin, heure, durée, alerte, alerte_message, client_id, statut_visite) VALUES ($vals)";
    
    if ($conn->query($sql) === TRUE) {
        $parentId = $conn->insert_id;
        $sql = "UPDATE ag_visite SET parent_visite_id = $parentId WHERE id = $parentId";
        if ($conn->query($sql) === TRUE) {
            return $parentId;
        } 
        else {
            return null;
        }
    }
    else {
        return null;
    }
}

function jourValide($tmpDate, $jours){
    if ($jours == null){
        return TRUE;
    }
    if (in_array(date_format($tmpDate, "N"), $jours)){
        return TRUE;
    }
    else {
        return FALSE;
    }  
            
            
}
function addVisite($titre,$tournée,$adresse,$infId, $clientId, $dateDébut, $dateEnd, $heures, $minutes, $inc, $jours, $durée, $alerte, $alerte_message, $notes, $statut, $parentId){
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



$dateStart = date_format(date_create($dateDébut),"Y-m-d");
$dateFin = date_format(date_create($dateEnd),"Y-m-d");
$time = $heures . ":" . $minutes;
$failed = false;
if (($dateStart != $dateFin)&&($parentId == null)){
    $parentId = createParentVisite($conn, $titre,$tournée,$adresse,$infId, $clientId, $dateDébut, $dateFin, $time, $durée, $alerte, $alerte_message, $notes, $statut);
    if ($parentId == null){
        $msg["statut"] = "Erreur";
        $msg["message"] = "Erreur during creation of parent visite";
        $failed = true;
    }
}

if (!$failed){
    $tmpDate = date_create($dateStart);
    $dateDiff = date_diff($tmpDate, date_create($dateEnd));
    $heure = $heures . ":" . $minutes;
    $cpt = 1;
    $cptLoop = 1;


    while (($dateDiff->format("%r%a") >= 0)&&($cptLoop < 1000)){
        $cptLoop++;
        // pour chaque date de visite
        if (!jourValide($tmpDate, $jours)){
            // cas ou la visite récurente est effectuée à jour fixe, et tmpDate ne correspond pas à un des jours choisis: on passe au jour suivant
            $tmpDate = date_add($tmpDate, date_interval_create_from_date_string($inc . ' days'));
            $dateDiff = date_diff($tmpDate, date_create($dateEnd), false);
        }
        else {
            $tmpInfId = $infId;
            $infIsDispo = check_inf_dispo($conn, $tmpInfId, $tournée, date_format($tmpDate,"Y-m-d"), $heure, $durée, $_SESSION["compte"], null);
            if (($tmpInfId == "999") || ($infIsDispo == false)){ //  signifie que l'infiemière doit être proposée automatiquement en fonction des disponibilités
                // requête pour trouver les infirmières dispo pour ce type de horaire ce jour là
                $tmpInfId = getInfDispo($conn, $tournée, date_format($tmpDate,"Y-m-d"), $heures, $minutes, $durée, $_SESSION["compte"], null);
                if ($tmpInfId == "999"){
                    $msg["message"] .= "pas d'infirmière disponible #" . $cpt . "," . $dateStart . "," .  $heure . "," . $durée ;
                    $cpt ++;
                }
            }

            if ($tmpInfId == null){
                    $msg["statut"] = "Erreur";
                    $msg["message"] = "";
                }
            else {

                if ($parentId == null)
                    $vals = "'" . $_SESSION["compte"] . "', NULL,'" . $titre . "','" . $tournée . "','" . $adresse . "','" . $tmpInfId . "','" . 
                        $notes . "','" . date_format($tmpDate, "Y-m-d") . "','" . $time . "','". $durée . "','". $alerte . "','". $alerte_message . "','" .  $clientId . "'," . $statut;
                else
                    $vals = "'" . $_SESSION["compte"] . "','" . $parentId . "','". $titre . "','" . $tournée . "','" . $adresse . "','" . $tmpInfId . "','" . 
                        $notes . "','" . date_format($tmpDate, "Y-m-d") . "','" . $time . "','". $durée . "','". $alerte . "','". $alerte_message . "','" .  $clientId . "'," . $statut;
    //            }
    //            else {                 
    //                $vals = "'" . $_SESSION["compte"] . "','". $parentId . "','','NULL','','" . $tmpInfId . "','','" . 
    //                        date_format($tmpDate, "Y-m-d") . "',NULL,NULL,NULL,'',NULL,NULL";
    //            }
                $sql = "INSERT INTO ag_visite (compte, parent_visite_id, titre, tournée, adresse, infirmière_id, notes, date, heure, durée, alerte, alerte_message, client_id, statut_visite) VALUES ($vals)";
                $tmpDate = date_add($tmpDate, date_interval_create_from_date_string($inc . ' days'));
                $dateDiff = date_diff($tmpDate, date_create($dateEnd), false);

                if ($conn->query($sql) === TRUE) {
                    } 
                else {
                    $msg["statut"] = "Erreur";
                    $msg["message"] = $conn->error . "requete: " . $sql;
                    }
            }
        }
    }
    if ($cptLoop >= 1000){
        $msg["statut"] = "Erreur";
        $msg["message"] = "Le processus s'est arrété après la création de 1000 visites, suspicion de problème";
    }
}
    
$conn->close();
    
return trim(json_encode($msg));

}

if (isset($_POST['titre'])&&
    isset($_POST['tournée'])&&    
    isset($_POST['adresse'])&&
    isset($_POST['infirmièreId'])&&
    isset($_POST['clientId'])&&
    isset($_POST['statut'])&&
    isset($_POST['dateDébut'])&&
    isset($_POST['dateFin'])&&
    isset($_POST['heures'])&&
    isset($_POST['minutes'])&& 
    isset($_POST['rec'])&&
    isset($_POST['durée'])&& 
    isset($_POST['alerte'])&&
    isset($_POST['alerteMessage'])&& 
    isset($_POST['clientNom'])&& 
    isset($_POST['clientPrenom'])&& 
    isset($_POST['clientAdr'])&& 
    isset($_POST['clientTel'])&& 
    isset($_POST['notes']) &&
    isset($_POST['jours']) &&    
    isset($_POST['parentId'])){
    $titre = filter_input(INPUT_POST, 'titre', FILTER_SANITIZE_STRING);
    $tournée = filter_input(INPUT_POST, 'tournée', FILTER_SANITIZE_STRING);
    $adresse = filter_input(INPUT_POST, 'adresse', FILTER_SANITIZE_STRING);
    $infId = filter_input(INPUT_POST, 'infirmièreId', FILTER_SANITIZE_NUMBER_INT);
    $clientId = filter_input(INPUT_POST, 'clientId', FILTER_SANITIZE_NUMBER_INT);
    $dateDébut = filter_input(INPUT_POST, 'dateDébut', FILTER_SANITIZE_NUMBER_INT);
    $dateFin = filter_input(INPUT_POST, 'dateFin', FILTER_SANITIZE_NUMBER_INT);
    $heures = filter_input(INPUT_POST, 'heures', FILTER_SANITIZE_NUMBER_INT);
    $minutes = filter_input(INPUT_POST, 'minutes', FILTER_SANITIZE_NUMBER_INT);
    $rec = filter_input(INPUT_POST, 'rec', FILTER_SANITIZE_NUMBER_INT);
    $durée = filter_input(INPUT_POST, 'durée', FILTER_SANITIZE_NUMBER_INT);
    $alerte = filter_input(INPUT_POST, 'alerte', FILTER_SANITIZE_NUMBER_INT);
    $alerte_message = filter_input(INPUT_POST, 'alerteMessage', FILTER_SANITIZE_STRING);
    $notes = filter_input(INPUT_POST, 'notes', FILTER_SANITIZE_STRING);
    $clientNom = filter_input(INPUT_POST, 'clientNom', FILTER_SANITIZE_STRING);
    $clientPrenom = filter_input(INPUT_POST, 'clientPrenom', FILTER_SANITIZE_STRING);
    $clientAdr = filter_input(INPUT_POST, 'clientAdr', FILTER_SANITIZE_STRING);
    $clientTel = filter_input(INPUT_POST, 'clientTel', FILTER_SANITIZE_STRING);
    $statut = filter_input(INPUT_POST, 'statut', FILTER_SANITIZE_NUMBER_INT);
    $parentId = filter_input(INPUT_POST, 'parentId', FILTER_SANITIZE_NUMBER_INT);
    $jours = $_POST['jours'];

    if (($dateDébut != $dateFin) && (!is_numeric($rec) || ($rec <= 0))){
        $msg0["statut"] = "Erreur";
        $msg0["message"] = "Pour les visites récurentes, la fréquence des visites doit être renseignée et strictement positive";
        echo trim(json_encode($msg0));
    }
    else {
            
        if ($clientId == null){
            $clientId = addClient($clientNom, $clientPrenom, $clientAdr, $clientTel);
        }

        echo addVisite($titre,$tournée,$adresse,$infId, $clientId, $dateDébut, $dateFin, $heures, $minutes, $rec, $jours, $durée, $alerte, $alerte_message, $notes, $statut, $parentId);
    }
}
else {
    $msg0["statut"] = "Erreur";
    $msg0["message"] = "missing arguments for nouvelle_visite";
    echo trim(json_encode($msg0));

}
?>
                          