<?php
session_start();
// Connect to our database (Step 2a)
include_once 'connexion.php';




function deleteMessage ($msgId){
    $connectDetails = getConnectionDetails();
    // Create connection
    $conn = new mysqli($connectDetails[0], $connectDetails[1], $connectDetails[2], $connectDetails[3]);
    $conn->query('SET NAMES utf8');

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    } 
    
    $res[] = array();

    $sql = "DELETE FROM ag_message WHERE id = '" . $msgId . "'";

    if ($conn->query($sql) === TRUE) {
        $res["statut"] = "success";
        $res["message"] = "Message supprimée avec success";
            
    } else {
        $res["statut"] = "Echec";
        $res["message"] = "Le message n'a pas pu être supprimée." . $conn->error;
    }
    
    $conn->close();
    return $res;

}

if (isset($_POST['msgId'])){
    $id = filter_input(INPUT_POST, 'msgId', FILTER_SANITIZE_NUMBER_INT);
    
    echo trim(json_encode(deleteMessage($id)));
}
else 
    echo "missing arguments for delete message";

?>
