<?php
session_start();
if(!isset($_SESSION['identifiant']))
{
   echo '<script> location.replace("login.php"); </script>';
}
// test
?>
<html lang="fr">
    <head>
        <meta charset="utf-8"> 
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
        <script type="text/javascript" src="boite_outils.js"></script>
        <script type="text/javascript" src="messagesModal.js"></script>
        <link rel="stylesheet" href="general_styles.css">


        <script>

        $(document).ready(function () {
            $("#topMenu").load("topMenu.html", function(){
                $("#topNav").append('<li><a href="logout.php">Déconnexion</a></li>');
                $("#topNav").append('<li><a href="upgradeDB.php">Upgrade</a></li>');
            });
            $("#agendaConfig").click(showAgendaParams);
            $("#horairesConfig").click(showHorairesParams);
            $("#addHoraire").click(showNewHoraireParams);
            $('#agendaParamsForm').on('submit', function (event) {
                event.preventDefault(); 
                $.ajax({
                    url: $(this).attr('action'),
                    type: 'post',
                    data: $(this).serialize(),
                    datatype: 'json',
                    success: function ($resMsg) {
                        // if success, HTML response is expected, so replace current
                        $data = JSON.parse($resMsg); 
                        $("#rightDisplayArea").text($data["msg"]);
                        $("#visitesParams").hide();
                        $("#horairesParams").hide();
                        $("#agendaParams").hide();
                        $("#rightDisplayArea").show();

                    }
                }).fail(function (jqXHR, textStatus, errorThrown) {
                    if (jqXHR.status == 0 || jqXHR == 302) {
                        alert('Your session has ended due to inactivity after 10 minutes.\nPlease refresh this page, or close this window and log back in to system.');
                    } else {
                        alert('Unknown error returned while saving' + (typeof errorThrown == 'string' && errorThrown.trim().length > 0 ? ':\n' + errorThrown : ''));
                    }
                });
            });
        });

        function showAgendaParams(){
            $("tr.selected").removeClass('selected');
            $(this).addClass('selected');
            $.ajax({
                url: 'get_agenda_config_params.php', 
                type: 'POST',
                data: {},
                datatype: 'json',
                success: function($resMsg){
                    console.log("msg=" + $resMsg.trim() + "done");
                    $res = JSON.parse($resMsg);             
                    console.log("success, configuration récupérée, statut: " +  $res["statut"] + ", msg: " + $res["message"]["agendaEndTime"]); 
                    if ($res["statut"] == "success"){
                        $("#startTime").html(getAllHours());
                        $("#endTime").html(getAllHours());
                        $("#timeInterval").html(getAllTimeIntervals());
                        $("#startTime").val(addZero($res["message"]["agendaStartTime"]));
                        $("#endTime").val(addZero($res["message"]["agendaEndTime"]));
                        $("#timeInterval").val(addZero($res["message"]["agendaTimeInterval"]));
                        $("#rightDisplayArea").hide();
                        $("#visitesParams").hide();
                        $("#horairesParams").hide();
                        $("#agendaParams").show();

                    }
                    else{
                        alert("Echec lors de la récupération des paramètres de configuration pour l'agenda: " + $res["statut"] + " / " + $res["message"]);
                    }
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    console.log("some sort of error"); 
                    if(thrownError == 'abort' || thrownError == 'undefined') return;
                        alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                }
            });
            
            
        }
        
       
        
        function showHorairesParams(){
            $("tr.selected").removeClass('selected');
            $(this).addClass('selected');                        
            $("#rightDisplayArea").hide();
            $("#agendaParams").hide();
            $("#visitesParams").hide();
            $.ajax({
                url: 'list_tournees.php', 
                type: 'POST',
                data: {},
                datatype: 'json',
                success: function($resMsg){
                    console.log("msg=" + $resMsg.trim() + "done");
                    $res = JSON.parse($resMsg);             
                    if ($res["statut"] == "success"){
                        $("#horaireList").html($res["data"]);
                    }
                    else {
                        $("#horairesParams").text($res["msg"]);
                    }
                    $("#horairesParams").show();
                    $("tr.horaireItem").click(function(){
                        console.log("click : " + $(this).children("td.horaireId").html());
                        showHorairesModal($(this).children("td.horaireId").html());
                    });
                }});
            
        }
        
        function showNewHoraireParams(){
            console.log("before");
            $("#delButton").hide();
            $("#modButton").hide();
            $("#newButton").show();
            $("#modalhoraireId").val(null);
            $("#tabbr").val("");
            $("#ttournee").val("");
            $("#tdesc").val("");
            $("#tstart1").val("00:00");
            $("#tend1").val("00:00");          

            console.log("after");
            $("#horaireModal").modal({
                focus: this,
                show: true
            });
        }
        
        
 
                
        function showHorairesModal($horaireId){
            console.log("show horaires modal" + $horaireId);
            $.ajax({
                url: 'get_horaire_details.php', 
                type: 'POST',
                data: {horaireId : $horaireId},
                datatype: 'json',
                success: function($resMsg){
                    $res = JSON.parse($resMsg);             
                    if ($res["statut"] == "success"){
                        $("#delButton").show();
                        $("#modButton").show();
                        $("#newButton").hide();
                        $("#modalhoraireId").val($horaireId);
                        $("#ttournee").val($res["message"]["tournee"]);
                        $("#tabbr").val($res["message"]["abr"]);
                        $("#tdesc").val($res["message"]["desc"]);
                        $("#tstart1").val($res["message"]["start1"]);
                        $("#tend1").val($res["message"]["end1"]);                      
                        $("#horaireModal").modal({
                            focus: this,
                            show: true
                        });
                    }
                    else {
                        alert("Une erreur est survenue: " + ($res["msg"]));
                    }
                }
            });
            
        }
        
        
        function saveNewTournee() {

            $horaires_visites = "{";
            $first = true;
            $(".visiteTypeCheckbox").each(function(){
                if (!$first){
                    $horaires_visites += ",";
                }
                else {
                    $first = false;
                }
                $horaires_visites += "\"" + $(this).children("input").val() + "\":\"" + $(this).children("input").prop("checked") + "\"";
            });
            $horaires_visites += "}";
            console.log("horaires-visite association: " + $horaires_visites);

            $.ajax({
                url: 'nouvelle_tournee.php', 
                type: 'POST',
                data: {
                    tournee: $("#ttournee").val(),
                    abr: $("#tabbr").val(),
                    desc: $("#tdesc").val(),
                    start1: $("#tstart1").val(),
                    end1: $("#tend1").val()
                },
                datatype: 'json',
                success: function($resMsg){
                    console.log("retour nouvelle horaire");
                    console.log("msg=" + $resMsg.trim() + "done");
                    $res = JSON.parse($resMsg);             
                    console.log("success, horaire crée, statut: " +  $res["statut"] + ", msg: " + $res["message"]); 
                    if ($res["statut"] == "success"){
                        showHorairesParams();
                    }
                    else {
                        alert("Une erreur est survenue lors de la création de la tâche: " + ($res["message"]));
                    }
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    console.log("Une erreur est survenue"); 
                    alert("Une erreur est survenue: " + thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                }
            });
        }
        
        function saveTournee() {
            console.log("modification de la tahce: " + $("#modalhoraireId").val());     

            $horaires_visites = "{";
            $first = true;
            $(".visiteTypeCheckbox").each(function(){
                if (!$first){
                    $horaires_visites += ",";
                }
                else {
                    $first = false;
                }
                $horaires_visites += "\"" + $(this).children("input").val() + "\":\"" + $(this).children("input").prop("checked") + "\"";
            });
            $horaires_visites += "}";
            console.log("horaires-visite association: " + $horaires_visites);

            $.ajax({
                url: 'modif_tournee.php', 
                type: 'POST',
                data: {
                    id: $("#modalhoraireId").val(),
                    tournee: $("#ttournee").val(),
                    abr: $("#tabbr").val(),
                    desc: $("#tdesc").val(),
                    start1: $("#tstart1").val(),
                    end1: $("#tend1").val()
                },
                datatype: 'json',
                success: function($resMsg){
                    console.log("retour modificationd'une tournée");
                    console.log("msg=" + $resMsg.trim() + "done");
                    $res = JSON.parse($resMsg);             
                    if ($res["statut"] == "success"){
                        showHorairesParams();
                    }
                    else {
                        alert("Une erreur est survenue lors de la modification de la tournée: " + ($res["message"]));
                    }
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    console.log("Une erreur est survenue"); 
                    alert("Une erreur est survenue: " + thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                }
            });
        }

        
        function deleteTournee() {
            if (confirm("Etes vous sur de vouloir effacer la tâche?")) {
                $.ajax({
                    url: 'delete_tournee.php', 
                    type: 'POST',
                    data: {horaireId : $("#modalhoraireId").val()},
                    datatype: 'json',
                    success: function($resMsg){
                        $res = JSON.parse($resMsg);             
                        if ($res["statut"] == "success"){
                            showHorairesParams();
                        }
                        else {
                            alert("Une erreur est survenue: " + ($res["msg"]));
                        }
                    },
                    error: function(xhr, ajaxOptions, thrownError) {
                        console.log("Une erreur est survenue"); 
                        alert("Une erreur est survenue: " + thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                    }
                });
            } 
            else {
                return false;
                }
            }
    
        </script>
        <style>
            
                      td.selected {
                background-color: #555;
                color: white;
            }

        </style>
    </head>
    <body>
        <div id="topMenu"></div>

        <div class="container-fluid">
            <div class="row titleBar">
                <div class="col-xs-12">
                    <h4>Configuration</h4>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-4 leftSide">
                    <div id="confSections">
                        <table class = "table">
                            <thead>
                                <tr>
                                    <th>Catégories </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr id = "agendaConfig">
                                    <td>Agenda</td>
                                </tr>    
                                <tr id = "horairesConfig">
                                    <td>Tournées</td>
                                </tr>    
                
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="rightSide col-xs-8"> 
                    <div class="rightSide col-xs-8" id = "rightDisplayArea" hidden> </div>
                    <div id="agendaParams" hidden>
                        <form id="agendaParamsForm" action="save_agenda_params.php" method="post">
                            <div class="form-group">
                            <label for="startTime">Heure de début:</label> 
                            <select name = "startTime" class= "form-control" id= "startTime"></select>
                            </div>
                            <div class="form-group">
                            <label for="endTime">Heure de fin:</label> 
                            <select name = "endTime" class="form-control" id= "endTime"></select>
                            </div>
                            <div class="form-group">
                            <label for="timeInterval">Interval (en min) de découpage des heures:</label> 
                            <select name = "timeInterval" class="form-control" id= "timeInterval"></select>
                            </div>
                            <button type="submit" class="btn btn-default">Enregistrer</button>
                        </form>
                    </div>

                    <div id = "horairesSection">
                        <div id="horairesParams" hidden>     
                            <div>
                            <h3>Liste des tournées et leurs plages horaires
                            <button id="addHoraire" type="button" style="float:right">+</button></h3>
                            </div>
                            <table class = "table">
                                <thead>                                   
                                    <tr>
                                        <th>Tournée</th>
                                        <th>Abbréviation</th>
                                        <th>Description</th>
                                        <th>Début</th>
                                        <th>Fin</th>
                                    </tr>
                                </thead>
                                <tbody id="horaireList"></tbody>
                            </table>
                        </div>
                        
                  
                        
                        <div class="modal fade" id="horaireModal" role="dialog"> 
                            <div class="modal-dialog">
                                <div class="modal-content">

                                    <!-- Modal Header -->
                                    <div class="modal-header">
                                      <h4 class="modal-title">Détail de la tâche
                                      <button type="button" class="close" data-dismiss="modal">&times;</button></h4>
                                    </div>

                                    <!-- Modal body -->
                                    <div class="modal-body">
                                        <input type="hidden" id="modalhoraireId">
                                        <div class="row" id="descHoraire" >
                                            <div class="form-group col-sm-3"> 
                                                <label for="ttournee">Tournée</label>
                                                <input name = "ttournee" type="text" class="form-control" id="ttournee" value = ""> 
                                            </div> 
                                            <div class="form-group col-sm-2"> 
                                                <label for="tabbr">Abbréviation</label>
                                                <input name = "tabbr" type="text" class="form-control" id="tabbr" value = ""> 
                                            </div> 
                                            <div class="form-group col-sm-7"> 
                                                <label for="tdesc">Description</label>
                                                <textarea id="tdesc" type="textarea" name="tdesc" class="form-control"></textarea>
                                            </div> 
                                        </div>
                                       <div class="form-group row">
                                           <h4 style="margin-left: 15px">Plage horaire 1</h4>
                                            <div class="col-xs-6">
                                                <label for="tstart1">Heure de début</label>
                                                <input name = "tstart1" type="time" class="form-control" id="tstart1" value = "09:00">
                                            </div>                                                    
                                            <div class="col-xs-6" >
                                                <label for="tend1">Heure de fin</label>
                                                <input name = "tend1" type="time" class="form-control" id="tend1" value = "18:00">
                                            </div>    
                                        </div>

                                    </div>

                                    <!-- Modal footer -->
                                    <div class="modal-footer">
                                        <button type= "submit" id = "modButton" onclick = "saveTournee()" class="btn btn-success pull-left" data-dismiss="modal"><span class="glyphicon glyphicon-off"></span> Valider</button> 
                                        <button type= "submit" id = "newButton" onclick = "saveNewTournee()" class="btn btn-success pull-right" data-dismiss="modal"><span class="glyphicon glyphicon-off"></span> Valider</button> 
                                        <button type= "submit" id = "delButton" onclick = "deleteTournee()" class="btn btn-danger pull-right" data-dismiss="modal"><span class="glyphicon glyphicon-trash"></span> Supprimer</button>                  
                                    </div>

                                </div>
                            </div>
                        </div>
               
                                       
                    </div>
                </div>
            </div>
        </div>
        <div id="espaceModalMessage"></div>

    </body>
</html>