<?php
session_start();
if(!isset($_SESSION['identifiant']))
{
   echo '<script> location.replace("login.php"); </script>';
}
?>
<html lang="fr">
    <head>
        <meta charset="utf-8"> 
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Visites</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
        <script type="text/javascript" src="boite_outils.js"></script>
        <script type="text/javascript" src="visiteModal.js"></script>
        <script type="text/javascript" src="messagesModal.js"></script>
        <link rel="stylesheet" href="general_styles.css">


        <script>

        $(document).ready(function () {

            $("#topMenu").load("topMenu.html", function(){
                $("#topNav").append('<li><a href="logout.php">Déconnexion</a></li>');
            });
            $("#addClient").click(addClient);	
            showClientList();
            $("#espaceModalModif").html(getModifVisiteModalForm());
            $("#espaceModalParentModif").html(getParentVisiteModalForm());
            $("#espaceModalCreate").html(getNewVisiteModalForm());
            $("#filtreClient").change(function() {
                showClientList();
            });
            $('[data-toggle="tooltip"]').tooltip();   

        });


        function addClient(){
            var res = getNewClientForm();
            console.log("start add client: " + res);
            $("#detailClient").html(res);
            $("#detailVisite").hide();
            $("#detailClient").show();
            $('#newClientForm').on('submit', function (event) {
                event.preventDefault(); // or return false, your choice
                console.log("on submit new client");
                $.ajax({
                    url: $(this).attr('action'),
                    type: 'post',
                    data: $(this).serialize(),
                    success: function (data, textStatus, jqXHR) {
                        // if success, HTML response is expected, so replace current
                        if (textStatus === 'success') {
                            $("#detailClient").text(data);
                            showClientList();
                            }
                        }
                    }).fail(function (jqXHR, textStatus, errorThrown) {
                        if (jqXHR.status == 0 || jqXHR == 302) {
                            alert('Your session has ended due to inactivity after 10 minutes.\nPlease refresh this page, or close this window and log back in to system.');
                            } 
                        else {
                            alert('Unknown error returned while saving' + (typeof errorThrown == 'string' && errorThrown.trim().length > 0 ? ':\n' + errorThrown : ''));
                            }
                        });
                });
            }
    
        function getNewClientForm(){
            var res =   "<form id=\"newClientForm\" action=\"nouveau_client.php\" method=\"post\">" + 
                        "<div class=\"form-group\">" +
                            "<label for=\"nom\">Nom:</label>" +
                            "<input name = \"nom\" type=\"text\" class=\"form-control\" id=\"nom\">" +
                        "</div>" +
                        "<div class=\"form-group\">" +
                            "<label for=\"prénom\">Prénom:</label>" +
                            "<input name = \"prénom\" type=\"text\" class=\"form-control\" id=\"prénom\">" +
                        "</div>" +
                        "<div class=\"form-group\">" +
                            "<label for=\"adresse\">Adresse:</label>" +
                            "<input name = \"adresse\" type=\"text\" class=\"form-control\" id=\"adresse\">" +
                        "</div>" +
                        "<div class=\"form-group\">" +
                            "<label for=\"tel_fixe\">Tel:</label>" +
                            "<input name = \"tel_fixe\" type=\"text\" class=\"form-control\" id=\"tel_fixe\">" +
                        "</div>" +
                        "<button type=\"submit\" class=\"btn btn-default\">Enregistrer</button>" +
                        "</form>";
            return res;
            }
            

            


        function setMissionOptions(defaultVal) {
            $.post('list_missions_ids.php',
                function (data) {  
                    $("#modalMission").html(data);
                    $("#modalMission").val(defaultVal);
                    }
                );
            }

            

            
        function showClientList() {
            console.log("showing clients list");
            $.post('list_clients.php', 
                {filtre: $('#filtreClient').val()},
                function (data) {  
                    $("#clientRows").html(data);
                    $(".client").click(function () { // selects the row data (td)being clicked, and show the content of the mission
                        $("td.selected").removeClass('selected');
                        $(this).parent().parent().addClass('selected');
                        console.log("show client " + $(this).children("span").html());
                        showClient($(this).children("span").html());
                        $("#detailClient").show();
                        $("#detailVisite").hide();
                        });
                    $(".toVisitesButton").click(function(){
                        console.log("to visits pressed");
                        $("td.selected").removeClass('selected');
                        $(this).parent().parent().addClass('selected');
                        $clientId = $(this).siblings("span").html();
                        $("#detailClient").hide();
                        $("#detailVisite").show();
                
                        showClientVisites($clientId);
                        });
                    }
                );
                console.log("end of showing clients list");       
            }
            
            

                            
 
        
        function showClient(input){
	   $.post('show_client.php', 
	        {clientId : input},
                function( data ){   
                    $("#detailClient").html(data); // affiche les données du client dans le formulaire
                    $("#deleteButton").click(function(){
                        if(confirm("Want to delete?")){
                            $.post("delete_client.php",
                                {data: $("#clientId").val()},
                                function(data, status){
                                    if(status === 'success') {
                                        $("#detailClient").text(data);
                                        showClientList();	
                                        }
                                    }).fail(function(jqXHR, textStatus, errorThrown) {
                                        if(jqXHR.status == 0 || jqXHR == 302) {
                                            alert('Your session has ended due to inactivity after 10 minutes.\nPlease refresh this page, or close this window and log back in to system.');
                                            } 
                                        else {
                                            alert('Unknown error returned while saving' + (typeof errorThrown == 'string' && errorThrown.trim().length > 0 ? ':\n' + errorThrown : ''));
                                            }
                                        });
                            } 
                            else {
                                return false;
                                }  
                        });
                    $("input.clientData,select.clientData").change(function(){		    
                        $("#modifButton").removeClass("disabled");
                        $("#modifClientForm").on("submit", function(event) {
                            event.preventDefault(); // or return false, your choice
                            
                            $.ajax({
                                url: $(this).attr('action'),
                                type: 'post',
                                data: $(this).serialize(),
                                success: function(data, textStatus, jqXHR) {
                                // if success, HTML response is expected, so replace current
                                    if(textStatus === 'success') {
                                        $("#detailClient").text("client modifié avec succès");
                                        showClientList();	
                                        }
                                    }
                                }).fail(function(jqXHR, textStatus, errorThrown) {
                                    if(jqXHR.status == 0 || jqXHR == 302) {
                                        alert('Your session has ended due to inactivity after 10 minutes.\nPlease refresh this page, or close this window and log back in to system.');
                                        } 
                                    else {
                                        alert('Unknown error returned while saving' + (typeof errorThrown == 'string' && errorThrown.trim().length > 0 ? ':\n' + errorThrown : ''));
                                        }
                                    });
                            if ($("#statut").val() == "inactif"){
                                if (confirm("Le statut du client est passé à inactif, toutes les visites futures le concernant vont être effacées. Confirmez-vous?")){
                                    var currentDate = new Date();
                                    var month = addZero(currentDate.getMonth() + 1);
                                    var day = addZero(currentDate.getDate());
                                    var dateString = currentDate.getFullYear() + "-" + month + "-" + day;
                                    $.post("delete_future_visites.php",
                                        {'clientId': $("#clientId").val(),
                                         'date' : dateString},
                                        function ($resMsg) {
                                            $res = JSON.parse($resMsg);             
                                            if ($res["statut"] == "success"){
                                                $("#detailClient").append($res["msg"]);
                                            }
                                            else {
                                                alert($res["message"]);
                                            }
                                        }
                                    );
                                }
                            }
                            
                            });
                        });
                    }
                );
            }


        function showClientVisites(clientId) {
             console.log("in showClientVisites");
           
            $.post('list_visites.php', // location of your php script
                {'clientId': clientId},
                function (data) {  // a function to deal with the returned information
                    $.ajax({
                        url: 'get_agenda_config_params.php', 
                        type: 'POST',
                        data: {fromsession: ""},
                        dataType: 'JSON',
                        success: function(agendaTimeData) {
                            var heureDebut = Number(agendaTimeData["agendaStartTime"]);
                            var heureFin = Number(agendaTimeData["agendaEndTime"]);
                            var interval = Number(agendaTimeData["agendaTimeInterval"]);

                            $("#visiteRows").html(data);
                            $(".visite").click(function () {
                                var visitId = $(this).find("span").html();
                                modifyRdv(visitId, "Planning", interval);
                                });
                            $(".parentVisite").click(function () {
                                var visitId = $(this).find("span").html();
                                modifyParentRdv(visitId, interval);
                                });
                            $("#addVisiteButton").click(function(){
                                addVisite(interval);
                                });
                        }
                    });
                    
                });
            }

         


        </script>
        <style>

            td.selected {
                background-color: #555;
                color: white;
            }
            
            .expandButton {
                color: black;
                margin-right: 5px;
            }
            .tabbedVisite { 
                display:inline-block; 
                margin-left: 20px; 
            }
            
 

        </style>
    </head>
    <body>
        <div id="topMenu"></div>
        <div class="container-fluid">
            <div class="row titleBar">
                <div class="col-xs-12">
                    <h4>Gestion des clients et visites</h4>
                </div>
            </div>
  
            <div class="row">
                <div class="col-xs-4 leftSide">
                    <div id="ClientList">
                        <table class = "table">
                            <thead>
                                <tr>
                                    <th>Clients 
                                        <select id="filtreClient">
                                            <option value="actif">actifs</option>
                                            <option value="inactif">inactifs</option>
                                            <option value="tous">tous</option>
                                        </select>
                                        <button id="addClient" type="button" style="float:right">+</button>
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="clientRows"></tbody>
                        </table>
                    </div>
                </div>
                <!-- only show one of the 2 folowing divs -->
                <div hidden class="rightSide col-xs-8" id="detailClient">  </div>
                <div hidden class="rightSide col-xs-8" id="detailVisite">  
                    <div class="row">
                        <div class="col-xs-11">
                            <h4>Visites</h4>
                        </div>
                        <div class="col-xs-1">
                            <button id="addVisiteButton" type="button" style="float:right">+</button>
                        </div>
                    </div>
                    <div id="visiteRows"></div>
                </div>
            </div>
        </div>

        <div id="espaceModalModif"></div>
        <div id="espaceModalParentModif"></div>
        <div id="espaceModalCreate"></div>
     
        <div id="espaceMessages">
        </div>
        <div id="espaceModalMessage"></div>
    </body>
</html>