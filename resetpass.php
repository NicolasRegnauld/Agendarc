<?php
session_start();
include_once 'connexion.php';
require("PasswordHash.php");

$connectDetails = getConnectionDetails();
// Create connection
$conn = new mysqli($connectDetails[0], $connectDetails[1], $connectDetails[2], $connectDetails[3]);
$conn->query('SET NAMES utf8');

function clearOldTokens($conn){
    $query = "UPDATE ag_infirmière SET token = NULL WHERE expires < NOW()" ;
    if ($conn->query($query) === TRUE) {
        return "success";
    }
    else {
        return "failed to remove old tokens";
    }
}


    
        

function getPassForm($id){
    $formString =  '<html><head></head><body>' . 
                    '<div id="loginbox" style="margin-top:50px;" class="mainbox col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">' .                    
                        '<div class="panel panel-info" >'.
                            '<div class="panel-heading">'.
                                '<div class="panel-title">Nouveau mot de passe</div>'.
                            '</div>'.     
                            '<div style="padding-top:30px" class="panel-body" >'.
                                '<div style="display:none" id="login-alert" class="alert alert-danger col-sm-12"></div>'.
                                '<form id="loginform" class="form-horizontal" role="form">'.
                                    '<div style="margin-bottom: 25px" class="input-group">'.
                                        '<span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>'.
                                        '<input id="password01" type="password" class="form-control" name="password01" value="" placeholder="nouveau mot de passe">'.                                        
                                    '</div>'.
                                    '<div style="margin-bottom: 25px" class="input-group">'.
                                        '<span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>'.
                                        '<input id="password02" type="password" class="form-control" name="password02" value="" placeholder="Confirmez votre mot de passe">'.                                     
                                    '</div>'.
                                    '<div style="margin-top:10px" class="form-group">'.
                                        '<!-- Button -->'.
                                        '<div class="col-sm-12 controls" style="weidth:500;">'.
                                            '<a id="btn-submit-pass" href="" class="btn btn-success col-sm-12 ">Envoyer</a>'.
                                        '</div>'.
                                    '</div>'.
                                    '<input id="changeid" hidden type="text" name="id" value="'. $id . '">'.                  
                                '</form>'.
                            '</div>'.                     
                        '</div>'.  
                    '</div>' . 
                '</body></html>';
    return $formString;
}

if (isset($_GET['token']) && isset($_GET['email']))
{
    $email = $_GET['email'];
    $token = $_GET['token'];
    
    clearOldTokens($conn);
    $sql = "SELECT identifiant, id from ag_infirmière WHERE email = '" . $email . "' AND token = '" . $token . "'";
    $result = $conn->query($sql);
    if ($result->num_rows == 1 ) {
        $row = $result->fetch_assoc();
        echo getPassForm($row['id']);
    }
    else {
        echo("probleme de token/email " . $sql . ", nb result: " . $result->num_rows);
    }
}



?>

<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Login</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
        <link rel="stylesheet" href="login_styles.css">

        <script>
            $(function () {
                $("#topMenu").load("topMenu.html");
                
          
                $("#btn-submit-pass").click(function(e){
                    console.log("trigger");
                    $pass1 = $("#password01").val();
                    $pass2 = $("#password02").val();
                    $id = $("#changeid").val();
                    $.ajax({
                        url : "newPass.php",
                        type: 'post',
                        data: {id: $id, 
                               password1: $pass1, 
                               password2: $pass2},
                        success: function (data, status) {
                            if (status === 'success') {
                                alert(data);
                                location.href = "login.php";
                            } 
                            else {
                                console.log("Problème d'enregistrement du mot de passe, status = " + status + ", data = " + data);
                            }
                        },
                        error: function(xhr, ajaxOptions, thrownError) {
                            if(thrownError == 'abort' || thrownError == 'undefined') return;
                            alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                        }
                
                    });
                    e.preventDefault();

                });
            });
        </script>
    </head>
</html>


