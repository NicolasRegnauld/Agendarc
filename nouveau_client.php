<?php
session_start();
// Connect to our database (Step 2a)
include_once 'client_functions.php';



if (isset($_POST['nom'])&&
    isset($_POST['prénom'])&&    
    isset($_POST['tel_fixe'])&&
    isset($_POST['adresse'])){
        $nom = filter_input(INPUT_POST, 'nom');
        $prenom = filter_input(INPUT_POST, 'prénom');
        $adr = filter_input(INPUT_POST, 'adresse');
        $tel = filter_input(INPUT_POST, 'tel_fixe');
        $res = addClient($nom, $prenom, $adr, $tel);
        if (strstr($res, "Err"))
           echo $res;
        else
           echo "Nouveau client créé avec succès";
    }
 else     
    echo "missing arguments for nouveau_client";

?>
