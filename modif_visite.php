<?php
session_start();
// Connect to our database (Step 2a)
include_once 'connexion.php';
include_once 'dispo_functions.php';



function modifVisiteTime($visitId, $infString, $heureIn, $date){
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


    // get the id of the infirmière, which comes in the for "Inf1"
    $infId = substr($infString,3);

    $sql = "select durée from ag_visite where id = " . $visitId;
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();

        // get the time id o, which comes in the for "H1015"
        $heure = substr($heureIn, 1, 2) . ":" . substr($heureIn, 3);


    //    $date = date_create($_POST["date"]);

        $msg["message"] = "inf = " . $infId . ", " . $date . ", " . $heure . ", " . $row["durée"] . ", " . $visitId;
        if ($infId != '999'){
            $isDispo = check_inf_dispo_time($conn, $infId, $date, $heure, $row["durée"], $_SESSION["compte"], $visitId);

        }
        else {
            $isDispo = true;
        }
        if ($isDispo == true)
        {
            $vals = "infirmière_id='" . $infId . "',date='". $date . "',heure='" . $heure . "'";

            $sql = "UPDATE ag_visite SET " . $vals . "WHERE id = '" . $visitId . "'";


            if ($conn->query($sql) === TRUE) {
                $msg["message"] =  $infId . ", " . $date . ", " . $heure . ", " . $row["durée"] . ", " . $visitId . ", " . $isDispo;
                // une place est libérée, essayer de réaffecter des visites
                 $msg["message"] .= " - update: " . affectVisitesForDate($conn, $date, $_SESSION["compte"]);
            } else {
                $msg["statut"] = "Erreur2";
                $msg["message"] = $conn->error;
            }
        }
        else
        {
            $msg["statut"] = "Infirmière pas disponible";
        }
    }
    else
    {
        $msg["statut"] = "Erreur";
        $msg["message"] = "Visite à modifier non trouvée";
    }

    $conn->close();
    return trim(json_encode($msg));
}

function modifVisiteStatut($visitId, $statut){
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



    $sql = "select durée from ag_visite where id = " . $visitId;
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $msg["message"] = "";
        $sql = "UPDATE ag_visite SET statut_visite='" . $statut . "' WHERE id = '" . $visitId . "'";
        if ($conn->query($sql) === TRUE) {
            $msg["message"] = "Changement de statut effectué";
        } else {
            $msg["statut"] = "Erreur";
            $msg["message"] = $conn->error;
        }
    }
    else
    {
        $msg["statut"] = "Erreur";
        $msg["message"] = "Visite à modifier non trouvée";
    }

    $conn->close();
    return trim(json_encode($msg));
}

if (isset($_POST["visiteId"]) &&
    isset($_POST["infirmièreId"]) &&
    isset($_POST["date"]) &&
    isset($_POST["heure"])){
   
    $visitId = filter_input(INPUT_POST, 'visiteId', FILTER_SANITIZE_NUMBER_INT);
    $infString = filter_input(INPUT_POST, 'infirmièreId', FILTER_SANITIZE_STRING);
    $date = filter_input(INPUT_POST, 'date', FILTER_SANITIZE_STRING);
    $heure = filter_input(INPUT_POST, 'heure', FILTER_SANITIZE_STRING);

    echo modifVisiteTime($visitId, $infString, $heure, $date);
 
}
else if (isset($_POST["visiteId"]) &&
    isset($_POST["statut"])){
    
    $visitId = filter_input(INPUT_POST, 'visiteId', FILTER_SANITIZE_NUMBER_INT);
    $statut = filter_input(INPUT_POST, 'statut', FILTER_SANITIZE_NUMBER_INT);

    echo modifVisiteStatut($visitId, $statut);
    }
else
    echo "problème d'arguments pour modifVisiteFull";

?>
