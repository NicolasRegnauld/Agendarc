<?php
function chargerClasse($classname)
{
  require $classname.'.class.php';
}

spl_autoload_register('chargerClasse');
session_start();
include_once 'connexion.php';
$db = getPDO();
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
$db->query("SET NAMES 'utf8'");
$manager = new CompteManager($db);

function register_gr($manager, $groupement, $email, $identifiant, $pass, $nom, $prenom){
$connectDetails = getConnectionDetails();
// Create connection
$conn = new mysqli($connectDetails[0], $connectDetails[1], $connectDetails[2], $connectDetails[3]);
$conn->query('SET NAMES utf8');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

$sql = "SELECT identifiant from ag_infirmière WHERE identifiant = '" . $identifiant . "'";
$result = $conn->query($sql);

  if ($result->num_rows > 0) {
      echo "Une infirmière est déjà enregistrée avec l'identifiant " . $identifiant . ". Choisissez en un autre.";
  }
  else
  {
        $sql = "SELECT libellé from ag_compte WHERE libellé = '" . $groupement . "'";
        $result = $conn->query($sql);
        if ($manager->exists($groupement)) {
            echo "Ce goupement existe déjà, utilisez un nom différent, ou connectez-vous au groupement existant";
        }
        else {
            $dateFin = (new DateTime('today +1 months'))->format("Y-m-d");
            $compte = new Compte(['libellé' => $groupement, 'nbUsers' => 5, 'dateFin' => $dateFin]);
            $manager->add($compte);            
            $grId = $compte->id();	
            $hash = password_hash($pass, PASSWORD_DEFAULT);

             // Store the hash somewhere such as a database
            // The code for that is up to you as this tutorial only focuses on hashing passwords
            $vals = '"' . $identifiant . '","' . $hash . '","' . $email . '","' . $grId . '","' . $nom . '","' . $prenom . '"';
            $sql = "INSERT INTO ag_infirmière (identifiant, password, email, compte, nom, prénom) VALUES ($vals)";
            if ($conn->query($sql) === TRUE) {
                echo "Nouveau compte (groupement) et nouvelle infirmère créés avec succès";
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }

 
                    
                
            
        }
  }

$conn->close();

}

if (isset($_POST['groupement'])&&
    isset($_POST['email'])&&    
    isset($_POST['username'])&&
    isset($_POST['password'])&&
    isset($_POST['confirmPassword'])&&
    isset($_POST['nom'])&&
    isset($_POST['prenom'])){
    $groupement = filter_input(INPUT_POST, 'groupement');
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $identifiant = filter_input(INPUT_POST, 'username');
    $pass = filter_input(INPUT_POST, 'password');
    $pass2 = filter_input(INPUT_POST, 'confirmPassword');
    $nom = filter_input(INPUT_POST, 'nom');
    $prenom = filter_input(INPUT_POST, 'prenom');
    if (filter_var($email, FILTER_VALIDATE_EMAIL)){
        if ($pass === $pass2){
            echo register_gr($manager, $groupement, $email, $identifiant, $pass, $nom, $prenom);
        }
        else {
            echo "Erreur: les deux mots de passe ne sont pas identiues";
        }
    }  
     else {
        echo "Erreur: l'adresse email est invalide";
    }        
}
else 
    echo "missing arguments for registration";
?>