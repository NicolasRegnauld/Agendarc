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
    $sql = "SELECT id, nom, prénom FROM ag_infirmière where compte = '" . $_SESSION["compte"] . "'";
else
    $sql = "SELECT id, nom, prénom FROM ag_infirmière where compte = '" . $_SESSION["compte"] . "' and statut = '" . $_POST['filtre'] . "'";

$result = $conn->query($sql);


if ($result->num_rows > 0) {
    // output data of each row
	
    $res = "";
	while($row = $result->fetch_assoc()) {
        $res .= "<tr><td><div><button type=\"button\" class=\"dispoInfirmière btn btn-default btn-xs glyphicon glyphicon-calendar\" style=\"float:left\"></button>&nbsp;" . 
                "<span class=\"infirmière\">" . $row["nom"] . " " . $row["prénom"] . "<span hidden>" . $row["id"] . "</span></span>". 
				"<span hidden>" . $row["id"] . "</span>" . "</div></td></tr>";
        
//                $res .= "<tr><td><div class=\"row\"><div class=\"col-xs-10 infirmière\">" . $row["nom"] . " " . $row["prénom"] . "<span hidden>" . $row["id"] . "</span>" ."</div>". 
//		        "<div class=\"col-xs-2\"><button type=\"button\" class=\"dispoInfirmière btn btn-default btn-xs glyphicon glyphicon-calendar\" style=\"float:left\"></button>" . 
//				"<span hidden>" . $row["id"] . "</span>" . "</div></div></td></tr>";
    } 
	echo($res);
} else {
    echo "0 results";
}

$conn->close();

?>
