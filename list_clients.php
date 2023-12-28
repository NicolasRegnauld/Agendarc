<?php
session_start();
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


if ($_POST['filtre'] == 'tous')
    $sql = "SELECT id, nom, prénom FROM ag_client where compte = '" . $_SESSION["compte"] . "' ORDER BY nom, prénom";
else
    $sql = "SELECT id, nom, prénom FROM ag_client where compte = '" . $_SESSION["compte"] . "' and statut = '" . $_POST['filtre'] . "' ORDER BY nom, prénom";
$result = $conn->query($sql);


if ($result->num_rows > 0) {
    // output data of each row
	
    $res = "";
    while($row = $result->fetch_assoc()) {
//        $res .= "<tr><td class=\"client\">" . $row["nom"] . " " . $row["prénom"] . "<span hidden>" . $row["id"] . "</span>" . "</td></tr>";
 
        $res .= "<tr><td><div >" . 
		"<button type=\"button\" class=\"toVisitesButton btn btn-default btn-xs glyphicon glyphicon-briefcase\" style=\"float:left\"></button>&nbsp;" . 
                "<span hidden>" . $row["id"] . "</span><span class=\"client\">" . $row["nom"] . " " . $row["prénom"] . "<span hidden>" . $row["id"] . "</span></span>" . 
                "</div></td></tr>";

//                $res .= "<tr><td><div class=\"row\"><div class=\"col-xs-10 client\">" . $row["nom"] . " " . $row["prénom"] . "<span hidden>" . $row["id"] . "</span>" ."</div>" . 
//		        "<div class=\"col-xs-2\"><button type=\"button\" class=\"toVisitesButton btn btn-default btn-xs glyphicon glyphicon-briefcase\" style=\"float:right\"></button>" . 
//				"<span hidden>" . $row["id"] . "</span>" . "</div></div></td></tr>";

    } 
	echo($res);
} else {
    echo "0 results";
}

$conn->close();

?>
