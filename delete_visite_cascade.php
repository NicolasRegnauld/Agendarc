<?php
// Connect to our database (Step 2a)
include_once 'connexion.php';

function deleteVisite ($parentId, $delParent, $dateDebut, $dateFin){
    $connectDetails = getConnectionDetails();
    // Create connection
    $conn = new mysqli($connectDetails[0], $connectDetails[1], $connectDetails[2], $connectDetails[3]);
    $conn->query('SET NAMES utf8');

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    } 

    $sql = "DELETE FROM ag_visite WHERE id != '$parentId' AND parent_visite_id = '$parentId' AND (statut_visite IS NULL || (statut_visite != '3'))" .
               "AND (date BETWEEN '$dateDebut' AND '$dateFin')";

    if ($conn->query($sql) === TRUE) {
        
        $nbDel = mysqli_affected_rows($conn);
        if (!$delParent){
            $msg["statut"] = "success";
            $msg["message"]  =  $nbDel . " visites supprimées avec success, del: " . $delParent;
        }
        else {
            $sql = "SELECT id from ag_visite WHERE (parent_visite_id = '$parentId') AND (id != '$parentId')";
            $result = $conn->query($sql);
            if ($result->num_rows == 0) {
                $sql = "DELETE FROM ag_visite WHERE id = '$parentId'";
                if ($conn->query($sql) === TRUE) {
                    $msg["statut"] = "success";
                    $nbDel++;
                    $msg["message"]  =  $nbDel . " visites supprimées avec success";
                }
                else {
                    $msg["statut"] = "failed";
                    $msg["message"]  =  $nbDel . " visites supprimées avec success, mais la visite récurente n'a pas pu être détruite";
                }
            }
            else {
                $msg["statut"] = "success";
                $msg["message"]  =  $nbDel . " visites supprimées avec success, mais la visite récurente n'a pas pu être détruite car " . $result->num_rows . " visites n'ont pu ^tre détruites cars elles ont le statut Terminé";
            } 
        }
            
    }
    else {
        $msg["statut"] = "error";
        $msg["message"] =$sql . "<br>" . $conn->error;
    }

    $conn->close();
    
    return $msg;

}

if (isset($_POST['visiteId']) &&
    isset($_POST['delParent']) &&
    isset($_POST['dateDebut']) &&
    isset($_POST['dateFin'])){
    $id = filter_input(INPUT_POST, 'visiteId', FILTER_SANITIZE_NUMBER_INT);
    $delParent = filter_input(INPUT_POST, 'delParent', FILTER_SANITIZE_NUMBER_INT);
    $dateDebut = filter_input(INPUT_POST, 'dateDebut');
    $dateFin = filter_input(INPUT_POST, 'dateFin');
    echo trim(json_encode(deleteVisite($id, $delParent, $dateDebut, $dateFin)));
}
else 
    echo "missing arguments for delete visite";

?>
