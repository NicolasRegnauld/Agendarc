<?php
session_start();
if (!isset($_SESSION['erreurmsg']))
    $_SESSION["erreurmsg"] = "";
        
function checkError(){
    if ($_SESSION["erreurmsg"] != ""){
        echo "alert('Erreur: ' + '" . $_SESSION["erreurmsg"] ."');";
        $_SESSION["erreurmsg"] = "";
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
                <?php checkError() ?>
                $("#topMenu").load("topMenu.html");
                $('#login-form-link').click(function (e) {
                    $("#login-form").delay(100).fadeIn(100);
                    $("#register-form").fadeOut(100);
                    $('#register-form-link').removeClass('active');
                    $(this).addClass('active');
                    e.preventDefault();
                });
                $('#register-form-link').click(function (e) {
                    $("#register-form").delay(100).fadeIn(100);
                    $("#login-form").fadeOut(100);
                    $('#login-form-link').removeClass('active');
                    $(this).addClass('active');
                    e.preventDefault();
                });
                $("#forgot-password").click(function (e){
                    $("#login-registration-panel").hide();
                    $("#recover-password-panel").show();
                    e.preventDefault();
                });
                $("#btn-reinitial").click(function(e){
                    console.log("trigger");
                    e.preventDefault();

                    $user = $("#login-username").val();
                    $.ajax({
                        url : "requestResetPasswordLink.php",
                        type: 'post',
                        data: {ident_mail: $user},
                        success: function (data) {
                            if (data === 'success') {
                                console.log("retour php: " + data);
                                alert("Un email a été envoyé, suivez les instructions qu'il contient pour réinitialiser votre mot de passe")
                            } 
                            else {
                                console.log("Erreur: " + data);
                                alert("Erreur: " + data);
                            }
                        },
                        error: function(xhr, ajaxOptions, thrownError) {
                            if(thrownError == 'abort' || thrownError == 'undefined') return;
                            alert("Probleme! " + thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                        }
                
                    });
                    console.log("sortie");

                });
            });
        </script>
    </head>
    <body>
        <div id="topMenu"></div>
        <div class="container" id="login-registration-panel">
            <div class="row">
                <div class="col-md-6 col-md-offset-3">
                    <div class="panel panel-login">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-xs-6">
                                    <a href="#" class="active" id="login-form-link">Login</a>
                                </div>
                                <div class="col-xs-6">
                                    <a href="#" id="register-form-link">Register</a>
                                </div>
                            </div>
                            <hr>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-lg-12">
                                    <form id="login-form" action="process_login.php" method="post" role="form" style="display: block;">
                                        <div class="form-group">
                                            <input type="text" name="username" id="username2" tabindex="1" class="form-control" placeholder="Username" value="">
                                        </div>
                                        <div class="form-group">
                                            <input type="password" name="password" id="password2" tabindex="2" class="form-control" placeholder="Password">
                                        </div>
                                        <div class="form-group text-center">
                                            <input type="checkbox" tabindex="3" class="" name="remember" id="remember">
                                            <label for="remember"> Remember Me</label>
                                        </div>
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-sm-6 col-sm-offset-3">
                                                    <input type="submit" name="login-submit" id="login-submit" tabindex="4" class="form-control btn btn-login" value="Log In">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <div class="text-center">
                                                        <a href="" tabindex="5" id="forgot-password" class="forgot-password">Forgot Password?</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                    <form id="register-form" action="register_groupement.php" method="post" role="form" style="display: none;">

                                        <div class="form-group">
                                            <input type="text" name="groupement" id="groupement" tabindex="1" class="form-control" placeholder="Nom du groupement" value="">
                                        </div>
                                        <div class="form-group">
                                            <input type="email" name="email" id="email" tabindex="1" class="form-control" placeholder="Email Address" value="">
                                        </div>
                                        <div class="form-group">
                                            <input type="text" name="nom" id="nom" tabindex="1" class="form-control" placeholder="Nom" value="">
                                        </div>
                                        <div class="form-group">
                                            <input type="text" name="prenom" id="prenom" tabindex="1" class="form-control" placeholder="Prénom" value="">
                                        </div>
                                        <div class="form-group">
                                            <input type="text" name="username" id="username" tabindex="1" class="form-control" placeholder="Identifiant" value="">
                                        </div>
                                        <div class="form-group">
                                            <input type="password" name="password" id="password" tabindex="2" class="form-control" placeholder="Mot de passe">
                                        </div>
                                        <div class="form-group">
                                            <input type="password" name="confirmPassword" id="confirm-password" tabindex="2" class="form-control" placeholder="Confirm Password">
                                        </div>
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-sm-6 col-sm-offset-3">
                                                    <input type="submit" name="register-submit" id="register-submit" tabindex="4" class="form-control btn btn-register" value="Register Now">
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div hidden class="container" id="recover-password-panel">    
            <div id="loginbox" style="margin-top:50px;" class="mainbox col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">                    
                <div class="panel panel-info" >
                    <div class="panel-heading">
                        <div class="panel-title">Réinitialiser mon mot de passe</div>
                    </div>     
                    <div style="padding-top:30px" class="panel-body" >
                        <div style="display:none" id="login-alert" class="alert alert-danger col-sm-12"></div>
                        <form id="loginform" class="form-horizontal" role="form">
                            <div style="margin-bottom: 25px" class="input-group">
                                <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                                <input id="login-username" type="text" class="form-control" name="username" value="" placeholder="username or email">                                        
                            </div>
                            <div style="margin-top:10px" class="form-group">
                                <!-- Button -->
                                <div class="col-sm-12 controls" style="weidth:500;">
                                    <a id="btn-reinitial" href="" class="btn btn-success col-sm-12 ">Réinitialiser</a>
                                </div>
                            </div>
                        </form>     
                    </div>                     
                </div>  
            </div>
        </div>
    </body>
</html>



