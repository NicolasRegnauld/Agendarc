<?php
session_start();
// Connect to our database (Step 2a)
include_once 'connexion.php';




function deleteTournee ($horaireId){
    $connectDetails = getConnectionDetails();
    // Create connection
    $conn = new mysqli($connectDetails[0], $connectDetails[1], $connectDetails[2], $connectDetails[3]);
    $conn->query('SET NAMES utf8');

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    } 
    
    $res[] = array();

    $sql = "DELETE FROM ag_tournée WHERE id = '" . $horaireId . "'";

    if ($conn->query($sql) === TRUE) {
        $res["statut"] = "success";
        $res["message"] = "Tournée supprimée avec success";
            
    } else {
        $res["statut"] = "Echec";
        $res["message"] = "La tournée n'a pas pu être supprimée." . $conn->error;
    }
    
    $conn->close();
    return $res;

}

if (isset($_POST['horaireId'])){
    $id = filter_input(INPUT_POST, 'horaireId', FILTER_SANITIZE_NUMBER_INT);
    
    echo trim(json_encode(deleteTournee($id)));
}
else 
    echo "missing arguments for delete tournée";

?>
