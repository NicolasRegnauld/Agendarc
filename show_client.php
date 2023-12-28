<?php
// Connect to our database (Step 2a)
include_once 'connexion.php';

function getSelected($str1, $str2){
    if ($str1 == $str2)
        return "selected";
    else
        return "";
}
        
function showClient($clientId){
$connectDetails = getConnectionDetails();
// Create connection
$conn = new mysqli($connectDetails[0], $connectDetails[1], $connectDetails[2], $connectDetails[3]);
$conn->query('SET NAMES utf8');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 



$marker = '<span hidden="">';

$sql = "SELECT id, nom, prénom, adresse, tel_fixe, statut FROM ag_client WHERE id=" . $clientId;
$result = $conn->query($sql);


if ($result->num_rows == 1) {
    // output data of each row
	
    $res = "";
	while($row = $result->fetch_assoc()) {	
  	  $res = "<form id=\"modifClientForm\" action=\"modif_client.php\" method=\"post\"><div class=\"form-group\">" .
		"<label for=\"nom\">Nom:</label>" .
        "<input name = \"nom\" type=\"text\" class=\"form-control clientData\" id=\"nom\" value=\"" . $row["nom"] . "\">" .
        "</div>" .
        "<div class=\"form-group\">" .
        "<label for=\"prénom\">Prénom:</label>" .
        "<input name = \"prénom\" type=\"text\" class=\"form-control clientData\" id=\"prénom\" value=\"" . $row["prénom"] . "\">" .
        "</div>" .
        "<div class=\"form-group\">" .
        "<label for=\"statut\">Statut:</label>" .
        "<select class=\"form-control clientData\" name=\"statut\" id=\"statut\">" . 
            "<option " . getSelected($row["statut"], 'actif') . ">actif</option>" .
            "<option " . getSelected($row["statut"], 'inactif') . ">inactif</option>" .
        "</select>" .
        "</div>" .
        "<div class=\"form-group\">" .
        "<label for=\"adresse\">Adresse:</label>" .
	    "<input name = \"adresse\" type=\"text\" class=\"form-control clientData\" id=\"adresse\" value=\"" . $row["adresse"] . "\">" .
        "</div>" .
		"<div class=\"form-group\">" .
        "<label for=\"tel_fixe\">Tel:</label>" .
	    "<input name = \"tel_fixe\" type=\"text\" class=\"form-control clientData\" id=\"tel_fixe\" value=\"" . $row["tel_fixe"] . "\">" .
        "</div>" .
		"<div class=\"form-group\">" .
        "<label class=\"hidden\" for=\"clientId\">ID:</label>" .
	    "<input name = \"id\" type=\"text\" class=\"form-control hidden clientData\" id=\"clientId\" value=\"" . $row["id"] . "\">" .
        "</div>" .
        "<button id=\"modifButton\" type=\"submit\" class=\"btn btn-default disabled\">Modifier</button>" .
		"<button id=\"deleteButton\" type=\"button\" style=\"float:right\" class=\"btn btn-danger\">Supprimer</button>" .
        "</form>";
	 } 

    } else {
    $res = "0 results";
    }     

$conn->close();
return $res;

}

if (isset($_POST["clientId"])) {
    $clientId = filter_input(INPUT_POST, 'clientId', FILTER_SANITIZE_NUMBER_INT);
    echo showClient($clientId);
}
else {
    echo "missing arguments for show_client";
}
?>
