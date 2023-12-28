<?php
// Connect to our database (Step 2a)
include_once 'connexion.php';

function getSelected($str1, $str2){
    if ($str1 == $str2)
        return "selected";
    else
        return "";
}

function showInfirmière ($infId){
$connectDetails = getConnectionDetails();
// Create connection
$conn = new mysqli($connectDetails[0], $connectDetails[1], $connectDetails[2], $connectDetails[3]);
$conn->query('SET NAMES utf8');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

$sql = "SELECT id, nom, prénom, statut, identifiant, email, adresse, tel_fixe, tel_portable, notes FROM ag_infirmière WHERE id=" . $infId;
$result = $conn->query($sql);

if ($result->num_rows == 1) {
    // output data of each row
	
    $res = "";
	while($row = $result->fetch_assoc()) {	

  	  $res = "<form id=\"modifInfirmièreForm\" action=\"modif_infirmiere.php\" method=\"post\"><div class=\"form-group\">" .
		"<label for=\"nom\">Nom:</label>" .
        "<input name = \"nom\" type=\"text\" class=\"form-control infirmièreData\" id=\"nom\" value=\"" . $row["nom"] . "\">" .
        "</div>" .
        "<div class=\"form-group\">" .
        "<label for=\"prénom\">Prénom:</label>" .
        "<input name = \"prénom\" type=\"text\" class=\"form-control infirmièreData\" id=\"prénom\" value=\"" . $row["prénom"] . "\">" .
        "</div>" .
                  
        "<div class=\"form-group\">" .
        "<label for=\"statut\">Statut</label>" .
        "<select class=\"form-control infirmièreDataSelect\" name=\"statut\" id=\"statut\">" .
            "<option " . getSelected($row["statut"], 'actif') . "> actif</option>" .
            "<option " . getSelected($row["statut"], 'inactif') . "> inactif</option>" .
        "</select>" .
        "</div>" .
        "<div class=\"form-group\">" .
        "<label for=\"identifiant\">Identifiant:</label>" .
        "<input name = \"identifiant\" type=\"text\" class=\"form-control infirmièreData\" id=\"identifiant\" value=\"" . $row["identifiant"] . "\">" .
        "</div>" .        
	    "<div class=\"form-group\">" .
        "<label for=\"email\">Email:</label>" .
        "<input name = \"email\" type=\"email\" class=\"form-control infirmièreData\" id=\"email\" value=\"" . $row["email"] . "\">" .
        "</div>" .
        "<div class=\"form-group\">" .
        "<label for=\"adresse\">Adresse:</label>" .
	    "<input name = \"adresse\" type=\"text\" class=\"form-control infirmièreData\" id=\"adresse\" value=\"" . $row["adresse"] . "\">" .
        "</div>" .
		"<div class=\"form-group\">" .
        "<label for=\"tel_fixe\">Tel. fixe:</label>" .
	    "<input name = \"tel_fixe\" type=\"text\" class=\"form-control infirmièreData\" id=\"tel_fixe\" value=\"" . $row["tel_fixe"] . "\">" .
        "</div>" .
		"<div class=\"form-group\">" .
        "<label for=\"tel_portable\">Tel. portable:</label>" .
	    "<input name = \"tel_portable\" type=\"text\" class=\"form-control infirmièreData\" id=\"tel_portable\" value=\"" . $row["tel_portable"] . "\">" .
        "</div>" .
		"<div class=\"form-group\">" .
        "<label for=\"notes\">Notes:</label>" .
	    "<textarea name = \"notes\" class=\"form-control infirmièreData\" id=\"notes\">" . $row["notes"] . "</textarea>" .
        "</div>" .
		"<div class=\"form-group\">" .
        "<label class=\"hidden\" for=\"infirmièreId\">ID:</label>" .
	    "<input name = \"id\" type=\"text\" class=\"form-control hidden infirmièreData\" id=\"infirmièreId\" value=\"" . $row["id"] . "\">" .
        "</div>" .
        "<button id=\"modifButton\" type=\"submit\" disabled=\"disabled\" class=\"btn btn-default disabled\">Modifier</button>" .
		"<button id=\"deleteButton\" type=\"button\" style=\"float:right\" class=\"btn btn-danger\">Supprimer</button>" .
        "</form>";
	 } 
    } else {
        $res = "0 results";
    }     

$conn->close();
return $res;
}


if (isset($_POST["infirmièreString"])) {
    $infId = filter_input(INPUT_POST, 'infirmièreString', FILTER_SANITIZE_NUMBER_INT);
    echo showInfirmière($infId);
}
else {
    echo "missing arguments for show_infirmière";
}
?>
