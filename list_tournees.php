<?php
session_start();
function listHoraires() {
 
    // Connect to our database (Step 2a)
    include_once 'connexion.php';

    $connectDetails = getConnectionDetails();
    // Create connection
    $conn = new mysqli($connectDetails[0], $connectDetails[1], $connectDetails[2], $connectDetails[3]);
    $conn->query('SET NAMES utf8');

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    } 

    $res["statut"]="success";
    $res["msg"]="";
    $res["data"] = "";
    
    $currentTournée = "";
    $sql = "SELECT id, tournée, val_abr, val_full, start_time1, end_time1 FROM ag_tournée  where compte = '" . $_SESSION["compte"] . "' ORDER BY tournée";
    $result = $conn->query($sql);
//    if ($result === TRUE){
        if ($result->num_rows > 0) {
            // output data of each row
            $data = "";
            while($row = $result->fetch_assoc()) {
                if ($currentTournée !=  $row["tournée"]){
                    // en-ete pour la tournée
                    $currentTournée =  $row["tournée"];
                    $data .= "<tr class='active'><td>" . $row["tournée"]. "</td><td></td><td></td><td></td><td></td></tr>";
                } 
                $data .= "<tr class='horaireItem'><td class='horaireId' hidden>" . $row["id"]. "</td><td></td><td>" . $row["val_abr"] . "</td><td>" . $row["val_full"] . "</td><td>" . $row["start_time1"] . "</td><td>" . $row["end_time1"] . "</td></tr>";
                
            }
            $res["data"] = $data;
        } else if ($result->num_rows == 0) {
            $res["statut"]="empty";
            $res["msg"]="Pas de horaires enregistrées dans la base de données";
        } 
        else {
            $res["statut"]="failed";
            $res["msg"]="Un problème est survenu lors de la récupération des tâches";
        }
//    }
//    else {
//        $res["statut"]="failed";
//        $res["msg"]="Un problème est survenu lors de la récupération des tâches: " . $conn->error;
//    }
        
    $conn->close(); 
    return json_encode($res); 
    }

      
    echo listHoraires();

?>
