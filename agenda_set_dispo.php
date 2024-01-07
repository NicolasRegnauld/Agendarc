<?php
session_start();

// Connect to our database (Step 2a)
include_once 'connexion.php';
include_once 'dispo_functions.php';


        
// find all the visites for that infirmière on that day, which have a horaire corresponding to the one being removed ($oldHoraire)
// update these with a value 999 for the infId field
function removeInfFromOldHoraire($conn, $infId, $date){
    $res = "";
    $sql = "SELECT id FROM ag_visite WHERE date = \"" . $date . "\" AND infirmière_id = '" . $infId . "'"; 


    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
	while($row = $result->fetch_assoc()) {
            $res .= updateVisiteInf($conn, $row['id'], '999');
        }
    }
    return $res;
}



function updateVisites($conn, $infId, $oldHoraire, $newHoraire, $date){
    $res = "Res from updateVisites: ";
    if ($oldHoraire != null)
        removeInfFromOldHoraire($conn, $infId, $date);
    
    $res .= affectVisitesForDate($conn, $date, $_SESSION['compte']);
    return $res;
}
        

function setDispo($year,$month,$day,$infId,$newVal){
// Create connection
$connectDetails = getConnectionDetails();
$conn = new mysqli($connectDetails[0], $connectDetails[1], $connectDetails[2], $connectDetails[3]);
$conn->query('SET NAMES utf8');

$res[] = array();

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT id FROM ag_infirmière where compte = '" . $_SESSION["compte"] . "' AND id = " . $infId;
$result = $conn->query($sql);
if ($result->num_rows != 1) {
  $res["statut"] = "erreur";
  $res["data"] = "";
  $res["message"] = "Infirmiere ID " . $infId . " inconnu";
  return $res;
}
$tmpDate = $year . "-" . $month . "-" . $day;
//$tmpDate = date_format($tmpDate, "Y-m-d")date_create($tmpString);
$oldHoraires = null;
$sql = "SELECT horaires FROM ag_dispo WHERE date = \"" . $tmpDate . "\" AND infId = " . $infId;
$result = $conn->query($sql);
if ($result->num_rows == 1) {
    $row = $result->fetch_assoc();
    $oldHoraires = $row["horaires"];
    $sql = "UPDATE ag_dispo SET horaires = \"" . $newVal . "\"WHERE date = \"" . $tmpDate . "\" AND infId = " . $infId;
}
else {
    $sql = "INSERT INTO ag_dispo (date, infId, horaires, compte) VALUES (\"". $tmpDate . "\"," . $infId . ",\"" . $newVal . "\",\"". $_SESSION['compte'] ."\")";
}

$result = $conn->query($sql);
$res["statut"] = "success";
$res["message"] = updateVisites($conn, $infId, $oldHoraires, $newVal, $tmpDate);
$conn->close();

return $res;
}


if (isset($_POST['year'])&&
    isset($_POST['month'])&&
    isset($_POST['day'])&&
    isset($_POST['infId'])&&
    isset($_POST['newVal'])
       ){
    $month = filter_input(INPUT_POST, 'month', FILTER_SANITIZE_NUMBER_INT);
    $year = filter_input(INPUT_POST, 'year', FILTER_SANITIZE_NUMBER_INT);
    $day = filter_input(INPUT_POST, 'day', FILTER_SANITIZE_NUMBER_INT);
    $infId = filter_input(INPUT_POST, 'infId', FILTER_SANITIZE_NUMBER_INT);
    $newHoraires = filter_input(INPUT_POST, 'newVal');

 //   echo "params: " . ", " . $month . ", " . $year . ", " . $day . ", " . $infId . ", " . $newVal;
    echo trim(json_encode(setDispo($year,$month,$day,$infId,$newHoraires)));
}
else{ 
  $res["statut"] = "erreur";
  $res["data"] = "";
  $res["message"] = "Missing arguments for agenda_set_dispo";
  echo trim(json_encode($res));
}
?>