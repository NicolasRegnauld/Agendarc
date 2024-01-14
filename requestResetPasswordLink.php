<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

include_once 'connexion.php';

require 'PHPMailer-master/src/Exception.php';
require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';



function generateRandomString($length = 20) {

    // This function has taken from stackoverflow.com
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return md5($randomString);
}


function send_mail($to, $token) {
    $mail = new PHPMailer(TRUE);
    $mail->isSMTP();
    $mail->Host = 'smtp.ionos.fr';
    $mail->SMTPAuth = true;
    $mail->Username = "support@agendarc.com";
    $mail->Password = "jd7Ui9(re3";
    $mail->SMTPSecure = "ssl";

    $mail->Port = 465;
    $mail->CharSet="UTF-8";
    $mail->Encoding = 'base64';
    $mail->SMTPOptions = array(
    'ssl' => array(
        'verify_peer' => false,
        'verify_peer_name' => false,
        'allow_self_signed' => true)
        );
    $mail->setFrom('support@agendarc.com', 'Support Agendarc');
    $mail->addAddress($to);
    $mail->Subject = 'Réinitialisation de votre mot de passe';
//    $link = 'http://agendarcresetbug/Agendarc/resetpass.php?email='.$to.'&token='.$token;
    $link = 'http://agendarc.com/fr/resetpass.php?email='.$to.'&token='.$token;
    $mail->Body = "Bonjour. Vous avez demander la réinitialisation de votre mot de passe. Suivez ce lien pour réinitiliser votre mot de passe: $link. Si le lien ne fonctionne pas, copier l'adresse dans votre navigateur.";

    if(!$mail->send()) {
        return $mail->ErrorInfo;
    } 
    else {
        return "success";
    }
}



function sendResetEmail($ident_email) {
    $connectDetails = getConnectionDetails();
// Create connection
$conn = new mysqli($connectDetails[0], $connectDetails[1], $connectDetails[2], $connectDetails[3]);
$conn->query('SET NAMES utf8');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
    
    $sql = "SELECT identifiant, email from ag_infirmière WHERE identifiant = '" . $ident_email . "' OR email = '" . $ident_email . "'";
    $result = $conn->query($sql);
    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $mailToAdr = $row["email"];
        $token = generateRandomString();
        
        $query = "UPDATE ag_infirmière SET token = '$token', expires = NOW() + INTERVAL 24 HOUR WHERE identifiant = '" . $row["identifiant"] . "'" ;
        if ($conn->query($query) === TRUE) {
            
            $send_mail = send_mail($mailToAdr, $token);
            if ($send_mail === 'success') {
                return "success";
            }
            else
            {
                return "un problème est survenu pendant l'envoi de l'email 01: " . $send_mail;
            }
        }
        else  {
            return "un problème est survenu pendant l'envoi de l'email2";
        }
    } else  {
        return "utilisateur inconnu";
    }
}

    
if (isset($_POST['ident_mail'])){
    $ident_email = filter_input(INPUT_POST, 'ident_mail');
    echo sendResetEmail($ident_email);
}
else{ 
    echo "missing argument for resetting password";
}
?>