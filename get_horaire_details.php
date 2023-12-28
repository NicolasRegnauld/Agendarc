<?php
// Connect to our database (Step 2a)
include_once 'connexion.php';

function getDetails($horaireId){
    $connectDetails = getConnectionDetails();
    // Create connection
    $conn = new mysqli($connectDetails[0], $connectDetails[1], $connectDetails[2], $connectDetails[3]);
    $conn->query('SET NAMES utf8');

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }     
        
    $sql = "SELECT compte, tournée, val_abr, val_full, start_time1, end_time1
        FROM ag_tournée WHERE id=" . $horaireId;

    //echo json_encode(array("titre"=>$sql));
    $result = $conn->query($sql);



    if ($result->num_rows == 1) {
        // output data of each row

        $res[] = array();
        $row = $result->fetch_assoc(); 
        // get the values from the visite row
        $abr = $row["val_abr"];
        $desc = $row["val_full"];
        $start1 = $row["start_time1"];
        $end1 = $row["end_time1"]; 
        $compte = $row["compte"];
        $tournee = $row["tournée"];
        $res["statut"] = "success";
        $res["message"] = array("tournee"=>$tournee, "abr"=>$abr, "desc"=>$desc, "start1"=>$start1, "end1"=>$end1);    

            
    } else {
        $res["statut"] = "error";
        $res["message"]= "Tournée non trouvée";
    }     

    $conn->close();
    return $res;
}

if (isset($_POST['horaireId'])){
    $id = filter_input(INPUT_POST, 'horaireId', FILTER_SANITIZE_NUMBER_INT);
    echo trim(json_encode(getDetails($id)));
}



?>
