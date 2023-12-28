<?php


function getHorairesArray($conn, $tournee, $compte){
    $res = array();
    $sql = "SELECT id from ag_tournée WHERE tournée = '$tournee' AND compte = '" . $compte . "'"; 
    $result = $conn->query($sql);
    while( $row = $result->fetch_assoc()){
        array_push($res, $row["id"]);
    }        
    return $res;
    }
    
function getHoraires($conn, $tournee, $compte){
    $res = null;
    $horaires =  getHorairesArray($conn, $tournee, $compte);
    if ($horaires != null){
        $res = "('" . $horaires[0] . "'"; 
        for ($i=1; $i<count($horaires); $i++){
            $res .= ",'" . $horaires[$i] . "'";
            }
        $res .= ")";
        }
    return $res;
    }
    
function getHoraireAbrevsFromIds($conn, $horaires, $compte){
    $res = "";
    $horaires = "(" . $horaires . ")";
    $sql = "SELECT val_abr from ag_tournée WHERE compte = $compte AND id IN $horaires"; 
    $result = $conn->query($sql);
    $first = true;
    if ($result != null){
        while( $row = $result->fetch_assoc()){
            if ($first){
                $first = false;
            }
            else{
                $res .="/";
            }
            $res .= $row["val_abr"];
        }
    }
    else {
        $res = "failed to retieve abrevs " . $sql;
    }
    return $res;
}
/* 
 * Vérifie que l'infirmière ($infId) n'a pas une autre visite sur le créneau [$startTime, $startTime + durée]
 * à la date $date. 
 * $visiteId est null pour une création de visite, ou contient l'id de la visite à modifier. Dans ce cas, 
 * on ignore cette visite dans la recherche de clash
 */
function check_inf_dispo_time($conn, $infId, $date, $startTime, $durée, $visitId)
{
    // requète pour vérifier que l'infirmière n'a pas déjà une visite sur ce créneau horraire
    // on charge toutes les visites de l'infirmière ce jour, et on compare les créneaux
    $sql = "SELECT id, infirmière_id, heure, durée FROM ag_visite WHERE date = \"" . $date . "\" AND infirmière_id = " . $infId;
    $result = $conn->query($sql);
    
    $endTime =  getEndTime2($startTime, $durée);
    $newStartTime = ((int) substr($startTime, 0, 2)) * 60 + ((int) substr($startTime, 3, 2));
    $newEndTime = ((int) substr($endTime, 0, 2)) * 60 + ((int) substr($endTime, 3, 2));


    $dispo = true;
    if ($result != NULL) {

        while (($dispo == true) and ( $row = $result->fetch_assoc())) {
            
            if (($visitId == null) || ($visitId != $row["id"])) {        
                $oldStartTime = ((int) substr($row["heure"], 0, 2)) * 60 + ((int) substr($row["heure"], 3, 2));
                $oldEndTime = $oldStartTime + $row["durée"];
                
                
                if ((($oldStartTime <= $newStartTime) && ($oldEndTime > $newStartTime)) ||
                        (($oldStartTime < $newEndTime) && ($oldEndTime > $newEndTime)) ||
                        (($oldStartTime >= $newStartTime) && ($oldEndTime <= $newEndTime)))
                    $dispo = false;
            }
        }
    }
    return $dispo;
}

function check_inf_dispo($conn, $infId, $tournee, $date, $heureDébut, $durée, $compte, $visitId){
    $horaires = getHorairesArray($conn, $tournee, $compte);
    $res = false;
    $res2 = "";
    $heureFin = getEndTime2($heureDébut, $durée);
    if ($horaires != null){        
        // trouver les infirmières et leurs horaires pour le jour voulu 
        $sql = "SELECT infId, horaires FROM ag_dispo " .
                "WHERE date = \"" . $date . "\" AND infId = $infId";
        $result = $conn->query($sql);
        $res2 = "requete 1: " . $sql;
        if (($result != null) && ($result->num_rows == 1)){
            // Pour chacune des infirmière retournées par la requête précédente
            $row = $result->fetch_assoc();
            $horairesInf =  explode(",",$row["horaires"]);
            // on test tous ses horaires pour voir s'il y en a un qui est compatible avec l'horaire de la visite qui nous intéresse
            // dès u'on en trouve un, on s'arrête et on retourne l'infirmière
            for ($i=0; ($i<count($horairesInf)) && ($res == false); $i++){
                $horaireId = $horairesInf[$i];
                if(in_array($horaireId, $horaires)){
                    $sql = "SELECT start_time1, end_time1 from ag_tournée WHERE id = $horaireId";
                    $res2 .= "requete 2: " . $sql;

                    $resultH = $conn->query($sql);
                    if ($resultH != null){
                        $rowH = $resultH->fetch_assoc();
                        $hStart = $rowH['start_time1'];
                        $hEnd = $rowH['end_time1'];

                        if (($heureDébut >= $hStart) && ($heureFin <= $hEnd)){
                            // on a trouvé un horaire compatible pour cette infirmière 
                            // on vérifie que l'infirmière n'a pas déjà une visite sur ce créneau horaire
                            $res2 .= " call check inf dispo $infId, $date, $heureDébut, $durée, $visitId";
                            $res = check_inf_dispo_time($conn, $infId, $date, $heureDébut, $durée, $visitId);
                        }
                    }
                }
            }
        }
                           
    }
    return $res;
}

function addZero($i) {
    $res = (int)$i;
    if ($res < 10) {
        $res = "0" . $res;
    }
    return $res;
}


// returns "08:15" from "07", "15", "30"
function getEndTime2($heure, $durée){
    $heuresDébut = substr($heure, 0, 2);
    $minutesDébut = substr($heure, 3, 2);
    
    return getEndTime($heuresDébut, $minutesDébut, $durée);
}

// returns "08:15" from "07", "15", "30"
function getEndTime($heures, $minutes, $durée){
    $heuresFin = (int)$heures;
    $minutesFin = (int)$minutes + (int)$durée;
    while ($minutesFin >= 60){
        $minutesFin -= 60;
        $heuresFin ++;
    }
    return addZero($heuresFin) . ":" . addZero($minutesFin);
}
function getInfDispo($conn, $tournee, $date, $heures, $minutes, $durée, $compte, $visitId){
    $res = "999";
    $res2 = "";
    $horaires = getHorairesArray($conn, $tournee, $compte);
    if ($horaires != null){
        
        $startTime = addZero($heures) . ":" . addZero($minutes);
        $endTime = getEndTime($heures, $minutes, $durée);
        
        // trouver les infirmières et leurs horaires pour le jour voulu 
        $sql = "SELECT infId, horaires FROM ag_dispo " .
                "WHERE date = \"" . $date . "\" AND compte = '" . $compte . "'";
        $result = $conn->query($sql);
        if ($result != null){
            // Pour chacune des infirmière retournées par la requête précédente
            while(($res == "999") and ($row = $result->fetch_assoc())) {
                $horairesInf =  explode(",",$row["horaires"]);
                $res2 .= "check dispo, inf: " . $row["infId"] . "date: " . $date;
                // on test tous ses horaires pour voir s'il y en a un qui est compatible avec l'horaire de la visite qui nous intéresse
                // dès u'on en trouve un, on s'arrête et on retourne l'infirmière
                for ($i=0; ($i<count($horairesInf)) && ($res == "999"); $i++){
                    $horaireId = $horairesInf[$i];
                    if(in_array($horaireId, $horaires))
                    {
                        $sql = "SELECT start_time1, end_time1 from ag_tournée WHERE id = $horaireId";
                        $resultH = $conn->query($sql);
                        if ($resultH != null){
                            $rowH = $resultH->fetch_assoc();
                            $hStart = $rowH['start_time1'];
                            $hEnd = $rowH['end_time1'];
                        }
                        if ((($startTime . ":00") >= $hStart) && (($endTime . ":00") <= $hEnd)){
                            // on a trouvé un horaire compatible pour cette infirmière 
                            // requète pour vérifier que l'infirmière n'a pas déjà une visite sur ce créneau horaire
                            // on charge toutes les visites de l'infirmière ce jour, et on compare les créneaux
                            $isDispo = check_inf_dispo_time($conn, $row["infId"], $date, $startTime, $durée, $visitId);
                            $res2 .= "<br>    " .  ": " . $heures . "-" . $minutes . " -> " . $startTime . " - " . $endTime . " = " . $isDispo;
                            if ($isDispo == true)
                            {
                                $res = $row["infId"];
                            }
                        }
                    }                    
                }                       
            }
        }	
    }
    return $res;
}

function updateVisiteInf($conn, $visitId, $newInfId){
    $vals = "infirmière_id='" . $newInfId . "'";

    $sql = "UPDATE ag_visite SET " . $vals . "WHERE id = '" . $visitId . "'";


    if ($conn->query($sql) === TRUE) {
        return "Succes: champs infId de la visite " . $visitId . " mis à jour avec la valeur " . $newInfId;
    } else {
        return "Erreur: champs infId de la visite " . $visitId . " pas mise à jour: " . $sql;
    }
}

function affectVisitesForDate($conn, $date, $compte){
    $res = "";
    $sql = "SELECT ag_visite.id, tournée, heure, durée FROM ag_visite WHERE date = \"" . $date . "\" AND infirmière_id = '999' AND compte = '" . $compte . "'"; 
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
	while($row = $result->fetch_assoc()) {
            $heures = (int)substr($row['heure'], 0, 2);
            $minutes = (int)substr($row['heure'], 3, 2);
//            $res .= "Heure visite : " . $row['heure'] . " => " . $heures . " : " . $minutes . "<br>";
            $newInfId = getInfDispo($conn, $row['tournée'], $date, $heures, $minutes, $row['durée'], $compte, null);
//            $res .= $newInfId;
            $res .= updateVisiteInf($conn, $row['id'], $newInfId);
        }
    }
    return $res;
}


    