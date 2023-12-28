<?php
// Connect to our database (Step 2a)
include_once 'connexion.php';

function modif_infirmiere($id, $nom, $prénom, $statut, $identifiant, $email, $tel_fixe, $tel_portable, $adresse, $notes){
	$connectDetails = getConnectionDetails();
	// Create connection
	$conn = new mysqli($connectDetails[0], $connectDetails[1], $connectDetails[2], $connectDetails[3]);
	$conn->query('SET NAMES utf8');

        $res = array();
	// Check connection
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	} 

        $sql = "SELECT identifiant from ag_infirmière WHERE identifiant = '" . $identifiant . "' AND id != '" . $id . "'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $res["statut"] = "abort";
            $res["message"] =  "Modification impossible, une autre infirmière est déjà enregistrée avec cet identifiant (" . $identifiant . "). Choisissez en un autre.";
        }
        else {    
            $vals = "nom='" . $nom . "', prénom='" . $prénom . "', statut='" . $statut  . "', identifiant='" . $identifiant . "', email='" . $email . "', adresse='" . $adresse . 
            "', tel_fixe='" . $tel_fixe . "', tel_portable='" . $tel_portable . "', notes='" . $notes . "'";

            $sql = "UPDATE ag_infirmière SET " . $vals . "WHERE id = '" . $id . "'";

            if ($conn->query($sql) === TRUE) {
                $res["statut"] = "success";
                $res["message"] = "Infirmière modifiée avec success test ";
            } else {
                $res["statut"] = "failed";
                $res["message"] = "Error: " . $sql . "<br>" . $conn->error;
            }
        }

	$conn->close();
	return $res;
}


$res = array();
if (isset($_POST['id'])&&
    isset($_POST['nom'])&&
    isset($_POST['prénom'])&&
    isset($_POST['statut'])&&
    isset($_POST['identifiant'])&&
    isset($_POST['email'])&&    
    isset($_POST['tel_fixe'])&&
    isset($_POST['tel_portable'])&&
    isset($_POST['adresse'])&&
    isset($_POST['notes'])){
		
    $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
    $nom = filter_input(INPUT_POST, 'nom', FILTER_SANITIZE_STRING);
    $prénom = filter_input(INPUT_POST, 'prénom', FILTER_SANITIZE_STRING);
    $statut = filter_input(INPUT_POST, 'statut', FILTER_SANITIZE_STRING);
    $identifiant = filter_input(INPUT_POST, 'identifiant', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $tel_fixe = filter_input(INPUT_POST, 'tel_fixe', FILTER_SANITIZE_STRING);
    $tel_portable = filter_input(INPUT_POST, 'tel_portable', FILTER_SANITIZE_STRING);
    $adresse = filter_input(INPUT_POST, 'adresse', FILTER_SANITIZE_STRING);
    $notes = filter_input(INPUT_POST, 'notes', FILTER_SANITIZE_STRING);

    if ($nom == ""){
         $res["message"] =  "Impossible d'effectuer les modifications, le champ 'nom' ne peut pas être vide";
         $res["statut"] = "abort";
    }
    else if ($identifiant == ""){
        $res["message"] =  "Impossible d'effectuer les modifications, le champ 'identifiant' ne peut pas être vide";
        $res["statut"] = "abort";
    } 
    else if (filter_var($email, FILTER_VALIDATE_EMAIL)){
        $res = modif_infirmiere($id, $nom, $prénom, $statut, $identifiant, $email, $tel_fixe, $tel_portable, $adresse, $notes);
    }  
    else {
        $res["message"] =  "Impossible d'effectuer les modifications: l'adresse email est invalide";
        $res["statut"] = "abort";
    }        
}
else {
    $res["message"] =  "Erreur: problème d'arguments avec la fonction de modification d'une infirmière";
    $res["statut"] = "failed";
}      

echo json_encode($res);

?>
