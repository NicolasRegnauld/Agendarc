<?php
session_start();
include_once 'connexion.php';
require("PasswordHash.php");

$connectDetails = getConnectionDetails();
// Create connection
$conn = new mysqli($connectDetails[0], $connectDetails[1], $connectDetails[2], $connectDetails[3]);
$conn->query('SET NAMES utf8');

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
function storePass($conn, $id, $pass){
    $hash = password_hash($pass, PASSWORD_DEFAULT);

    $query = "UPDATE ag_infirmière SET token = NULL, expires = NULL, password = '" .$hash . "' WHERE id = " . $id;
    if ($conn->query($query) === TRUE) {               
        return "Votre mot de passe a été modifié"; 
    }
    else {
        return  "Un problème est survenu lors du changement de mot de passe, veuillez réessayer.";
    }      

}


if (isset($_POST['id'])&&
    isset($_POST['password1'])&&
    isset($_POST['password2'])){
    $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_STRING);
    $pass1 = filter_input(INPUT_POST, 'password1', FILTER_SANITIZE_STRING);
    $pass2 = filter_input(INPUT_POST, 'password2', FILTER_SANITIZE_STRING);
    if ($pass1 != $pass2)
        echo "les deux mots de passes sont différents, veuillez les ressaisir";
    else       
        echo storePass($conn, $id, $pass1);
    }