<?php
session_start();
// Connect to our database (Step 2a)
include_once 'connexion.php';
require("PasswordHash.php");


function add_infirmiere($nom, $prénom, $statut, $identifiant, $email, $tel_fixe, $tel_portable, $adresse, $notes){
    $connectDetails = getConnectionDetails();
    // Create connection
    $conn = new mysqli($connectDetails[0], $connectDetails[1], $connectDetails[2], $connectDetails[3]);
    $conn->query('SET NAMES utf8');

    $msg["statut"] = "success";
    $msg["message"] = "";	// Check connection
    if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
    } 

    if ($identifiant == "")
    {
        $msg["message"] =  "Le champ 'identifiant' est vide, il doit impérativement être renseigné, choisissez en un (il doit être unique).";
        $msg["statut"] = "abort";
    } 
    else if ($nom == "")
    {
        $msg["message"] =  "Le champ 'nom' est vide, il doit impérativement être renseigné.";
        $msg["statut"] = "abort";
    }
    else {
        $sql = "SELECT identifiant from ag_infirmière WHERE identifiant = '" . $identifiant . "'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
                $msg["message"] =  "Une infirmière est déjà enregistrée avec l'identifiant " . $identifiant . ". Choisissez en un autre.";
                $msg["statut"] = "abort";
        }
        else {  
            $grId = $_SESSION["compte"];
            $vals = '"' . $identifiant . '","' . $email . '","' . $grId . '","' . $nom . '","' . $prénom . '","' . $statut . '","' . $tel_fixe. '","' . $tel_portable. '","' . $adresse. '","' . $notes . '"';
            $sql = "INSERT INTO ag_infirmière (identifiant, email, compte, nom, prénom, statut, tel_fixe, tel_portable, adresse, notes) VALUES ($vals)";
            if ($conn->query($sql) === TRUE) {
                $msg["message"] = "Nouvelle infirmère créés avec succès";
            } else {
                $msg["statut"] = "failed";
                $msg["message"] =  "Error: " . $sql . "<br>" . $conn->error;
            }
        }
    }

    $conn->close();
    return trim(json_encode($msg));
}

$res["statut"] = "success";
$res["message"] = "";

if (isset($_POST['nom'])&&
    isset($_POST['prénom'])&&
    isset($_POST['statut'])&&
    isset($_POST['identifiant'])&&
    isset($_POST['email'])&&    
    isset($_POST['tel_fixe'])&&
    isset($_POST['tel_portable'])&&
    isset($_POST['adresse'])&&
    isset($_POST['notes'])){
    $nom = filter_input(INPUT_POST, 'nom', FILTER_SANITIZE_STRING);
    $prénom = filter_input(INPUT_POST, 'prénom', FILTER_SANITIZE_STRING);
    $statut = filter_input(INPUT_POST, 'statut', FILTER_SANITIZE_STRING);
    $identifiant = filter_input(INPUT_POST, 'identifiant', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $tel_fixe = filter_input(INPUT_POST, 'tel_fixe', FILTER_SANITIZE_STRING);
    $tel_portable = filter_input(INPUT_POST, 'tel_portable', FILTER_SANITIZE_STRING);
    $adresse = filter_input(INPUT_POST, 'adresse', FILTER_SANITIZE_STRING);
    $notes = filter_input(INPUT_POST, 'notes', FILTER_SANITIZE_STRING);

    if (filter_var($email, FILTER_VALIDATE_EMAIL)){
        $res = add_infirmiere($nom, $prénom, $statut, $identifiant, $email, $tel_fixe, $tel_portable, $adresse, $notes);
        echo $res;
    }  
     else {
         $res["message"] =  "Erreur: l'adresse email est invalide";
         $res["statut"] = "abort";
         echo trim(json_encode($res));
    }  
    
}
else {
    $res["message"] =  "Erreur: problème d'arguments avec la fonction de création d'une nouvelle infirmière";
    $res["statut"] = "failed";
    echo trim(json_encode($res));
}      


    
?>
