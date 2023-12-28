<?php
session_start();

// Connect to our database (Step 2a)
include_once 'connexion.php';

function dateFr($dateIn){
    $dateTime = date_create($dateIn);
    $res = date_format($dateTime, "d-m-Y");
    $day = date_format($dateTime, "N");
    $jour = "";
    
    switch ($day) {
    case "1":
        $jour = "Lun";
        break;
    case "2":
        $jour = "Mar";
        break;
    case "3":
        $jour = "Mer";
        break;
    case "4":
        $jour = "Jeu";
        break;
    case "5":
        $jour = "Ven";
        break;
    case "6":
        $jour = "Sam";
        break;
    case "7":
        $jour = "Dim";
        break;
    }
    $res =  $jour . " " . $res;
    return $res;
}
function getClientVisites($clientId){
$connectDetails = getConnectionDetails();
// Create connection
$conn = new mysqli($connectDetails[0], $connectDetails[1], $connectDetails[2], $connectDetails[3]);
$conn->query('SET NAMES utf8');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

$sql = "SELECT ag_visite.titre, ag_visite.infirmière_id, ag_visite.id as visiteId, parent_visite_id, ag_infirmière.nom AS inf_nom, ag_infirmière.id AS infId, ag_visite.date 
  FROM ag_visite
  LEFT JOIN ag_infirmière on ag_visite.infirmière_id = ag_infirmière.id
  WHERE ag_visite.client_id= $clientId AND ((ag_visite.id = parent_visite_id) OR parent_visite_id is NULL) ORDER BY ag_visite.date";

$result = $conn->query($sql);

$res = "";

if ($result->num_rows > 0) {
    // output data of each row
    $cpt = 1;	
    while($row = $result->fetch_assoc()) {
        $inf = $row["inf_nom"];
        if ($row['infirmière_id'] == '999')
            $inf = "pas affectée";
        if ($row["parent_visite_id"] != null){
            $subId = "subvisite" . $cpt;
            $res .= "<div><a class = \"expandButton\" style=\"float:left;\" data-toggle=\"collapse\" data-target=\"#" . $subId . "\"><span class=\"glyphicon glyphicon-expand\"></span></a>" . "<span class=\"parentVisite\">" . $row["titre"] . " - " . $inf . "<span hidden>" . $row["visiteId"] . "</span></span></div>";
            $sql = "SELECT ag_visite.titre, ag_visite.infirmière_id, ag_visite.id as visiteId, parent_visite_id, ag_infirmière.nom AS inf_nom, ag_infirmière.id AS infId, ag_visite.date 
                    FROM ag_visite
                    LEFT JOIN ag_infirmière on ag_visite.infirmière_id = ag_infirmière.id
                    WHERE (parent_visite_id = " . $row["visiteId"] . ") AND (ag_visite.id != parent_visite_id) ORDER BY ag_visite.date";

            $res .= "<div id = $subId>"; 
            $subresult = $conn->query($sql);
            while($row2 = $subresult->fetch_assoc()) {
                $inf = $row2["inf_nom"];
                if ($row2['infirmière_id'] == '999')
                    $inf = "pas affectée";
                $res .= "<div><span class=\"visite tabbedVisite\">" . dateFr($row2["date"]) . " - " . $row2["titre"] . " - " . $inf . "<span hidden>" . $row2["visiteId"] . "</span></div>";
            }
            $res .= "</div>";
            $cpt++;
        } 
        else {
            $res .= "<div><span class=\"visite\">" . dateFr($row["date"]) . " - " . $row["titre"] . " - " . $row["inf_nom"] . "<span hidden>" . $row["visiteId"] . "</span></div>";
        }
    }
} 

$conn->close();
return $res;
}

if (isset($_POST['clientId'])){
    $clientId = filter_input(INPUT_POST, 'clientId', FILTER_SANITIZE_NUMBER_INT);
    echo getClientVisites($clientId);
}
else 
    echo "missing arguments for getAgenda";

?>
