<?php
// Connect to our database (Step 2a)
include_once 'connexion.php';

function deleteInf($infId) {
$connectDetails = getConnectionDetails();
// Create connection
$conn = new mysqli($connectDetails[0], $connectDetails[1], $connectDetails[2], $connectDetails[3]);
$conn->query('SET NAMES utf8');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

// ne détruire l'infirmière que si elle n'apparait dans aucune visite terminée (statut  = 3)
$sql = "SELECT COUNT(id) AS nbVisites FROM ag_visite WHERE infirmière_id = '" . $infId . "' AND statut_visite = '" . 3 . "'";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$res = array();
if ($row["nbVisites"] > 0) {
    $res["message"] = "Impossible d'effacer cette infirmière car elle est référencée dans " . $row["nbVisites"] . " visite(s) terminée(s)";
    $res["statut"] = "abort";
}
else {
    $sql = "DELETE FROM ag_infirmière WHERE id = '" . $infId . "'";

    if ($conn->query($sql) === TRUE) {
        $res["statut"] = "success";
        $res["message"] = "Infirmière supprimée avec succès";
    } else {
        $res["statut"] = "failure";
        $res["message"] = "Une erreur est survenue, l'infirmière n'a pas été effacée. " . $conn->error;
    }
}

$conn->close();
return $res;
}

if (isset($_POST["infId"])){
    $infId = filter_input(INPUT_POST, 'infId', FILTER_SANITIZE_NUMBER_INT);
    echo json_encode(deleteInf($infId));
}
else {
    $res = array();
    $res["statut"] = "failure";
    $res["message"] = "Problème de paramètre dans l'appel à la fonction pour effacer une infirmière";
    echo json_encode($res);
}
    

?>
