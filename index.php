<?php
function chargerClasse($classname)
{
  require_once $classname.'.class.php';
}
spl_autoload_register('chargerClasse');
session_start();
if(!isset($_SESSION['identifiant']))
{
   echo '<script> location.replace("login.php"); </script>';
}
?>
<html lang="fr">
    <head>
        <meta charset="utf-8"> 
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0"/>
        <title>Shared Agenda</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
        <script type="text/javascript" src="index.js"></script>
        <script type="text/javascript" src="boite_outils.js"></script>
        <script type="text/javascript" src="visiteModal.js"></script>
        <script type="text/javascript" src="messagesModal.js"></script>
        <link rel="stylesheet" href="general_styles.css">
        <link rel="icon" href="data:,">
        <style>

            .Nom2.H1015{
                background-color : red !important; 
            }

            .dateBar{
                background-color: Gainsboro ;
                height: 50px;
                padding-top: 12px;
            }

            .agendaHeader{
                position:sticky;
                background-color:#FAFAFA;
                top: 0px;
                width:100%;
                z-index: 200;	
            }
            .tableHeader{
                top: 150px;
                width:100%;
                background-color: Gainsboro ;
            }

            .hover{
                background-color: yellow !important;
            }


            div.agendaGoTop {
                position: fixed;
                bottom: 0;
                right: 0;
                width: 50px;
            }

            .h-divider{
                margin-top:5px;
                margin-bottom:5px;
                height:1px;
                width:100%;
                border-top:1px solid gray;
            }
        </style>
    </head>
    <body>


        <div class=" agendaHeader">

            <div id="topMenu"></div>

            <div>
                <div class = "row dateBar">
                    <div class="col-xs-3"></div>
                    <div class="col-xs-1 glyphicon glyphicon-circle-arrow-left" id="prevDay" align="right" style="padding-top:4px"></div>
                    <div class="col-xs-4" align="center">
                        <input id="calendar" type="date" name="newDate">
                    </div>
                    <div class="col-xs-1 glyphicon glyphicon-circle-arrow-right" id="nextDay" align="left" style="padding-top:4px"></div>
                    <div class="col-xs-3"></div>
                </div>

                <p class="selBoxLine" id="selectAgenda"> This is where the agenda selection goes </p>
                <p class="selBoxLine" id="selectTournee"> This is where the tournée selection goes </p>
                <div id="agendaTableHeader"></div>
            </div>
            
        </div>

        <div class = "agenda-table"> 
            <div id="agendaTableContent"> This is where the agenda goes 
            </div>
            
        </div>
        <div class="agendaGoTop">
                <button type="button" onclick="scrolltopFunction()" class="btn"><span class="glyphicon glyphicon-arrow-up"></span></button>
            </div>
        <div id="espaceModalStatut"></div>
        <div id="espaceModalModif"></div>
        <div id="espaceModalCreate"></div>
        <div id="espaceModalMessage"></div>

        
        
</body>
</html>