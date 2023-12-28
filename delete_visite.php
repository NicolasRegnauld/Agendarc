<?php
// Connect to our database (Step 2a)
require_once 'connexion.php';
require_once 'dispo_functions.php';

function deleteVisite ($visiteId){
    $connectDetails = getConnectionDetails();
    // Create connection
    $conn = new mysqli($connectDetails[0], $connectDetails[1], $connectDetails[2], $connectDetails[3]);
    $conn->query('SET NAMES utf8');

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    } 
    
    $res = "";

    $sql = "SELECT statut_visite, date, compte FROM ag_visite WHERE id= $visiteId ";
    $result = $conn->query($sql); 
    if ($result->num_rows ==1 ){
        $row = $result->fetch_assoc();
        if ($row["statut_visite"] == 3)
            $res = "Impossible de supprimer une visite terminée, pour des raisons de traçabilité";
        else
        {
            $date = $row['date'];
            $compte = $row['compte'];
            $sql = "DELETE FROM ag_visite WHERE id = '" . $visiteId . "'";

            if ($conn->query($sql) === TRUE) {
                $res = "Visite supprimée avec success";
                // une place est libérée, essayer de réaffecter des visites
                affectVisitesForDate($conn, $date, $compte);

            } else {
                $res = "Error: " . $sql . "<br>" . $conn->error;
            }
        }
    }
    else {
        $res = "Erreur: visite" . $visiteId . " non trouvée";
    }
        


    $conn->close();
    return $res;

}

if (isset($_POST['visiteId'])){
    $id = filter_input(INPUT_POST, 'visiteId', FILTER_SANITIZE_NUMBER_INT);
    echo deleteVisite($id);
}
else 
    echo "missing arguments for delete visite";

?>
