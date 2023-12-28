<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require_once 'connexion.php';


function updateTypeVisites(){
    $connectDetails = getConnectionDetails();
    // Create connection
    $conn = new mysqli($connectDetails[0], $connectDetails[1], $connectDetails[2], $connectDetails[3]);
    $conn->query('SET NAMES utf8');

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }     
        
    $res = "";
    $sql = "select id, horairesOld, compte from ag_type_visite";
    //echo json_encode(array("titre"=>$sql));
    $result = $conn->query($sql);
    while ($row = $result->fetch_assoc()){
        
        $horaires = explode(",", $row["horairesOld"]);
        $horaireIds = array();
        foreach($horaires as $horaireAbr) {
            $sql = "select id from ag_horaire WHERE compte = '" . $row["compte"] . "' AND val_abr = '" . $horaireAbr . "'";
            $result2 = $conn->query($sql);
            $row2 = $result2->fetch_assoc();
            array_push($horaireIds, $row2["id"]);
        }
        
        $sql = "UPDATE ag_type_visite SET horaireIds='" . implode(",", $horaireIds) . "' WHERE id = '" . $row["id"] . "'";
        if ($conn->query($sql) === TRUE) {
            $res .= $row["id"] . " modified successfully <br>";
        }
        else {
            $res .= "Failed to update  ". $row["id"] . "<br>";
        }
    }
    
    return $res;
            
}


function updateVisites(){
    $connectDetails = getConnectionDetails();
    // Create connection
    $conn = new mysqli($connectDetails[0], $connectDetails[1], $connectDetails[2], $connectDetails[3]);
    $conn->query('SET NAMES utf8');

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }     
        
    $res = "";
    
    $sql = "UPDATE ag_visite SET typeId = (SELECT id from ag_type_visite WHERE ag_type_visite.compte = ag_visite.compte AND ag_type_visite.label = ag_visite.typeOld) WHERE ag_visite.typeId = " . "'0'";
    if ($conn->query($sql) === TRUE) {
            $res .=  mysqli_affected_rows($conn) . " rows modified successfully <br>";
        }
        else {
            $res .= "Failed to update: " . $sql;
        }
    return $res;
}

function updateDispo(){
    $connectDetails = getConnectionDetails();
    // Create connection
    $conn = new mysqli($connectDetails[0], $connectDetails[1], $connectDetails[2], $connectDetails[3]);
    $conn->query('SET NAMES utf8');

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }     
        
    $res = "";

    $sql = "UPDATE ag_dispo SET compte = (SELECT compte from ag_infirmière WHERE ag_infirmière.id = ag_dispo.infId)"; 
    if ($conn->query($sql) === TRUE) {
        $res .=  mysqli_affected_rows($conn) . " rows modified successfully  with compte set<br>";
    
        $sql = "UPDATE ag_dispo SET horaireId = (SELECT id from ag_horaire WHERE ag_horaire.val_abr = ag_dispo.horaireOld AND ag_horaire.compte = ag_dispo.compte) "; 
        if ($conn->query($sql) === TRUE) {
            $res .=  mysqli_affected_rows($conn) . " rows modified successfully with horaireId<br>";
        }
        else {
            $res .= "Failed to update typeId: " . $sql;
        }
    }
    else {
        $res .= "Failed to updat comptee: " . $sql;
    }
        
    return $res;
}

/*
 * Updates table ag_dispo to transfer the single ID colum horaireId to the colum "horaires"
 * which is a string of comma separated horraire Ids. (Horraire is the new name for horaires, 
 * one horaire may or may not be specific to a type of visit)
 */
function updateDispoH(){
    $connectDetails = getConnectionDetails();
    // Create connection
    $conn = new mysqli($connectDetails[0], $connectDetails[1], $connectDetails[2], $connectDetails[3]);
    $conn->query('SET NAMES utf8');

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }     
        
    $res = "";

    $sql = "UPDATE ag_dispo SET horaires = horaireId "; 
    if ($conn->query($sql) === TRUE) {
        $res .=  mysqli_affected_rows($conn) . " rows modified successfully with horaires<br>";
    }
    else {
        $res .= "Failed to update horaires: " . $sql;
    }
    
  
        
    return $res;
}

// affecte toutes les visites non reliées à une visite parent @ une visite parent de même titre
function addParents(){
    $connectDetails = getConnectionDetails();
    // Create connection
    $conn = new mysqli($connectDetails[0], $connectDetails[1], $connectDetails[2], $connectDetails[3]);
    $conn->query('SET NAMES utf8');

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }     
        
    $res = "";
    $sql = "SELECT DISTINCT client_id, titre, typeId, heure, durée, compte, count(*) as nb FROM ag_visite GROUP BY client_id, titre, typeId, compte";
    $result = $conn->query($sql);
    while ($row = $result->fetch_assoc()){
        
        $sql = "SELECT id, compte, client_id, titre FROM ag_visite WHERE client_id = '" . $row["client_id"] . "' AND titre = '" . $row["titre"] . "' AND parent_visite_id = id" ;
        if ($result2 = $conn->query($sql)){
            if ($result2->num_rows >= 1){
                
                $row2 = $result2->fetch_assoc();
                $res .= $row2["id"] . " is a master for " . $row["client_id"] . " - " . $row["titre"] . " (" . $row["nb"] . ")<br>";
                $sql = "UPDATE ag_visite SET parent_visite_id = '" . $row2["id"] . "' WHERE compte = '" . $row2["compte"] . "' AND client_id = '" . $row2["client_id"] . "' AND titre = '" . $row2["titre"] . "'"; 
                if ($conn->query($sql)){
                    $res .= "update done";
                }
                else {
                    $res .= "update failed";
                }
                    
                    
                
            }
            else {
                if ($row["nb"] > 1){
                $parentId = createParentVisite($conn, $row["titre"],$row["typeId"],"","999", $row["client_id"], "", "2018-12-31", $row["heure"], "1", $row["durée"], "0", "", "", "1");
            
                $res .= $parentId . "créated for " . $row["client_id"] . " - " . $row["titre"] . " (" . $row["nb"] . ")<br>";
                }
                else {
                    $res .= $row["client_id"] . " - " . $row["titre"] . " (" . $row["nb"] . ")<br>";
                }
            }
                
        }
        else {
            $res .= "Failure for " . $row["client_id"] . " - " . $row["titre"] . "<br>";
        }
    }
    return $res;
}

function cleanMissingClient(){
    $connectDetails = getConnectionDetails();
    // Create connection
    $conn = new mysqli($connectDetails[0], $connectDetails[1], $connectDetails[2], $connectDetails[3]);
    $conn->query('SET NAMES utf8');

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }     
        
    $res = "";
    $sql = "DELETE FROM ag_visite WHERE client_id NOT IN (SELECT c.id FROM ag_client c)";
    if ($result = $conn->query($sql)){
        $nbDel = mysqli_affected_rows($conn);
        $res = $nbDel . " visites effacées";
    }
    else {
        $res = "Aucune de visites effacée";
    }
    return $res;
        
}

// force la tentative d'affectation d'une infirmière tous les jours ou au moins une visite non affectée existe
function forceRefreshVisites999()
{
    $connectDetails = getConnectionDetails();
    // Create connection
    $conn = new mysqli($connectDetails[0], $connectDetails[1], $connectDetails[2], $connectDetails[3]);
    $conn->query('SET NAMES utf8');

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }     
        
    $res = "";
    $sql = "SELECT DISTINCT date, compte from ag_visite where infirmière_id = '999'"; 
    $result = $conn->query($sql);
    while ($row = $result->fetch_assoc()){
        $res .= affectVisitesForDate($conn, $row["date"], $row["compte"]);
    }
    
    return $res;
}

function replaceTypeIdByTournee(){
    $connectDetails = getConnectionDetails();
    // Create connection
    $conn = new mysqli($connectDetails[0], $connectDetails[1], $connectDetails[2], $connectDetails[3]);
    $conn->query('SET NAMES utf8');

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }     
    
    $res = "";
    $sql = "SELECT id, typeId FROM ag_visite";
    $result = $conn->query($sql);
    while ($row = $result->fetch_assoc()){
        
        $sql = "SELECT label FROM ag_type_visite WHERE id = '" . $row["typeId"] . "'";
        if ($result2 = $conn->query($sql)){
            if ($result2->num_rows == 1){
                
                $row2 = $result2->fetch_assoc();
                $sql = "UPDATE ag_visite SET tournée = '" . $row2["label"] . "' WHERE id = '" . $row["id"] . "'"; 
                if ($conn->query($sql)){
                    $res .= "update done";
                }
                else {
                    $res .= "update failed";
                }
                    
                    
                
            }
            else {
                $res .= "update failed for id " . $row["typeId"] . " with type " . $row["typeId"];
            }
                
        }
        else {
            $res .= "update failed (2) for id " . $row["typeId"] . " with type " . $row["typeId"];
        }
    }
    return $res;
        
}

// echo updateTypeVisites();
// echo updateVisites();
// echo updateDispo();
// echo updateDispoH();
// echo addParents();
// echo cleanMissingClient();
// echo forceRefreshVisites999();
// echo replaceTypeIdByTournee();
echo "Pas de mise à jour nécessaire."
?>
