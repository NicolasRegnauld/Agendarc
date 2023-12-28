<?php
// Connect to our database (Step 2a)
include_once 'connexion.php';

function getDetails($visiteId){
    $connectDetails = getConnectionDetails();
    // Create connection
    $conn = new mysqli($connectDetails[0], $connectDetails[1], $connectDetails[2], $connectDetails[3]);
    $conn->query('SET NAMES utf8');

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }     
        
    $sql = "SELECT ag_visite.*, ag_infirmière.nom as inf_nom, ag_infirmière.id as inf_id, ag_client.nom as client_nom, ag_client.id as client_id, 
        ag_statut.libellé as statut_txt, ag_client.adresse as client_adresse
        FROM ag_visite
              LEFT JOIN ag_client on ag_client.id = client_id
              LEFT JOIN ag_infirmière on ag_visite.infirmière_id = ag_infirmière.id
              LEFT JOIN ag_statut on ag_statut.id = ag_visite.statut_visite
            WHERE ag_visite.id=" . $visiteId;

    //echo json_encode(array("titre"=>$sql));
    $result = $conn->query($sql);



    if ($result->num_rows == 1) {
        // output data of each row

        $res[] = array();
        $row = $result->fetch_assoc(); 
        // get the values from the visite row
        $adr = $row["adresse"];
        $titre = $row["titre"];
        $clientNom = $row["client_nom"];
        $clientId = $row["client_id"]; 
        $infNom = $row["inf_nom"];
        $infId = $row["inf_id"];
        $notes = $row["notes"];
        $rapport = $row["rapport"];
        $statutId = $row["statut_visite"];
        $statutText = $row["statut_txt"];
        $dateDebut = $row["date"]; 
        $dateFin = $row["date_fin"]; 
        $heure = $row["heure"]; 
        $durée = $row["durée"]; 
        $tournée = $row["tournée"];
        $alerte = $row["alerte"];
        $alerteMessage = $row["alerte_message"];
                
        if ($row["parent_visite_id"] != null){
            $sql = "SELECT ag_visite.*, ag_infirmière.nom as inf_nom, ag_infirmière.id as inf_id, ag_client.nom as client_nom, ag_client.id as client_id, 
            ag_statut.libellé as statut_txt, ag_client.adresse as client_adresse
            FROM ag_visite
                  LEFT JOIN ag_client on ag_client.id = client_id
                  LEFT JOIN ag_infirmière on ag_visite.infirmière_id = ag_infirmière.id
                  LEFT JOIN ag_statut on ag_statut.id = ag_visite.statut_visite
                WHERE ag_visite.id=" . $row["parent_visite_id"];

            $resultP = $conn->query($sql);

            // if the visite has a parent, fill in all the empty values using those from the parent visite 
            if ($resultP->num_rows == 1) {
                // output data of each row
                $rowP = $resultP->fetch_assoc();
                if (($titre == null) || ($titre == ""))                               
                    $titre = $rowP["titre"];
                if (($clientNom == null) || ($clientNom == ""))  
                    $clientNom = $rowP["client_nom"];
                if (($clientId == null) || ($clientId == ""))  
                    $clientId = $rowP["client_id"]; 
                if (($adr == null) || ($adr == ""))
                    $adr = $rowP["adresse"]; 
                if (($infNom == null) || ($infNom == ""))  
                    $infNom = $rowP["inf_nom"];
                if (($infId == null) || ($infId == ""))  
                    $infId = $rowP["inf_id"];
                if (($notes == null) || ($notes == ""))  
                    $notes = $rowP["notes"];
                if (($statutId == null) || ($statutId == ""))  
                    $statutId = $rowP["statut_visite"];
                if (($statutText == null) || ($statutText == ""))  
                    $statutText = $rowP["statut_txt"];
                if (($dateDebut == null) || ($dateDebut == ""))  
                    $dateDebut = $rowP["date"]; 
                if (($dateFin == null) || ($dateFin == ""))  
                    $dateFin = $rowP["date_fin"]; 
                if (($heure == null) || ($heure == ""))  
                    $heure = $rowP["heure"]; 
                if (($durée == null) || ($durée == ""))  
                    $durée = $rowP["durée"]; 
                if (($tournée == null) || ($tournée == ""))  
                    $tournée = $rowP["tournée"];
                if (($alerte == null) || ($alerte == ""))  
                    $alerte = $rowP["alerte"];
                if (($alerteMessage == null) || ($alerteMessage == ""))  
                    $alerteMessage = $rowP["alerte_message"];
                if (($adr == null) || ($adr == ""))
                    $adr = $rowP["client_adresse"];
            }

        }

        // si l'adresse n'est pas renseignée pour la visite, on donne celle du client
        if (($adr == null) || ($adr == ""))
            $adr = $row["client_adresse"];
        $res["statut"] = "success";
        $res["message"] = array("titre"=>$titre, "clientNom"=>$clientNom, "clientId"=>$clientId, 
        "infirmière"=>$infNom, "infirmièreId"=>$infId, "adresse"=>$adr, "notes"=>$notes,
        "rapport"=>$rapport, "statutId"=>$statutId, "statutText"=>$statutText, "date"=>$dateDebut, "heure"=>$heure, 
        "durée"=>$durée, "tournée"=>$tournée, "alerte"=>$alerte, "alerteMessage"=>$alerteMessage);

            
    } else {
        $res["statut"] = "error";
        $res["message"]= "Visite non trouvée";
    }     

    $conn->close();
    return $res;
}

if (isset($_POST['visitId'])){
    $id = filter_input(INPUT_POST, 'visitId', FILTER_SANITIZE_NUMBER_INT);
    echo trim(json_encode(getDetails($id)));
}



?>
