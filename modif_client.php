
<?php
// Connect to our database (Step 2a)
include_once 'connexion.php';

function modifClient($nom,$prénom,$statut, $tel_fixe, $adresse){
$connectDetails = getConnectionDetails();
// Create connection
$conn = new mysqli($connectDetails[0], $connectDetails[1], $connectDetails[2], $connectDetails[3]);
$conn->query('SET NAMES utf8');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

$vals = "nom='" . $nom . "', prénom='" . $prénom . "', statut='" . $statut. "', adresse='" . $adresse . "', tel_fixe='" . $tel_fixe . "'";

$sql = "UPDATE ag_client SET " . $vals . "WHERE id = '" . $_POST["id"] . "'";


if ($conn->query($sql) === TRUE) {
    $res =  "Client modifié avec success";
} else {
    $res =  "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
return $res;
}

if (isset($_POST['nom'])&&
    isset($_POST['prénom'])&&
    isset($_POST['tel_fixe'])&&
    isset($_POST['adresse'])&&
    isset($_POST['statut'])){
    $nom = filter_input(INPUT_POST, 'nom');
    $prénom = filter_input(INPUT_POST, 'prénom');
    $statut = filter_input(INPUT_POST, 'statut');
    $tel_fixe = filter_input(INPUT_POST, 'tel_fixe');
    $adresse = filter_input(INPUT_POST, 'adresse');
    
    echo modifClient($nom,$prénom,$statut, $tel_fixe, $adresse);
}
else 
    echo "missing arguments for modif client";

?>
