<?php
function chargerClasse($classname)
{
  require $classname.'.class.php';
}
spl_autoload_register('chargerClasse');
session_start();
error_reporting(E_ALL);
include_once 'connexion.php';
function login_inf($identifiant, $pass){
$connectDetails = getConnectionDetails();
// Create connection
$conn = new mysqli($connectDetails[0], $connectDetails[1], $connectDetails[2], $connectDetails[3]);
$conn->query('SET NAMES utf8');

// Check connection
if (mysqli_connect_errno()) {
    die('<p>La connexion au serveur MySQL a échoué: '.mysqli_connect_error().'</p>');
} 

$sql = "SELECT id, identifiant, password, compte, statut FROM ag_infirmière WHERE identifiant = '$identifiant'";
$result = $conn->query($sql);

  if ($result->num_rows == 1) {
    $row = $result->fetch_assoc();
    
     if (!password_verify($pass, $row["password"])){
        $_SESSION["erreurmsg"] = "connection refusée, combinaison identifiant/mot de passe invalide";
        header("Location: login.php");    
        die(); 
     }
     else if ($row["statut"] == 'inactif') {
        $_SESSION["erreurmsg"] = "connection refusée, votre compte est désactivé, contactez votre administrateur";
        header("Location: login.php");    
        die(); 
     }
     else {
        $infId = $row["id"];
        $cptId = $row["compte"];
        $_SESSION["identifiant"] = $identifiant;
        $_SESSION["compte"] = $cptId;
        $db = getPDO();
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
        $db->query("SET NAMES 'utf8'");
        $logManager = new LogManager($db);
        if ($logManager->exists($infId, $cptId)){
        }
        else {
            $log = new Log(['infId' => $infId, 'cptId' => $cptId]);
            $logManager->add($log);    
        }
        header("Location: index.php"); 
        die();
     }
  }
  else {
    $_SESSION["erreurmsg"] = "connection refusée, combinaison identifiant/mot de passe invalide.";
    header("Location: login.php");    
    die();   
  }

$conn->close();

}
if (isset($_POST['username'])&&
    isset($_POST['password'])){
    $identifiant = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $pass = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
    login_inf($identifiant, $pass);
}
else 
    echo "missing arguments for login";
?>